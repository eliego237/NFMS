<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateSqliteToPgsql extends Command
{
    protected $signature = 'db:migrate-sqlite-to-pgsql
        {--dry-run : Compte les lignes sans ecrire dans Postgres}
        {--only= : Ne migrer qu\'une table precise (debug)}';

    protected $description = 'Transfere les donnees de database.sqlite vers la connexion pgsql, dans l\'ordre des dependances FK.';

    protected array $tables = [
        'users',
        'roles',
        'permissions',
        'model_has_roles',
        'model_has_permissions',
        'role_has_permissions',
        'payment_methods',
        'students',
        'trainings',
        'training_modules',
        'enrollments',
        'payments',
        'expenses',
        'cash_transactions',
        'settings',
        'activity_log',
    ];

    public function handle(): int
    {
        $only = $this->option('only');
        $dryRun = $this->option('dry-run');
        $tables = $only ? [$only] : $this->tables;

        if (!$dryRun) {
            $this->warn('Ceci va TRUNCATE puis reecrire les tables listees sur la connexion pgsql.');
            if (!$this->confirm('Continuer ?')) {
                return self::FAILURE;
            }
        }

        if (!$dryRun) {
            DB::connection('pgsql')->statement('SET session_replication_role = replica;');
        }

        $summary = [];

        foreach ($tables as $table) {
            if (!Schema::connection('sqlite_legacy')->hasTable($table)) {
                $this->line("skip {$table} (absente du SQLite legacy)");
                continue;
            }

            $sourceCount = DB::connection('sqlite_legacy')->table($table)->count();

            if ($dryRun) {
                $this->line("{$table}: {$sourceCount} lignes (source)");
                $summary[$table] = $sourceCount;
                continue;
            }

            $this->info("Migration de {$table} ({$sourceCount} lignes)...");

            DB::connection('pgsql')->table($table)->truncate();

            $inserted = 0;
            DB::connection('sqlite_legacy')->table($table)->orderBy('id')
                ->chunk(500, function ($rows) use ($table, &$inserted) {
                    $payload = $rows->map(fn ($row) => (array) $row)->all();
                    if (!empty($payload)) {
                        DB::connection('pgsql')->table($table)->insert($payload);
                        $inserted += count($payload);
                    }
                });

            $targetCount = DB::connection('pgsql')->table($table)->count();
            $summary[$table] = [$sourceCount, $targetCount];

            if ($sourceCount !== $targetCount) {
                $this->error("  MISMATCH {$table}: source={$sourceCount} vs cible={$targetCount}");
            } else {
                $this->line("  OK {$targetCount} lignes transferees");
            }
        }

        if (!$dryRun) {
            DB::connection('pgsql')->statement('SET session_replication_role = DEFAULT;');
            $this->resyncSequences($tables);
        }

        $this->newLine();
        $this->table(['Table', 'Source', 'Cible'], collect($summary)->map(function ($v, $k) {
            return is_array($v) ? [$k, $v[0], $v[1]] : [$k, $v, '-'];
        })->toArray());

        return self::SUCCESS;
    }

    protected function resyncSequences(array $tables): void
    {
        $this->info('Resynchronisation des sequences Postgres...');

        foreach ($tables as $table) {
            if (!Schema::connection('pgsql')->hasColumn($table, 'id')) {
                continue;
            }

            DB::connection('pgsql')->statement("
                SELECT setval(
                    pg_get_serial_sequence('{$table}', 'id'),
                    COALESCE((SELECT MAX(id) FROM {$table}), 1)
                )
            ");
        }

        $this->line('  Sequences resynchronisees');
    }
}

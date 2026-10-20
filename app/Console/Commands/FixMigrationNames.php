<?php
// À placer dans app/Console/Commands/FixMigrationNames.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixMigrationNames extends Command
{
    protected $signature = 'db:fix-migration-names';
    protected $description = 'Renomme les entrées de migrations désynchronisées entre le nom en base et le nom de fichier actuel.';

    protected array $renames = [
        '2026_07_04_173746_create_payment_methods_table' => '2026_07_04_173747_create_payment_methods_table',
        '2026_07_04_173748_create_payments_table' => '2026_07_04_173749_create_payments_table',
    ];

    public function handle(): int
    {
        foreach ($this->renames as $old => $new) {
            $row = DB::table('migrations')->where('migration', $old)->first();

            if (!$row) {
                $this->warn("Introuvable : {$old} (déjà corrigée ou jamais existante)");
                continue;
            }

            $updated = DB::table('migrations')
                ->where('migration', $old)
                ->update(['migration' => $new]);

            $this->info("Renommé ({$updated} ligne) : {$old} -> {$new}");
        }

        $this->newLine();
        $this->info('État actuel de la table migrations (filtré payment) :');
        $rows = DB::table('migrations')->where('migration', 'like', '%payment%')->get();
        foreach ($rows as $r) {
            $this->line("  id={$r->id}  batch={$r->batch}  {$r->migration}");
        }

        return self::SUCCESS;
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {

            // Informations académiques
            $table->string('level')->nullable()->after('emergency_contact');
            $table->string('program')->nullable()->after('level');
            $table->string('academic_year')->nullable()->after('program');
            $table->date('registration_date')->nullable()->after('academic_year');

            // Informations financières
            $table->decimal('tuition_fee', 12, 2)->default(0)->after('registration_date');
            $table->decimal('amount_paid', 12, 2)->default(0)->after('tuition_fee');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {

            $table->dropColumn([
                'level',
                'program',
                'academic_year',
                'registration_date',
                'tuition_fee',
                'amount_paid',
            ]);

        });
    }
};
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
        Schema::table('enrollments', function (Blueprint $table) {

            $table->decimal('catalog_price', 12, 2)
                ->default(0)
                ->after('training_id');

            $table->decimal('discount_amount', 12, 2)
                ->default(0)
                ->after('catalog_price');

            $table->decimal('final_price', 12, 2)
                ->default(0)
                ->after('discount_amount');

            $table->string('discount_reason')
                ->nullable()
                ->after('final_price');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {

            $table->dropColumn([
                'catalog_price',
                'discount_amount',
                'final_price',
                'discount_reason',
            ]);

        });
    }
};
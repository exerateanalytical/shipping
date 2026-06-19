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
        Schema::create('coa_records', function (Blueprint $table) {
            $table->id();
            $table->string('lot_number', 50)->unique();
            $table->string('product_name', 150)->default('Tirzepatide');
            $table->string('cas_number', 50)->default('2023788-19-2');
            $table->string('molecular_formula', 50)->default('C225H348N48O68');
            $table->decimal('molecular_weight', 10, 4)->default(4813.4600);
            $table->string('manufacturer', 100)->default('Apex Laboratories');
            $table->string('catalog_number', 50)->nullable();
            $table->date('manufacture_date');
            $table->date('expiry_date');
            $table->date('analysis_date');
            // Test results
            $table->decimal('purity_hplc', 5, 2)->default(99.99);
            $table->string('appearance', 150)->default('White to off-white lyophilized powder');
            $table->string('solubility', 150)->default('Soluble in water (1 mg/mL)');
            $table->string('moisture_content', 50)->default('≤ 5.0%');
            $table->string('ph', 20)->default('6.0 - 7.0');
            $table->string('endotoxin', 100)->default('< 1 EU/mg');
            $table->string('sterility', 100)->default('Sterile');
            $table->string('identity_ms', 100)->default('Confirmed');
            $table->string('identity_hplc', 100)->default('Confirmed');
            $table->string('storage_conditions', 150)->default('-20°C, desiccated, protected from light');
            $table->string('grade', 50)->default('Research Grade');
            $table->string('quantity', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa_records');
    }
};

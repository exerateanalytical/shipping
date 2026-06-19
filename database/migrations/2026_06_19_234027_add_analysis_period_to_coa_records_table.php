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
        Schema::table('coa_records', function (Blueprint $table) {
            $table->date('analysis_start_date')->nullable()->after('analysis_date');
        });
    }

    public function down(): void
    {
        Schema::table('coa_records', function (Blueprint $table) {
            $table->dropColumn('analysis_start_date');
        });
    }
};

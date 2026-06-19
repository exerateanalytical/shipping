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
            $table->string('recipient_name', 150)->nullable()->after('quantity');
            $table->string('recipient_email', 150)->nullable()->after('recipient_name');
        });
    }

    public function down(): void
    {
        Schema::table('coa_records', function (Blueprint $table) {
            $table->dropColumn(['recipient_name', 'recipient_email']);
        });
    }
};

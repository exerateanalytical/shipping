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
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('status', 30)->default('pending')->after('waybill_number');
            $table->decimal('goods_value', 12, 2)->nullable()->after('declared_value');
            $table->decimal('insurance_fee', 12, 2)->nullable()->after('goods_value');
            $table->boolean('insurance_refundable')->default(true)->after('insurance_fee');
            $table->decimal('customs_duties', 12, 2)->nullable()->after('insurance_refundable');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['status', 'goods_value', 'insurance_fee', 'insurance_refundable', 'customs_duties']);
        });
    }
};

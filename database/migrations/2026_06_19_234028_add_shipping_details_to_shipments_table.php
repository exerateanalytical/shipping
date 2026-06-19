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
            $table->decimal('shipping_fee', 10, 2)->nullable()->after('currency');
            $table->string('shipping_fee_currency', 3)->default('EUR')->after('shipping_fee');
            $table->date('estimated_arrival')->nullable()->after('shipping_fee_currency');
            $table->boolean('is_sensitive')->default(false)->after('estimated_arrival');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['shipping_fee', 'shipping_fee_currency', 'estimated_arrival', 'is_sensitive']);
        });
    }
};

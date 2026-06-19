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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('waybill_number', 20)->unique();
            $table->string('service_type', 50)->default('EXPRESS WORLDWIDE');
            $table->string('product_code', 10)->default('P');
            // Shipper
            $table->string('shipper_name', 100);
            $table->string('shipper_company', 100)->nullable();
            $table->string('shipper_address1', 150);
            $table->string('shipper_address2', 150)->nullable();
            $table->string('shipper_city', 100);
            $table->string('shipper_state', 100)->nullable();
            $table->string('shipper_postal', 20);
            $table->string('shipper_country', 3);
            $table->string('shipper_phone', 30)->nullable();
            // Receiver
            $table->string('receiver_name', 100);
            $table->string('receiver_company', 100)->nullable();
            $table->string('receiver_address1', 150);
            $table->string('receiver_address2', 150)->nullable();
            $table->string('receiver_city', 100);
            $table->string('receiver_state', 100)->nullable();
            $table->string('receiver_postal', 20);
            $table->string('receiver_country', 3);
            $table->string('receiver_phone', 30)->nullable();
            // Package
            $table->decimal('weight_kg', 8, 2)->default(1.00);
            $table->string('dimensions', 50)->nullable();
            $table->integer('pieces')->default(1);
            $table->string('content_description', 255)->nullable();
            $table->decimal('declared_value', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            // Routing
            $table->string('origin_service_area', 10)->nullable();
            $table->string('dest_service_area', 10)->nullable();
            $table->string('routing_code', 50)->nullable();
            $table->date('ship_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

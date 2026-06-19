<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Shipment::truncate();

        \App\Models\Shipment::create([
            'waybill_number'      => '7251987634',
            'service_type'        => 'EXPRESS WORLDWIDE',
            'product_code'        => 'P',
            // Shipper
            'shipper_name'        => 'Sean West',
            'shipper_company'     => null,
            'shipper_address1'    => '123 Sender Street',
            'shipper_address2'    => null,
            'shipper_city'        => 'Miami',
            'shipper_state'       => 'FL',
            'shipper_postal'      => '33101',
            'shipper_country'     => 'US',
            'shipper_phone'       => null,
            // Receiver
            'receiver_name'       => 'Joao Silva',
            'receiver_company'    => 'DHL Servicepoint 101',
            'receiver_address1'   => 'R. Arco do Carvalhão 3A',
            'receiver_address2'   => null,
            'receiver_city'       => 'Lisboa',
            'receiver_state'      => null,
            'receiver_postal'     => '1070-008',
            'receiver_country'    => 'PT',
            'receiver_phone'      => null,
            // Package — 20 grams
            'weight_kg'              => 0.020,
            'dimensions'             => null,
            'pieces'                 => 1,
            'content_description'    => 'Tirzepatide Peptide',
            'declared_value'         => null,
            'currency'               => 'EUR',
            'shipping_fee'           => 200.00,
            'shipping_fee_currency'  => 'EUR',
            'estimated_arrival'      => '2026-06-27',
            'is_sensitive'           => true,
            'origin_service_area'    => 'MIA',
            'dest_service_area'      => 'LIS',
            'routing_code'           => 'MIA-LIS-LIS',
            'ship_date'              => '2026-06-19',
        ]);
    }
}

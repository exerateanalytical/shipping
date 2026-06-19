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
        \App\Models\Shipment::create([
            'waybill_number'     => '1234567890',
            'service_type'       => 'EXPRESS WORLDWIDE',
            'product_code'       => 'P',
            'shipper_name'       => 'John Smith',
            'shipper_company'    => 'Acme Corp',
            'shipper_address1'   => '123 Business Park Drive',
            'shipper_address2'   => 'Suite 400',
            'shipper_city'       => 'New York',
            'shipper_state'      => 'NY',
            'shipper_postal'     => '10001',
            'shipper_country'    => 'US',
            'shipper_phone'      => '+1 212 555 0100',
            'receiver_name'      => 'Jane Doe',
            'receiver_company'   => 'Global Imports Ltd',
            'receiver_address1'  => '45 Oxford Street',
            'receiver_address2'  => 'Floor 2',
            'receiver_city'      => 'London',
            'receiver_state'     => 'England',
            'receiver_postal'    => 'W1D 2DZ',
            'receiver_country'   => 'GB',
            'receiver_phone'     => '+44 20 7946 0958',
            'weight_kg'          => 2.50,
            'dimensions'         => '30 x 20 x 15 cm',
            'pieces'             => 1,
            'content_description'=> 'Commercial Documents',
            'declared_value'     => 150.00,
            'currency'           => 'USD',
            'origin_service_area'=> 'JFK',
            'dest_service_area'  => 'LHR',
            'routing_code'       => 'JFK-LHR-LHR',
            'ship_date'          => now()->toDateString(),
        ]);
    }
}

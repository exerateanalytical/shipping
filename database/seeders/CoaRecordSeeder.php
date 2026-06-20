<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoaRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\CoaRecord::truncate();

        \App\Models\CoaRecord::create([
            'lot_number'         => 'APX-TZP-2024-061',
            'product_name'       => 'Tirzepatide',
            'cas_number'         => '2023788-19-2',
            'molecular_formula'  => 'C₂₂₅H₃₄₈N₄₈O₆₈',
            'molecular_weight'   => 4813.4600,
            'manufacturer'       => 'Apex Laboratories',
            'catalog_number'     => 'APX-TZP-20MG',
            'manufacture_date'     => '2026-01-15',
            'expiry_date'         => '2028-01-14',
            'analysis_start_date' => '2026-06-17',
            'analysis_date'       => '2026-06-19',
            'purity_hplc'        => 99.99,
            'appearance'         => 'White to off-white lyophilized powder',
            'solubility'         => 'Soluble in water at 1 mg/mL',
            'moisture_content'   => '≤ 5.0%',
            'ph'                 => '6.0 - 7.0',
            'endotoxin'          => '< 1.0 EU/mg',
            'sterility'          => 'Sterile – Passes USP <71>',
            'identity_ms'        => 'Confirmed by LC-MS/MS',
            'identity_hplc'      => 'Confirmed by RP-HPLC',
            'storage_conditions' => '-20°C, desiccated, protected from light',
            'grade'              => 'Research Grade',
            'quantity'           => '20 mg',
            'recipient_name'     => 'Joao Silva',
            'recipient_email'    => 'joao.cristovao.silva@proton.me',
            'notes'              => 'Consult SDS (Safety Data Sheet) document APX-SDS-TZP-001 before handling.',
        ]);
    }
}

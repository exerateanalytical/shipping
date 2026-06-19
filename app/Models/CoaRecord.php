<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoaRecord extends Model
{
    protected $fillable = [
        'lot_number', 'product_name', 'cas_number', 'molecular_formula', 'molecular_weight',
        'manufacturer', 'catalog_number', 'manufacture_date', 'expiry_date', 'analysis_date',
        'purity_hplc', 'appearance', 'solubility', 'moisture_content', 'ph',
        'endotoxin', 'sterility', 'identity_ms', 'identity_hplc',
        'storage_conditions', 'grade', 'quantity', 'recipient_name', 'recipient_email', 'notes',
        'analysis_start_date',
    ];

    protected $casts = [
        'manufacture_date'      => 'date',
        'expiry_date'          => 'date',
        'analysis_date'        => 'date',
        'analysis_start_date'  => 'date',
        'purity_hplc' => 'decimal:2',
        'molecular_weight' => 'decimal:4',
    ];
}

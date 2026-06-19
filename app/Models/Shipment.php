<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'waybill_number', 'status', 'service_type', 'product_code',
        'shipper_name', 'shipper_company', 'shipper_address1', 'shipper_address2',
        'shipper_city', 'shipper_state', 'shipper_postal', 'shipper_country', 'shipper_phone',
        'receiver_name', 'receiver_company', 'receiver_address1', 'receiver_address2',
        'receiver_city', 'receiver_state', 'receiver_postal', 'receiver_country', 'receiver_phone',
        'weight_kg', 'dimensions', 'pieces', 'content_description', 'declared_value', 'currency',
        'goods_value', 'insurance_fee', 'insurance_refundable', 'customs_duties',
        'shipping_fee', 'shipping_fee_currency', 'estimated_arrival', 'is_sensitive',
        'origin_service_area', 'dest_service_area', 'routing_code', 'ship_date',
    ];

    protected $casts = [
        'ship_date'             => 'date',
        'estimated_arrival'     => 'date',
        'is_sensitive'          => 'boolean',
        'insurance_refundable'  => 'boolean',
        'weight_kg'             => 'decimal:2',
        'declared_value'        => 'decimal:2',
        'goods_value'           => 'decimal:2',
        'insurance_fee'         => 'decimal:2',
        'customs_duties'        => 'decimal:2',
        'shipping_fee'          => 'decimal:2',
    ];
}

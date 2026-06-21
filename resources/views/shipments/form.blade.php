@extends('layouts.app')
@section('title', $editing ? 'Edit Shipment' : 'New Shipment')
@section('content')
<style>
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px 24px; }
.form-grid.cols3 { grid-template-columns:1fr 1fr 1fr; }
.form-group { display:flex; flex-direction:column; gap:4px; }
.form-group label { font-size:11px; font-weight:700; text-transform:uppercase; color:#666; letter-spacing:.5px; }
.form-group input, .form-group select { border:1.5px solid #ddd; border-radius:4px; padding:8px 10px; font-size:14px; width:100%; transition:border-color .15s; }
.form-group input:focus, .form-group select:focus { outline:none; border-color:#D40511; }
.form-group input.error, .form-group select.error { border-color:#D40511; background:#fff5f5; }
.err-msg { font-size:11px; color:#D40511; margin-top:2px; }
.section-hdr { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#fff; background:#D40511; padding:6px 12px; border-radius:4px; margin:20px 0 12px; }
.section-hdr.dark { background:#1a1a1a; }
.section-hdr.yellow { background:#FFCC00; color:#333; }
.toggle-row { display:flex; gap:24px; align-items:center; flex-wrap:wrap; margin:8px 0; }
.toggle-label { display:flex; align-items:center; gap:8px; font-size:14px; font-weight:500; cursor:pointer; }
.toggle-label input[type=checkbox] { width:16px; height:16px; accent-color:#D40511; }
.form-actions { display:flex; gap:12px; margin-top:24px; align-items:center; }
@media(max-width:640px){.form-grid,.form-grid.cols3{grid-template-columns:1fr;}}
</style>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h1 style="color:#D40511;">{{ $editing ? 'Edit Shipment' : 'New Shipment' }}</h1>
        <a href="{{ route('shipments.index') }}" class="btn btn-outline">&larr; Back</a>
    </div>

    @if($errors->any())
    <div style="background:#fff5f5;border:1.5px solid #D40511;border-radius:4px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#D40511;">
        Please fix the highlighted fields below.
    </div>
    @endif

    <form method="POST" action="{{ $editing ? route('shipments.update', $shipment->id) : route('shipments.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif

        {{-- ── SHIPMENT INFO ── --}}
        <div class="section-hdr">Shipment Info</div>
        <div class="form-grid cols3">
            <div class="form-group">
                <label>Waybill Number *</label>
                <input type="text" name="waybill_number" value="{{ old('waybill_number', $shipment->waybill_number) }}" class="{{ $errors->has('waybill_number') ? 'error' : '' }}" required>
                @error('waybill_number')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Service Type *</label>
                <input type="text" name="service_type" value="{{ old('service_type', $shipment->service_type) }}" class="{{ $errors->has('service_type') ? 'error' : '' }}" placeholder="EXPRESS WORLDWIDE" required>
                @error('service_type')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Product Code *</label>
                <input type="text" name="product_code" value="{{ old('product_code', $shipment->product_code) }}" class="{{ $errors->has('product_code') ? 'error' : '' }}" placeholder="P" required>
                @error('product_code')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Status *</label>
                <select name="status" class="{{ $errors->has('status') ? 'error' : '' }}">
                    @foreach(['pending','in_transit','delivered','cancelled'] as $s)
                    <option value="{{ $s }}" {{ old('status', $shipment->status) === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
                @error('status')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Ship Date *</label>
                <input type="date" name="ship_date" value="{{ old('ship_date', $shipment->ship_date?->format('Y-m-d')) }}" class="{{ $errors->has('ship_date') ? 'error' : '' }}" required>
                @error('ship_date')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Est. Delivery</label>
                <input type="date" name="estimated_arrival" value="{{ old('estimated_arrival', $shipment->estimated_arrival?->format('Y-m-d')) }}" class="{{ $errors->has('estimated_arrival') ? 'error' : '' }}">
                @error('estimated_arrival')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- ── ROUTING ── --}}
        <div class="section-hdr yellow">Routing</div>
        <div class="form-grid cols3">
            <div class="form-group">
                <label>Origin Service Area</label>
                <input type="text" name="origin_service_area" value="{{ old('origin_service_area', $shipment->origin_service_area) }}" placeholder="MIA">
                @error('origin_service_area')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Dest. Service Area</label>
                <input type="text" name="dest_service_area" value="{{ old('dest_service_area', $shipment->dest_service_area) }}" placeholder="LIS">
                @error('dest_service_area')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Routing Code</label>
                <input type="text" name="routing_code" value="{{ old('routing_code', $shipment->routing_code) }}" placeholder="MIA-LIS-LIS">
                @error('routing_code')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- ── SHIPPER ── --}}
        <div class="section-hdr dark">Shipper</div>
        <div class="form-grid">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="shipper_name" value="{{ old('shipper_name', $shipment->shipper_name) }}" class="{{ $errors->has('shipper_name') ? 'error' : '' }}" required>
                @error('shipper_name')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Company</label>
                <input type="text" name="shipper_company" value="{{ old('shipper_company', $shipment->shipper_company) }}">
            </div>
            <div class="form-group">
                <label>Address Line 1 *</label>
                <input type="text" name="shipper_address1" value="{{ old('shipper_address1', $shipment->shipper_address1) }}" class="{{ $errors->has('shipper_address1') ? 'error' : '' }}" required>
                @error('shipper_address1')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Address Line 2</label>
                <input type="text" name="shipper_address2" value="{{ old('shipper_address2', $shipment->shipper_address2) }}">
            </div>
            <div class="form-group">
                <label>City *</label>
                <input type="text" name="shipper_city" value="{{ old('shipper_city', $shipment->shipper_city) }}" class="{{ $errors->has('shipper_city') ? 'error' : '' }}" required>
                @error('shipper_city')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>State / Province</label>
                <input type="text" name="shipper_state" value="{{ old('shipper_state', $shipment->shipper_state) }}" placeholder="FL">
            </div>
            <div class="form-group">
                <label>Postal Code *</label>
                <input type="text" name="shipper_postal" value="{{ old('shipper_postal', $shipment->shipper_postal) }}" class="{{ $errors->has('shipper_postal') ? 'error' : '' }}" required>
                @error('shipper_postal')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Country Code *</label>
                <input type="text" name="shipper_country" value="{{ old('shipper_country', $shipment->shipper_country) }}" class="{{ $errors->has('shipper_country') ? 'error' : '' }}" placeholder="US" maxlength="10" required>
                @error('shipper_country')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="shipper_phone" value="{{ old('shipper_phone', $shipment->shipper_phone) }}">
            </div>
        </div>

        {{-- ── CONSIGNEE ── --}}
        <div class="section-hdr dark">Consignee (Receiver)</div>
        <div class="form-grid">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="receiver_name" value="{{ old('receiver_name', $shipment->receiver_name) }}" class="{{ $errors->has('receiver_name') ? 'error' : '' }}" required>
                @error('receiver_name')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Company / Pickup Point</label>
                <input type="text" name="receiver_company" value="{{ old('receiver_company', $shipment->receiver_company) }}">
            </div>
            <div class="form-group">
                <label>Address Line 1 *</label>
                <input type="text" name="receiver_address1" value="{{ old('receiver_address1', $shipment->receiver_address1) }}" class="{{ $errors->has('receiver_address1') ? 'error' : '' }}" required>
                @error('receiver_address1')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Address Line 2</label>
                <input type="text" name="receiver_address2" value="{{ old('receiver_address2', $shipment->receiver_address2) }}">
            </div>
            <div class="form-group">
                <label>City *</label>
                <input type="text" name="receiver_city" value="{{ old('receiver_city', $shipment->receiver_city) }}" class="{{ $errors->has('receiver_city') ? 'error' : '' }}" required>
                @error('receiver_city')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>State / Province</label>
                <input type="text" name="receiver_state" value="{{ old('receiver_state', $shipment->receiver_state) }}">
            </div>
            <div class="form-group">
                <label>Postal Code *</label>
                <input type="text" name="receiver_postal" value="{{ old('receiver_postal', $shipment->receiver_postal) }}" class="{{ $errors->has('receiver_postal') ? 'error' : '' }}" required>
                @error('receiver_postal')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Country Code *</label>
                <input type="text" name="receiver_country" value="{{ old('receiver_country', $shipment->receiver_country) }}" class="{{ $errors->has('receiver_country') ? 'error' : '' }}" placeholder="PT" maxlength="10" required>
                @error('receiver_country')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="receiver_phone" value="{{ old('receiver_phone', $shipment->receiver_phone) }}">
            </div>
        </div>

        {{-- ── PACKAGE ── --}}
        <div class="section-hdr yellow">Package Details</div>
        <div class="form-grid cols3">
            <div class="form-group">
                <label>Weight (kg) *</label>
                <input type="number" step="0.001" name="weight_kg" value="{{ old('weight_kg', $shipment->weight_kg) }}" class="{{ $errors->has('weight_kg') ? 'error' : '' }}" required>
                @error('weight_kg')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Dimensions</label>
                <input type="text" name="dimensions" value="{{ old('dimensions', $shipment->dimensions) }}" placeholder="e.g. 20×15×10 cm">
            </div>
            <div class="form-group">
                <label>Pieces *</label>
                <input type="number" name="pieces" value="{{ old('pieces', $shipment->pieces ?? 1) }}" min="1" class="{{ $errors->has('pieces') ? 'error' : '' }}" required>
                @error('pieces')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group" style="grid-column:span 2;">
                <label>Content Description *</label>
                <input type="text" name="content_description" value="{{ old('content_description', $shipment->content_description) }}" class="{{ $errors->has('content_description') ? 'error' : '' }}" required>
                @error('content_description')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Currency *</label>
                <input type="text" name="currency" value="{{ old('currency', $shipment->currency ?? 'EUR') }}" maxlength="10" class="{{ $errors->has('currency') ? 'error' : '' }}" required>
                @error('currency')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="toggle-row" style="margin:10px 0 4px;">
            <label class="toggle-label">
                <input type="checkbox" name="is_sensitive" value="1" {{ old('is_sensitive', $shipment->is_sensitive) ? 'checked' : '' }}>
                Sensitive Shipment (shows red warning strip)
            </label>
        </div>

        {{-- ── FINANCIALS ── --}}
        <div class="section-hdr">Charges & Financials</div>
        <div class="form-grid cols3">
            <div class="form-group">
                <label>Declared Goods Value</label>
                <input type="number" step="0.01" name="goods_value" value="{{ old('goods_value', $shipment->goods_value) }}" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Shipping Fee</label>
                <input type="number" step="0.01" name="shipping_fee" value="{{ old('shipping_fee', $shipment->shipping_fee) }}" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Shipping Currency</label>
                <input type="text" name="shipping_fee_currency" value="{{ old('shipping_fee_currency', $shipment->shipping_fee_currency ?? 'EUR') }}" maxlength="10">
            </div>
            <div class="form-group">
                <label>Insurance Fee</label>
                <input type="number" step="0.01" name="insurance_fee" value="{{ old('insurance_fee', $shipment->insurance_fee) }}" placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Customs Duties</label>
                <input type="number" step="0.01" name="customs_duties" value="{{ old('customs_duties', $shipment->customs_duties) }}" placeholder="0.00">
            </div>
        </div>
        <div class="toggle-row">
            <label class="toggle-label">
                <input type="checkbox" name="insurance_refundable" value="1" {{ old('insurance_refundable', $shipment->insurance_refundable) ? 'checked' : '' }}>
                Insurance refundable on delivery
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-red" style="padding:10px 28px;font-size:15px;">
                {{ $editing ? 'Save Changes' : 'Create Shipment' }}
            </button>
            @if($editing)
            <a href="{{ route('dhl.preview', $shipment->id) }}" class="btn btn-outline" target="_blank">Preview Label</a>
            <a href="{{ route('dhl.download', $shipment->id) }}" class="btn btn-yellow">Download PDF</a>
            @endif
            <a href="{{ route('shipments.index') }}" style="color:#888;font-size:14px;margin-left:8px;">Cancel</a>
        </div>
    </form>
</div>
@endsection

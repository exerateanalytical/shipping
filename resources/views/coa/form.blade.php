@extends('layouts.app')
@section('title', $editing ? 'Edit COA Record' : 'New COA Record')
@section('content')
<style>
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px 24px; }
.form-grid.cols3 { grid-template-columns:1fr 1fr 1fr; }
.form-group { display:flex; flex-direction:column; gap:4px; }
.form-group label { font-size:11px; font-weight:700; text-transform:uppercase; color:#666; letter-spacing:.5px; }
.form-group input, .form-group select, .form-group textarea { border:1.5px solid #ddd; border-radius:4px; padding:8px 10px; font-size:14px; width:100%; transition:border-color .15s; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline:none; border-color:#17b8b4; }
.form-group input.error { border-color:#D40511; background:#fff5f5; }
.err-msg { font-size:11px; color:#D40511; margin-top:2px; }
.section-hdr { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#fff; background:#17b8b4; padding:6px 12px; border-radius:4px; margin:20px 0 12px; }
.section-hdr.dark { background:#2c3340; }
.form-actions { display:flex; gap:12px; margin-top:24px; align-items:center; }
@media(max-width:640px){.form-grid,.form-grid.cols3{grid-template-columns:1fr;}}
</style>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h1 style="color:#17b8b4;">{{ $editing ? 'Edit COA Record' : 'New COA Record' }}</h1>
        <a href="{{ route('coa.index') }}" class="btn btn-outline" style="color:#17b8b4;border-color:#17b8b4;">&larr; Back</a>
    </div>

    @if($errors->any())
    <div style="background:#fff5f5;border:1.5px solid #D40511;border-radius:4px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#D40511;">
        Please fix the highlighted fields below.
    </div>
    @endif

    <form method="POST" action="{{ $editing ? route('coa.update', $coa->id) : route('coa.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif

        {{-- ── IDENTIFICATION ── --}}
        <div class="section-hdr">Product Identification</div>
        <div class="form-grid cols3">
            <div class="form-group">
                <label>Lot / Batch Number *</label>
                <input type="text" name="lot_number" value="{{ old('lot_number', $coa->lot_number) }}" class="{{ $errors->has('lot_number') ? 'error' : '' }}" required>
                @error('lot_number')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="product_name" value="{{ old('product_name', $coa->product_name) }}" class="{{ $errors->has('product_name') ? 'error' : '' }}" required>
                @error('product_name')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Catalogue No.</label>
                <input type="text" name="catalog_number" value="{{ old('catalog_number', $coa->catalog_number) }}">
            </div>
            <div class="form-group">
                <label>CAS Number</label>
                <input type="text" name="cas_number" value="{{ old('cas_number', $coa->cas_number) }}" placeholder="2023788-19-2">
            </div>
            <div class="form-group">
                <label>Molecular Formula</label>
                <input type="text" name="molecular_formula" value="{{ old('molecular_formula', $coa->molecular_formula) }}" placeholder="C₂₂₅H₃₄₈N₄₈O₆₈">
            </div>
            <div class="form-group">
                <label>Molecular Weight (g/mol)</label>
                <input type="number" step="0.0001" name="molecular_weight" value="{{ old('molecular_weight', $coa->molecular_weight) }}" placeholder="4813.46">
            </div>
            <div class="form-group">
                <label>Manufacturer</label>
                <input type="text" name="manufacturer" value="{{ old('manufacturer', $coa->manufacturer) }}" placeholder="Apex Laboratories">
            </div>
            <div class="form-group">
                <label>Quantity / Vial</label>
                <input type="text" name="quantity" value="{{ old('quantity', $coa->quantity) }}" placeholder="20 mg">
            </div>
            <div class="form-group">
                <label>Grade</label>
                <input type="text" name="grade" value="{{ old('grade', $coa->grade) }}" placeholder="Research Grade">
            </div>
        </div>

        {{-- ── DATES ── --}}
        <div class="section-hdr dark">Dates</div>
        <div class="form-grid cols3">
            <div class="form-group">
                <label>Manufacture Date *</label>
                <input type="date" name="manufacture_date" value="{{ old('manufacture_date', $coa->manufacture_date?->format('Y-m-d')) }}" class="{{ $errors->has('manufacture_date') ? 'error' : '' }}" required>
                @error('manufacture_date')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Analysis Start Date</label>
                <input type="date" name="analysis_start_date" value="{{ old('analysis_start_date', $coa->analysis_start_date?->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label>Analysis Date *</label>
                <input type="date" name="analysis_date" value="{{ old('analysis_date', $coa->analysis_date?->format('Y-m-d')) }}" class="{{ $errors->has('analysis_date') ? 'error' : '' }}" required>
                @error('analysis_date')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Re-Test / Expiry Date *</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date', $coa->expiry_date?->format('Y-m-d')) }}" class="{{ $errors->has('expiry_date') ? 'error' : '' }}" required>
                @error('expiry_date')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- ── TEST RESULTS ── --}}
        <div class="section-hdr">Test Results</div>
        <div class="form-grid cols3">
            <div class="form-group">
                <label>Purity HPLC (%) *</label>
                <input type="number" step="0.01" min="0" max="100" name="purity_hplc" value="{{ old('purity_hplc', $coa->purity_hplc) }}" class="{{ $errors->has('purity_hplc') ? 'error' : '' }}" required>
                @error('purity_hplc')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Appearance</label>
                <input type="text" name="appearance" value="{{ old('appearance', $coa->appearance) }}" placeholder="White to off-white lyophilized powder">
            </div>
            <div class="form-group">
                <label>Solubility</label>
                <input type="text" name="solubility" value="{{ old('solubility', $coa->solubility) }}" placeholder="Soluble in water at 1 mg/mL">
            </div>
            <div class="form-group">
                <label>Moisture Content</label>
                <input type="text" name="moisture_content" value="{{ old('moisture_content', $coa->moisture_content) }}" placeholder="≤ 5.0%">
            </div>
            <div class="form-group">
                <label>pH</label>
                <input type="text" name="ph" value="{{ old('ph', $coa->ph) }}" placeholder="6.0 - 7.0">
            </div>
            <div class="form-group">
                <label>Endotoxin</label>
                <input type="text" name="endotoxin" value="{{ old('endotoxin', $coa->endotoxin) }}" placeholder="< 1.0 EU/mg">
            </div>
            <div class="form-group">
                <label>Sterility</label>
                <input type="text" name="sterility" value="{{ old('sterility', $coa->sterility) }}" placeholder="Sterile – Passes USP &lt;71&gt;">
            </div>
            <div class="form-group">
                <label>Identity (MS)</label>
                <input type="text" name="identity_ms" value="{{ old('identity_ms', $coa->identity_ms) }}" placeholder="Confirmed by LC-MS/MS">
            </div>
            <div class="form-group">
                <label>Identity (HPLC)</label>
                <input type="text" name="identity_hplc" value="{{ old('identity_hplc', $coa->identity_hplc) }}" placeholder="Confirmed by RP-HPLC">
            </div>
            <div class="form-group" style="grid-column:span 3;">
                <label>Storage Conditions</label>
                <input type="text" name="storage_conditions" value="{{ old('storage_conditions', $coa->storage_conditions) }}" placeholder="-20°C, desiccated, protected from light">
            </div>
        </div>

        {{-- ── RECIPIENT ── --}}
        <div class="section-hdr dark">Recipient</div>
        <div class="form-grid cols3">
            <div class="form-group">
                <label>Recipient Name</label>
                <input type="text" name="recipient_name" value="{{ old('recipient_name', $coa->recipient_name) }}">
            </div>
            <div class="form-group">
                <label>Recipient Email</label>
                <input type="email" name="recipient_email" value="{{ old('recipient_email', $coa->recipient_email) }}">
                @error('recipient_email')<span class="err-msg">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- ── NOTES ── --}}
        <div class="section-hdr">Notes</div>
        <div class="form-group">
            <label>Internal Notes</label>
            <textarea name="notes" rows="3" style="resize:vertical;">{{ old('notes', $coa->notes) }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn" style="background:#17b8b4;color:#fff;padding:10px 28px;font-size:15px;">
                {{ $editing ? 'Save Changes' : 'Create COA Record' }}
            </button>
            @if($editing)
            <a href="{{ route('coa.preview', $coa->id) }}" class="btn btn-outline" style="color:#17b8b4;border-color:#17b8b4;" target="_blank">Preview COA</a>
            <a href="{{ route('coa.download', $coa->id) }}" class="btn btn-yellow">Download PDF</a>
            @endif
            <a href="{{ route('coa.index') }}" style="color:#888;font-size:14px;margin-left:8px;">Cancel</a>
        </div>
    </form>
</div>
@endsection

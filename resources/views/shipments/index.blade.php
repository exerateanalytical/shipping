@extends('layouts.app')
@section('title', 'DHL Shipments')
@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h1 style="color:#D40511;">DHL Express — Shipping Labels</h1>
        <a href="{{ route('shipments.create') }}" class="btn btn-red">+ New Shipment</a>
    </div>

    @if(session('success'))
    <div style="background:#f0fff4;border:1.5px solid #27ae60;border-radius:4px;padding:10px 16px;margin-bottom:16px;color:#1a7a3a;font-size:14px;">
        {{ session('success') }}
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Waybill #</th>
                <th>Shipper</th>
                <th>Receiver</th>
                <th>Destination</th>
                <th>Weight</th>
                <th>Ship Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($shipments as $s)
            <tr>
                <td><strong>{{ $s->waybill_number }}</strong><br><small style="color:#888;">{{ ucfirst(str_replace('_',' ',$s->status)) }}</small></td>
                <td>{{ $s->shipper_name }}<br><small style="color:#888;">{{ $s->shipper_city }}, {{ $s->shipper_country }}</small></td>
                <td>{{ $s->receiver_name }}<br><small style="color:#888;">{{ $s->receiver_company }}</small></td>
                <td>{{ $s->receiver_city }}, {{ $s->receiver_country }}</td>
                <td>{{ $s->weight_kg }} kg</td>
                <td>{{ $s->ship_date->format('d M Y') }}</td>
                <td style="white-space:nowrap;">
                    <a href="{{ route('shipments.edit', $s->id) }}" class="btn btn-outline" style="padding:5px 12px;font-size:12px;">Edit</a>
                    <a href="{{ route('dhl.preview', $s->id) }}" class="btn btn-outline" style="padding:5px 12px;font-size:12px;" target="_blank">Preview</a>
                    <a href="{{ route('dhl.download', $s->id) }}" class="btn btn-red" style="padding:5px 12px;font-size:12px;">PDF</a>
                    <form method="POST" action="{{ route('shipments.destroy', $s->id) }}" style="display:inline;" onsubmit="return confirm('Delete this shipment?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn" style="background:#fff;color:#888;border:1.5px solid #ddd;padding:5px 10px;font-size:12px;">✕</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $shipments->links() }}
</div>
@endsection

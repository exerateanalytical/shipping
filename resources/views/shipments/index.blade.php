@extends('layouts.app')
@section('title', 'DHL Shipments')
@section('content')
<div class="card">
    <h1 style="color:#D40511;">DHL Express — Shipping Labels</h1>
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
                <td><strong>{{ $s->waybill_number }}</strong></td>
                <td>{{ $s->shipper_name }}<br><small style="color:#888;">{{ $s->shipper_company }}</small></td>
                <td>{{ $s->receiver_name }}<br><small style="color:#888;">{{ $s->receiver_company }}</small></td>
                <td>{{ $s->receiver_city }}, {{ $s->receiver_country }}</td>
                <td>{{ $s->weight_kg }} kg</td>
                <td>{{ $s->ship_date->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('dhl.preview', $s->id) }}" class="btn btn-outline" target="_blank">Preview</a>
                    <a href="{{ route('dhl.download', $s->id) }}" class="btn btn-red">PDF</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $shipments->links() }}
</div>
@endsection

@extends('layouts.app')
@section('title', 'Certificate of Analysis')
@section('content')
<div class="card">
    <h1 style="color:#1a3a5c;">Certificate of Analysis — Apex Laboratories</h1>
    <table>
        <thead>
            <tr>
                <th>Lot Number</th>
                <th>Product</th>
                <th>Purity</th>
                <th>Analysis Date</th>
                <th>Expiry</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($records as $coa)
            <tr>
                <td><strong>{{ $coa->lot_number }}</strong></td>
                <td>{{ $coa->product_name }}<br><small style="color:#888;">{{ $coa->catalog_number }}</small></td>
                <td><span class="badge" style="background:#27ae60;color:#fff;">{{ $coa->purity_hplc }}%</span></td>
                <td>{{ $coa->analysis_date->format('d M Y') }}</td>
                <td>{{ $coa->expiry_date->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('coa.preview', $coa->id) }}" class="btn btn-outline" target="_blank" style="color:#1a3a5c;border-color:#1a3a5c;">Preview</a>
                    <a href="{{ route('coa.download', $coa->id) }}" class="btn btn-red">PDF</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $records->links() }}
</div>
@endsection

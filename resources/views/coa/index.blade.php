@extends('layouts.app')
@section('title', 'Certificate of Analysis')
@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h1 style="color:#17b8b4;">Certificate of Analysis — Apex Laboratories</h1>
        <a href="{{ route('coa.create') }}" class="btn" style="background:#17b8b4;color:#fff;">+ New COA</a>
    </div>

    @if(session('success'))
    <div style="background:#f0fff4;border:1.5px solid #27ae60;border-radius:4px;padding:10px 16px;margin-bottom:16px;color:#1a7a3a;font-size:14px;">
        {{ session('success') }}
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Lot Number</th>
                <th>Product</th>
                <th>Purity</th>
                <th>Recipient</th>
                <th>Analysis Date</th>
                <th>Expiry</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($records as $coa)
            <tr>
                <td><strong>{{ $coa->lot_number }}</strong><br><small style="color:#888;">{{ $coa->catalog_number }}</small></td>
                <td>{{ $coa->product_name }}<br><small style="color:#888;">{{ $coa->quantity }}</small></td>
                <td><span class="badge" style="background:#17b8b4;color:#fff;">{{ $coa->purity_hplc }}%</span></td>
                <td>{{ $coa->recipient_name }}<br><small style="color:#888;">{{ $coa->recipient_email }}</small></td>
                <td>{{ $coa->analysis_date->format('d M Y') }}</td>
                <td>{{ $coa->expiry_date->format('d M Y') }}</td>
                <td style="white-space:nowrap;">
                    <a href="{{ route('coa.edit', $coa->id) }}" class="btn btn-outline" style="color:#17b8b4;border-color:#17b8b4;padding:5px 12px;font-size:12px;">Edit</a>
                    <a href="{{ route('coa.preview', $coa->id) }}" class="btn btn-outline" style="color:#17b8b4;border-color:#17b8b4;padding:5px 12px;font-size:12px;" target="_blank">Preview</a>
                    <a href="{{ route('coa.download', $coa->id) }}" class="btn btn-red" style="padding:5px 12px;font-size:12px;">PDF</a>
                    <form method="POST" action="{{ route('coa.destroy', $coa->id) }}" style="display:inline;" onsubmit="return confirm('Delete this COA record?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn" style="background:#fff;color:#888;border:1.5px solid #ddd;padding:5px 10px;font-size:12px;">✕</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $records->links() }}
</div>
@endsection

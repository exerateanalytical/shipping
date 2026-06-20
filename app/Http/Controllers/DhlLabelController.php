<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DhlLabelController extends Controller
{
    public function preview($id)
    {
        $shipment = Shipment::findOrFail($id);
        return view('templates.dhl_label', compact('shipment'));
    }

    public function download($id)
    {
        $shipment = Shipment::findOrFail($id);
        $pdf = Pdf::loadView('templates.dhl_label_pdf', compact('shipment'))
            ->setPaper('a4', 'portrait');
        return $pdf->download("DHL_Label_{$shipment->waybill_number}.pdf");
    }

    public function index()
    {
        $shipments = Shipment::orderByDesc('created_at')->paginate(10);
        return view('shipments.index', compact('shipments'));
    }
}

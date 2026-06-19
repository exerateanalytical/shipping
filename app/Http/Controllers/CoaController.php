<?php

namespace App\Http\Controllers;

use App\Models\CoaRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    public function preview($id)
    {
        $coa = CoaRecord::findOrFail($id);
        return view('templates.coa', compact('coa'));
    }

    public function download($id)
    {
        $coa = CoaRecord::findOrFail($id);
        $pdf = Pdf::loadView('templates.coa_pdf', compact('coa'))
            ->setPaper('a4', 'portrait');
        return $pdf->download("COA_{$coa->product_name}_{$coa->lot_number}.pdf");
    }

    public function index()
    {
        $records = CoaRecord::orderByDesc('created_at')->paginate(10);
        return view('coa.index', compact('records'));
    }
}

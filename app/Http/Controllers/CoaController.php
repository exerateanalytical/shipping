<?php

namespace App\Http\Controllers;

use App\Models\CoaRecord;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    public function index()
    {
        $records = CoaRecord::orderByDesc('created_at')->paginate(10);
        return view('coa.index', compact('records'));
    }

    public function create()
    {
        return view('coa.form', ['coa' => new CoaRecord(), 'editing' => false]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        CoaRecord::create($data);
        return redirect()->route('coa.index')->with('success', 'COA record created successfully.');
    }

    public function edit($id)
    {
        $coa = CoaRecord::findOrFail($id);
        return view('coa.form', ['coa' => $coa, 'editing' => true]);
    }

    public function update(Request $request, $id)
    {
        $coa = CoaRecord::findOrFail($id);
        $data = $this->validated($request, $id);
        $coa->update($data);
        return redirect()->route('coa.index')->with('success', 'COA record updated successfully.');
    }

    public function destroy($id)
    {
        CoaRecord::findOrFail($id)->delete();
        return redirect()->route('coa.index')->with('success', 'COA record deleted.');
    }

    public function preview($id)
    {
        $coa = CoaRecord::findOrFail($id);
        return view('templates.coa', compact('coa'));
    }

    public function download($id)
    {
        $coa = CoaRecord::findOrFail($id);
        $pdf = SnappyPdf::loadView('templates.coa_print', compact('coa'))
            ->setOption('page-size', 'A4')
            ->setOption('margin-top', '0')
            ->setOption('margin-right', '0')
            ->setOption('margin-bottom', '0')
            ->setOption('margin-left', '0')
            ->setOption('enable-local-file-access', true);
        return $pdf->download("COA_{$coa->product_name}_{$coa->lot_number}.pdf");
    }

    private function validated(Request $request, $excludeId = null): array
    {
        $uniqueLot = 'unique:coa_records,lot_number' . ($excludeId ? ',' . $excludeId : '');
        return $request->validate([
            'lot_number'          => ['required', 'string', 'max:50', $uniqueLot],
            'product_name'        => ['required', 'string', 'max:150'],
            'cas_number'          => ['nullable', 'string', 'max:50'],
            'molecular_formula'   => ['nullable', 'string', 'max:100'],
            'molecular_weight'    => ['nullable', 'numeric', 'min:0'],
            'manufacturer'        => ['nullable', 'string', 'max:150'],
            'catalog_number'      => ['nullable', 'string', 'max:50'],
            'manufacture_date'    => ['required', 'date'],
            'expiry_date'         => ['required', 'date'],
            'analysis_date'       => ['required', 'date'],
            'analysis_start_date' => ['nullable', 'date'],
            'purity_hplc'         => ['required', 'numeric', 'min:0', 'max:100'],
            'appearance'          => ['nullable', 'string', 'max:200'],
            'solubility'          => ['nullable', 'string', 'max:200'],
            'moisture_content'    => ['nullable', 'string', 'max:50'],
            'ph'                  => ['nullable', 'string', 'max:30'],
            'endotoxin'           => ['nullable', 'string', 'max:100'],
            'sterility'           => ['nullable', 'string', 'max:100'],
            'identity_ms'         => ['nullable', 'string', 'max:100'],
            'identity_hplc'       => ['nullable', 'string', 'max:100'],
            'storage_conditions'  => ['nullable', 'string', 'max:200'],
            'grade'               => ['nullable', 'string', 'max:50'],
            'quantity'            => ['nullable', 'string', 'max:50'],
            'recipient_name'      => ['nullable', 'string', 'max:150'],
            'recipient_email'     => ['nullable', 'email', 'max:150'],
            'notes'               => ['nullable', 'string'],
        ]);
    }
}

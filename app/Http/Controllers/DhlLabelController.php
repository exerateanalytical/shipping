<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DhlLabelController extends Controller
{
    public function index()
    {
        $shipments = Shipment::orderByDesc('created_at')->paginate(10);
        return view('shipments.index', compact('shipments'));
    }

    public function create()
    {
        return view('shipments.form', ['shipment' => new Shipment(), 'editing' => false]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['insurance_refundable'] = $request->boolean('insurance_refundable');
        $data['is_sensitive']         = $request->boolean('is_sensitive');
        $shipment = Shipment::create($data);
        return redirect()->route('shipments.index')->with('success', 'Shipment created successfully.');
    }

    public function edit($id)
    {
        $shipment = Shipment::findOrFail($id);
        return view('shipments.form', ['shipment' => $shipment, 'editing' => true]);
    }

    public function update(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);
        $data = $this->validated($request, $id);
        $data['insurance_refundable'] = $request->boolean('insurance_refundable');
        $data['is_sensitive']         = $request->boolean('is_sensitive');
        $shipment->update($data);
        return redirect()->route('shipments.index')->with('success', 'Shipment updated successfully.');
    }

    public function destroy($id)
    {
        Shipment::findOrFail($id)->delete();
        return redirect()->route('shipments.index')->with('success', 'Shipment deleted.');
    }

    public function preview($id)
    {
        $shipment = Shipment::findOrFail($id);
        return view('templates.dhl_label', compact('shipment'));
    }

    public function download($id)
    {
        $shipment = Shipment::findOrFail($id);
        $pdf = Pdf::loadView('templates.dhl_label_pdf', compact('shipment'))
            ->setPaper([0, 0, 595.28, 841.89], 'portrait');
        return $pdf->download("DHL_Label_{$shipment->waybill_number}.pdf");
    }

    private function validated(Request $request, $excludeId = null): array
    {
        $uniqueWaybill = 'unique:shipments,waybill_number' . ($excludeId ? ',' . $excludeId : '');
        return $request->validate([
            'waybill_number'        => ['required', 'string', 'max:50', $uniqueWaybill],
            'status'                => ['required', 'string', 'max:50'],
            'service_type'          => ['required', 'string', 'max:100'],
            'product_code'          => ['required', 'string', 'max:10'],
            'shipper_name'          => ['required', 'string', 'max:150'],
            'shipper_company'       => ['nullable', 'string', 'max:150'],
            'shipper_address1'      => ['required', 'string', 'max:200'],
            'shipper_address2'      => ['nullable', 'string', 'max:200'],
            'shipper_city'          => ['required', 'string', 'max:100'],
            'shipper_state'         => ['nullable', 'string', 'max:100'],
            'shipper_postal'        => ['required', 'string', 'max:20'],
            'shipper_country'       => ['required', 'string', 'max:10'],
            'shipper_phone'         => ['nullable', 'string', 'max:50'],
            'receiver_name'         => ['required', 'string', 'max:150'],
            'receiver_company'      => ['nullable', 'string', 'max:150'],
            'receiver_address1'     => ['required', 'string', 'max:200'],
            'receiver_address2'     => ['nullable', 'string', 'max:200'],
            'receiver_city'         => ['required', 'string', 'max:100'],
            'receiver_state'        => ['nullable', 'string', 'max:100'],
            'receiver_postal'       => ['required', 'string', 'max:20'],
            'receiver_country'      => ['required', 'string', 'max:10'],
            'receiver_phone'        => ['nullable', 'string', 'max:50'],
            'weight_kg'             => ['required', 'numeric', 'min:0'],
            'dimensions'            => ['nullable', 'string', 'max:100'],
            'pieces'                => ['required', 'integer', 'min:1'],
            'content_description'   => ['required', 'string', 'max:200'],
            'currency'              => ['required', 'string', 'max:10'],
            'goods_value'           => ['nullable', 'numeric', 'min:0'],
            'insurance_fee'         => ['nullable', 'numeric', 'min:0'],
            'customs_duties'        => ['nullable', 'numeric', 'min:0'],
            'shipping_fee'          => ['nullable', 'numeric', 'min:0'],
            'shipping_fee_currency' => ['nullable', 'string', 'max:10'],
            'origin_service_area'   => ['nullable', 'string', 'max:10'],
            'dest_service_area'     => ['nullable', 'string', 'max:10'],
            'routing_code'          => ['nullable', 'string', 'max:50'],
            'ship_date'             => ['required', 'date'],
            'estimated_arrival'     => ['nullable', 'date'],
        ]);
    }
}

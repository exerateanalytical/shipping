@php
    $waybill       = $shipment->waybill_number;
    $shipDate      = $shipment->ship_date->format('d M Y');
    $shipDateShort = $shipment->ship_date->format('d/m/Y');

    $wLen   = mb_strlen($waybill);
    $masked = mb_substr($waybill, 0, 3)
            . str_repeat('*', max(0, $wLen - 5))
            . mb_substr($waybill, -2);

    $goodsValue    = (float)($shipment->goods_value   ?? 0);
    $insuranceFee  = (float)($shipment->insurance_fee ?? 0);
    $customsDuties = (float)($shipment->customs_duties ?? 0);
    $shippingFee   = (float)($shipment->shipping_fee  ?? 0);
    $totalFeesDue  = $shippingFee + $insuranceFee + $customsDuties;
    $cur           = $shipment->shipping_fee_currency ?? $shipment->currency ?? 'EUR';

    srand(crc32($waybill));
    $bars = '';
    for ($i = 0; $i < 70; $i++) {
        $w  = [2,2,2,3,3,4,2,3][rand(0,7)];
        $bg = $i % 2 === 0 ? '#000' : '#fff';
        $bars .= "<span style=\"display:inline-block;width:{$w}px;height:44px;background:{$bg};font-size:0;\"></span>";
    }
    srand();

    $weightKg    = (float)$shipment->weight_kg;
    $weightG     = $weightKg * 1000;
    $weightStr   = $weightG < 1000 ? number_format($weightG, 0).' g' : number_format($weightKg, 3).' kg';
    $weightLbStr = number_format($weightKg * 2.20462, 3).' lb';

    $acctNo  = strtoupper(substr(md5($waybill), 0, 8));
    $shipRef = strtoupper(substr(md5($waybill . $shipment->shipper_name), 0, 12));

    $receiverState = $shipment->receiver_state ? ', '.$shipment->receiver_state : '';
    $shipperState  = $shipment->shipper_state  ? ', '.$shipment->shipper_state  : '';

    $qrText = "DHL EXPRESS\nWaybill: {$waybill}\nStatus: PENDING\nInsurance: PENDING (Refundable on delivery)\nInsurance Fee: {$cur} ".number_format($insuranceFee,2)."\nCustoms Duties: {$cur} ".number_format($customsDuties,2)."\nTotal Fees Due: {$cur} ".number_format($totalFeesDue,2);
    $qrSvg  = (string)(new \SimpleSoftwareIO\QrCode\Generator)->format('svg')->size(80)->margin(1)->generate($qrText);
    $qrB64  = 'data:image/svg+xml;base64,'.base64_encode($qrSvg);
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 8pt;
    color: #000;
    background: #fff;
    margin: 20pt;
}
table { border-collapse: collapse; width: 100%; }
td { vertical-align: top; }
</style>
</head>
<body>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- MAIN LABEL TABLE  —  555pt wide (A4 - 2×20pt margins) --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<table style="width:555pt; border:1.5pt solid #999;">

{{-- ── ROW 1 : HEADER ── --}}
<tr style="border-bottom:2pt solid #000;">
    {{-- DHL logo --}}
    <td style="background:#D40511; width:80pt; padding:7pt 10pt; border-right:2pt solid #000; text-align:center; vertical-align:middle;">
        <span style="font-size:32pt; font-weight:bold; color:#FFCC00; letter-spacing:-2pt; line-height:1;">DHL</span>
    </td>
    {{-- Service name --}}
    <td style="padding:6pt 10pt; border-right:2pt solid #000; vertical-align:middle;">
        <div style="font-size:5.5pt; color:#D40511; font-weight:bold; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">DHL Express</div>
        <div style="font-size:13pt; font-weight:bold; text-transform:uppercase; line-height:1;">{{ $shipment->service_type }}</div>
        <div style="font-size:5.5pt; color:#555; margin-top:2pt;">Time Definite International Delivery &nbsp;&middot;&nbsp; Door to Door</div>
    </td>
    {{-- Product code --}}
    <td style="background:#FFCC00; width:55pt; text-align:center; padding:6pt 8pt; vertical-align:middle;">
        <div style="font-size:28pt; font-weight:bold; line-height:1;">{{ $shipment->product_code }}</div>
        <div style="font-size:4.5pt; font-weight:bold; text-transform:uppercase; color:#333; letter-spacing:.5pt; margin-top:2pt;">Product<br/>Code</div>
    </td>
</tr>

{{-- ── ROW 2 : DESTINATION BANNER ── --}}
<tr style="background:#FFCC00; border-bottom:2pt solid #000;">
    <td colspan="2" style="padding:6pt 10pt; border-right:2pt solid #000; vertical-align:middle;">
        <div style="font-size:5.5pt; text-transform:uppercase; color:#555; letter-spacing:.5pt; margin-bottom:2pt;">Routing Code</div>
        <div style="font-size:18pt; font-weight:bold; letter-spacing:2pt; color:#000; line-height:1;">{{ $shipment->routing_code ?? ($shipment->origin_service_area.'-'.$shipment->dest_service_area) }}</div>
        <div style="font-size:6.5pt; color:#333; margin-top:2pt; font-weight:bold;">{{ strtoupper($shipment->shipper_city) }}, {{ $shipment->shipper_country }} &nbsp;&rarr;&nbsp; {{ strtoupper($shipment->receiver_city) }}, {{ $shipment->receiver_country }}</div>
    </td>
    <td style="background:#000; text-align:center; padding:7pt 8pt; vertical-align:middle;">
        <div style="font-size:34pt; font-weight:bold; color:#FFCC00; line-height:1; letter-spacing:3pt;">{{ $shipment->dest_service_area }}</div>
        <div style="font-size:5.5pt; color:#FFCC00; margin-top:2pt; letter-spacing:1pt;">{{ strtoupper($shipment->receiver_city) }}</div>
    </td>
</tr>

{{-- ── ROW 3 : BARCODE + WAYBILL + QR ── --}}
<tr style="border-bottom:2pt solid #000;">
    <td colspan="2" style="padding:8pt 10pt 6pt;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:54%; vertical-align:top;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.5pt; margin-bottom:3pt;">Waybill Barcode &nbsp;&middot;&nbsp; Code 128</div>
                    <div style="height:44pt; overflow:hidden; white-space:nowrap;">{!! $bars !!}</div>
                    <div style="font-family:'Courier New',monospace; font-size:8pt; font-weight:bold; letter-spacing:2.5pt; margin-top:3pt;">{{ $masked }}</div>
                </td>
                <td style="width:46%; border-left:1.5pt solid #ddd; padding-left:9pt; vertical-align:top;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.5pt; margin-bottom:2pt;">Waybill No.</div>
                    <div style="font-family:'Courier New',monospace; font-size:13pt; font-weight:bold; letter-spacing:1.5pt; line-height:1.2;">{{ $masked }}</div>
                    <div style="font-size:6pt; color:#444; margin-top:3pt; line-height:1.7;">
                        <strong>Ship Date:</strong> {{ $shipDate }}<br/>
                        @if($shipment->estimated_arrival)
                        <strong style="color:#D40511;">Est. Delivery:</strong> {{ $shipment->estimated_arrival->format('d M Y') }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </td>
    <td style="width:55pt; text-align:center; padding:8pt 6pt; border-left:2pt solid #000; vertical-align:middle;">
        <img src="{{ $qrB64 }}" width="66" height="66" style="display:block; margin:0 auto;"/>
        <div style="font-size:4.5pt; color:#888; margin-top:2pt; text-align:center; text-transform:uppercase; letter-spacing:.3pt;">Scan to Track</div>
    </td>
</tr>

{{-- ── ROW 4 : ADDRESSES ── --}}
<tr style="border-bottom:2pt solid #000;">
    {{-- Shipper --}}
    <td style="padding:8pt 10pt; border-right:2pt solid #000; width:190pt; vertical-align:top;">
        <div style="font-size:5pt; text-transform:uppercase; color:#fff; background:#000; font-weight:bold; letter-spacing:.5pt; padding:1.5pt 5pt; display:inline-block; margin-bottom:4pt;">Shipper</div>
        <div style="font-size:9.5pt; font-weight:bold; margin-bottom:2pt; line-height:1.2;">{{ $shipment->shipper_name }}</div>
        @if($shipment->shipper_company)<div style="font-size:7.5pt; font-weight:bold; line-height:1.5; color:#222;">{{ $shipment->shipper_company }}</div>@endif
        <div style="font-size:7.5pt; line-height:1.55; color:#111;">{{ $shipment->shipper_address1 }}</div>
        @if($shipment->shipper_address2)<div style="font-size:7.5pt; line-height:1.55; color:#111;">{{ $shipment->shipper_address2 }}</div>@endif
        <div style="font-size:9pt; font-weight:bold; margin-top:2pt;">{{ strtoupper($shipment->shipper_city) }}{{ $shipperState }}</div>
        <div style="font-size:10.5pt; font-weight:bold; letter-spacing:1.5pt; margin-top:1pt;">{{ $shipment->shipper_postal }}</div>
        <div style="font-size:7.5pt; font-weight:bold; text-transform:uppercase; margin-top:1pt; color:#D40511;">{{ $shipment->shipper_country }}</div>
        @if($shipment->shipper_phone)<div style="font-size:6.5pt; color:#555; margin-top:3pt;">Tel: {{ $shipment->shipper_phone }}</div>@endif
    </td>
    {{-- Consignee --}}
    <td colspan="2" style="padding:8pt 10pt; vertical-align:top;">
        <div style="font-size:5pt; text-transform:uppercase; color:#fff; background:#000; font-weight:bold; letter-spacing:.5pt; padding:1.5pt 5pt; display:inline-block; margin-bottom:4pt;">Consignee</div>
        <div style="font-size:11.5pt; font-weight:bold; margin-bottom:2pt; line-height:1.2;">{{ $shipment->receiver_name }}</div>
        @if($shipment->receiver_company)<div style="font-size:8.5pt; font-weight:bold; line-height:1.5; color:#222;">{{ $shipment->receiver_company }}</div>@endif
        <div style="font-size:7.5pt; line-height:1.55; color:#111;">{{ $shipment->receiver_address1 }}</div>
        @if($shipment->receiver_address2)<div style="font-size:7.5pt; line-height:1.55; color:#111;">{{ $shipment->receiver_address2 }}</div>@endif
        <div style="font-size:10pt; font-weight:bold; margin-top:2pt;">{{ strtoupper($shipment->receiver_city) }}{{ $receiverState }}</div>
        <div style="font-size:12pt; font-weight:bold; letter-spacing:2pt; margin-top:1pt;">{{ $shipment->receiver_postal }}</div>
        <div style="font-size:7.5pt; font-weight:bold; text-transform:uppercase; margin-top:1pt; color:#D40511;">{{ $shipment->receiver_country }}</div>
        @if($shipment->receiver_phone)<div style="font-size:6.5pt; color:#555; margin-top:3pt;">Tel: {{ $shipment->receiver_phone }}</div>@endif
    </td>
</tr>

{{-- ── ROW 5 : SHIPMENT DETAILS ── --}}
<tr style="border-bottom:1.5pt solid #000;">
    <td style="padding:5pt 8pt; border-right:1pt solid #ccc;">
        <div style="font-size:4.5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1.5pt;">Gross Weight</div>
        <div style="font-size:8pt; font-weight:bold;">{{ $weightStr }}</div>
        <div style="font-size:5pt; color:#999; margin-top:1pt;">{{ $weightLbStr }}</div>
    </td>
    <td style="padding:5pt 8pt; border-right:1pt solid #ccc;">
        <div style="font-size:4.5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1.5pt;">Pieces</div>
        <div style="font-size:8pt; font-weight:bold;">{{ $shipment->pieces }} / 1</div>
    </td>
    <td style="padding:5pt 8pt;">
        <div style="font-size:4.5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1.5pt;">Ship Date</div>
        <div style="font-size:8pt; font-weight:bold;">{{ $shipDateShort }}</div>
        @if($shipment->estimated_arrival)
        <div style="font-size:5pt; color:#D40511; font-weight:bold; margin-top:1pt;">Est: {{ $shipment->estimated_arrival->format('d/m/Y') }}</div>
        @endif
    </td>
</tr>

{{-- ── ROW 6 : SERVICE ROW ── --}}
<tr style="border-bottom:1.5pt solid #000; background:#f2f2f2;">
    <td style="padding:4pt 8pt; border-right:1pt solid #ccc; text-align:center;">
        <div style="font-size:4.5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt;">Service</div>
        <div style="font-size:7.5pt; font-weight:bold; margin-top:1pt;">EXPRESS WW</div>
    </td>
    <td style="padding:4pt 8pt; border-right:1pt solid #ccc; text-align:center;">
        <div style="font-size:4.5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt;">Payor</div>
        <div style="font-size:7.5pt; font-weight:bold; margin-top:1pt;">Shipper</div>
    </td>
    <td style="padding:4pt 8pt; text-align:center;">
        <div style="font-size:4.5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt;">DHL Account</div>
        <div style="font-size:7pt; font-weight:bold; margin-top:1pt; font-family:'Courier New',monospace;">{{ $acctNo }}</div>
    </td>
</tr>

{{-- ── ROW 7 : CUSTOMS ── --}}
<tr style="border-bottom:1.5pt solid #000;">
    <td style="padding:5pt 8pt; border-right:1pt solid #ccc;">
        <div style="font-size:4.5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; display:block; margin-bottom:1pt;">Content Description</div>
        <span style="font-weight:bold; font-size:7pt;">{{ $shipment->content_description }}</span>
    </td>
    <td style="padding:5pt 8pt; border-right:1pt solid #ccc;">
        <div style="font-size:4.5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; display:block; margin-bottom:1pt;">HS Code</div>
        <span style="font-weight:bold; font-size:7pt;">2937.19</span>
    </td>
    <td style="padding:5pt 8pt;">
        <div style="font-size:4.5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; display:block; margin-bottom:1pt;">Declared Customs Value</div>
        <span style="font-weight:bold; font-size:7pt;">{{ $cur }} {{ number_format($goodsValue, 2) }}</span>
    </td>
</tr>

{{-- ── ROW 8 : CHARGES HEADER ── --}}
<tr>
    <td colspan="3" style="background:#1a1a1a; color:#fff; padding:4pt 10pt; font-size:6pt; font-weight:bold; text-transform:uppercase; letter-spacing:1.5pt; border-bottom:1pt solid #000;">
        Charges &amp; Payment Summary
    </td>
</tr>

{{-- Declared Goods Value --}}
@if($goodsValue)
<tr>
    <td colspan="2" style="padding:4pt 10pt; border-bottom:1pt solid #ececec; color:#888; font-size:7pt;">
        Declared Goods Value
        <span style="font-size:5pt; color:#999; display:block; margin-top:1pt;">For customs declaration only — not a DHL charge</span>
    </td>
    <td style="padding:4pt 10pt; border-bottom:1pt solid #ececec; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7pt; color:#888;">
        {{ $cur }}&nbsp;{{ number_format($goodsValue, 2) }}
    </td>
</tr>
@endif

{{-- Shipping Fee --}}
@if($shippingFee)
<tr>
    <td colspan="2" style="padding:4pt 10pt; border-bottom:1pt solid #ececec; font-size:7pt;">
        DHL Express Freight Charge
        <span style="font-size:5pt; color:#999; display:block; margin-top:1pt;">{{ $shipment->origin_service_area }} &rarr; {{ $shipment->dest_service_area }} &nbsp;&middot;&nbsp; {{ $shipment->service_type }}</span>
    </td>
    <td style="padding:4pt 10pt; border-bottom:1pt solid #ececec; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7pt;">
        {{ $cur }}&nbsp;{{ number_format($shippingFee, 2) }}
    </td>
</tr>
@endif

{{-- Insurance --}}
@if($insuranceFee)
<tr>
    <td colspan="2" style="padding:4pt 10pt; border-bottom:1pt solid #ececec; font-size:7pt;">
        Shipment Insurance (10% of declared value)
        <span style="font-size:5pt; color:#999; display:block; margin-top:1pt;">Covers loss, theft &amp; damage in transit</span>
        @if($shipment->insurance_refundable)
        <span style="font-size:5.5pt; color:#1a7a3a; display:block; margin-top:2pt; font-weight:bold;">&#10003; Refundable in full upon confirmed delivery</span>
        @endif
    </td>
    <td style="padding:4pt 10pt; border-bottom:1pt solid #ececec; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7pt;">
        {{ $cur }}&nbsp;{{ number_format($insuranceFee, 2) }}
    </td>
</tr>
@endif

{{-- Customs Duties --}}
@if($customsDuties)
<tr>
    <td colspan="2" style="padding:4pt 10pt; border-bottom:1pt solid #ececec; font-size:7pt;">
        Customs &amp; Import Duties
        <span style="font-size:5pt; color:#999; display:block; margin-top:1pt;">EU import clearance &nbsp;&middot;&nbsp; Destination: {{ $shipment->receiver_country }} &nbsp;&middot;&nbsp; Payable before release</span>
    </td>
    <td style="padding:4pt 10pt; border-bottom:1pt solid #ececec; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7pt;">
        {{ $cur }}&nbsp;{{ number_format($customsDuties, 2) }}
    </td>
</tr>
@endif

{{-- Total --}}
<tr>
    <td colspan="2" style="background:#1a1a1a; color:#FFCC00; font-size:9.5pt; font-weight:bold; padding:6pt 10pt; text-transform:uppercase; letter-spacing:.5pt;">
        Total Amount Due
    </td>
    <td style="background:#1a1a1a; color:#FFCC00; font-size:9.5pt; font-weight:bold; padding:6pt 10pt; text-align:right; font-family:'Courier New',monospace;">
        {{ $cur }}&nbsp;{{ number_format($totalFeesDue, 2) }}
    </td>
</tr>

{{-- ── FOOTER ── --}}
<tr style="border-top:1.5pt solid #000; background:#fafafa;">
    <td colspan="2" style="padding:5pt 10pt;">
        <div style="font-size:5.5pt; color:#555; line-height:1.8;">
            <strong>DHL Express (USA), Inc.</strong><br/>
            1200 S. Pine Island Road &nbsp;&middot;&nbsp; Plantation, FL 33324 &nbsp;&middot;&nbsp; USA<br/>
            Customer Service: 1-800-225-5345 &nbsp;&middot;&nbsp; dhl.com
        </div>
    </td>
    <td style="padding:5pt 10pt; vertical-align:bottom;">
        <div style="font-family:'Courier New',monospace; font-size:11pt; font-weight:bold; color:#D40511; letter-spacing:2pt; text-align:right;">{{ $masked }}</div>
        <div style="font-size:4.5pt; color:#999; margin-top:1pt; text-align:right;">Retain this waybill as proof of shipment</div>
    </td>
</tr>

{{-- ── DISCLAIMER ── --}}
<tr style="border-top:1pt solid #ddd; background:#f5f5f5;">
    <td colspan="3" style="padding:4pt 10pt;">
        <div style="font-size:4.5pt; color:#888; line-height:1.6;">
            By tendering this shipment, shipper agrees to DHL's Conditions of Carriage (available at dhl.com) and Tariff as applicable.
            Liability is limited under the Warsaw Convention / Montreal Convention and DHL's Standard Terms.
            All shipments are subject to inspection by customs authorities. Quote waybill number {{ $masked }} in all correspondence.
            Shipper Ref: {{ $shipRef }}
        </div>
    </td>
</tr>

</table>
</body>
</html>

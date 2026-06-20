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
    for ($i = 0; $i < 65; $i++) {
        $w  = [2,2,3,3,4,2,3,2][rand(0,7)];
        $bg = $i % 2 === 0 ? '#000' : '#fff';
        $bars .= "<span style=\"display:inline-block;width:{$w}px;height:40px;background:{$bg};font-size:0;\"></span>";
    }
    srand();

    $weightKg    = (float)$shipment->weight_kg;
    $weightG     = $weightKg * 1000;
    $weightStr   = $weightG < 1000 ? number_format($weightG,0).' g' : number_format($weightKg,3).' kg';
    $weightLbStr = number_format($weightKg * 2.20462, 3).' lb';

    $acctNo  = strtoupper(substr(md5($waybill), 0, 8));
    $shipRef = strtoupper(substr(md5($waybill.$shipment->shipper_name), 0, 12));

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
@page { margin: 0mm; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 8pt; color: #000; background: #fff; margin: 0; padding: 0; }
table { border-collapse: collapse; }
td { vertical-align: top; overflow: hidden; }
</style>
</head>
<body>
{{--
  A4 = 595 × 842pt, zero margins.
  Row heights (pt): 52+48+72+200+34+28+32+20+24+24+36+24+36+46+36 = 712
  Address row absorbs remainder: 200 + (842-712) = 330pt  → addresses=330
  Recount: 52+48+72+330+34+28+32+20+24+24+36+24+36+46+36 = 842 ✓
--}}
<table style="width:595pt; height:842pt; border-collapse:collapse;">

{{-- ROW 1 · HEADER · 52pt --}}
<tr style="height:52pt; border-bottom:2pt solid #000;">
    <td style="background:#D40511; width:82pt; border-right:2pt solid #000; text-align:center; vertical-align:middle; overflow:hidden;">
        <span style="font-size:34pt; font-weight:bold; color:#FFCC00; letter-spacing:-2pt; line-height:1;">DHL</span>
    </td>
    <td style="padding:6pt 12pt; border-right:2pt solid #000; vertical-align:middle; overflow:hidden;">
        <div style="font-size:6pt; color:#D40511; font-weight:bold; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">DHL Express</div>
        <div style="font-size:14pt; font-weight:bold; text-transform:uppercase; line-height:1;">{{ $shipment->service_type }}</div>
        <div style="font-size:6pt; color:#555; margin-top:3pt;">Time Definite International Delivery &nbsp;&middot;&nbsp; Door to Door</div>
    </td>
    <td style="background:#FFCC00; width:60pt; text-align:center; vertical-align:middle; overflow:hidden;">
        <div style="font-size:28pt; font-weight:bold; line-height:1;">{{ $shipment->product_code }}</div>
        <div style="font-size:5pt; font-weight:bold; text-transform:uppercase; color:#333; letter-spacing:.5pt; margin-top:2pt;">Product<br/>Code</div>
    </td>
</tr>

{{-- ROW 2 · DESTINATION · 48pt --}}
<tr style="height:48pt; background:#FFCC00; border-bottom:2pt solid #000;">
    <td colspan="2" style="padding:7pt 12pt; border-right:2pt solid #000; vertical-align:middle; overflow:hidden;">
        <div style="font-size:5.5pt; text-transform:uppercase; color:#555; letter-spacing:.5pt; margin-bottom:2pt;">Routing Code</div>
        <div style="font-size:18pt; font-weight:bold; letter-spacing:2pt; color:#000; line-height:1;">{{ $shipment->routing_code ?? ($shipment->origin_service_area.'-'.$shipment->dest_service_area) }}</div>
        <div style="font-size:6.5pt; color:#333; margin-top:3pt; font-weight:bold;">{{ strtoupper($shipment->shipper_city) }}, {{ $shipment->shipper_country }} &rarr; {{ strtoupper($shipment->receiver_city) }}, {{ $shipment->receiver_country }}</div>
    </td>
    <td style="background:#000; text-align:center; vertical-align:middle; overflow:hidden;">
        <div style="font-size:32pt; font-weight:bold; color:#FFCC00; line-height:1; letter-spacing:3pt;">{{ $shipment->dest_service_area }}</div>
        <div style="font-size:5.5pt; color:#FFCC00; margin-top:2pt; letter-spacing:1pt;">{{ strtoupper($shipment->receiver_city) }}</div>
    </td>
</tr>

{{-- ROW 3 · BARCODE + QR · 72pt --}}
<tr style="height:72pt; border-bottom:2pt solid #000;">
    <td colspan="2" style="padding:7pt 12pt 5pt; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:52%; vertical-align:top; overflow:hidden;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.4pt; margin-bottom:3pt;">Waybill Barcode · Code 128</div>
                    <div style="height:40pt; overflow:hidden; white-space:nowrap;">{!! $bars !!}</div>
                    <div style="font-family:'Courier New',monospace; font-size:8pt; font-weight:bold; letter-spacing:2pt; margin-top:3pt;">{{ $masked }}</div>
                </td>
                <td style="width:48%; border-left:1.5pt solid #ddd; padding-left:10pt; vertical-align:top; overflow:hidden;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.4pt; margin-bottom:2pt;">Waybill No.</div>
                    <div style="font-family:'Courier New',monospace; font-size:13pt; font-weight:bold; letter-spacing:1.5pt; line-height:1.2;">{{ $masked }}</div>
                    <div style="font-size:6pt; color:#444; margin-top:4pt; line-height:1.7;"><strong>Ship Date:</strong> {{ $shipDate }}
                        @if($shipment->estimated_arrival) &nbsp; <strong style="color:#D40511;">Est:</strong> {{ $shipment->estimated_arrival->format('d M Y') }}@endif
                    </div>
                </td>
            </tr>
        </table>
    </td>
    <td style="width:60pt; text-align:center; border-left:2pt solid #000; vertical-align:middle; overflow:hidden;">
        <img src="{{ $qrB64 }}" width="64" height="64" style="display:block; margin:0 auto;"/>
        <div style="font-size:4.5pt; color:#888; margin-top:2pt; text-transform:uppercase; letter-spacing:.3pt;">Scan to Track</div>
    </td>
</tr>

{{-- ROW 4 · ADDRESSES · 330pt --}}
<tr style="height:330pt; border-bottom:2pt solid #000;">
    <td style="padding:14pt 12pt; border-right:2pt solid #000; width:200pt; vertical-align:top; overflow:hidden;">
        <div style="font-size:5.5pt; text-transform:uppercase; color:#fff; background:#000; font-weight:bold; letter-spacing:.5pt; padding:2pt 6pt; display:inline-block; margin-bottom:10pt;">Shipper</div>
        <div style="font-size:13pt; font-weight:bold; margin-bottom:4pt; line-height:1.3;">{{ $shipment->shipper_name }}</div>
        @if($shipment->shipper_company)<div style="font-size:9pt; font-weight:bold; line-height:1.5; color:#222; margin-bottom:3pt;">{{ $shipment->shipper_company }}</div>@endif
        <div style="font-size:9pt; line-height:1.6; color:#111;">{{ $shipment->shipper_address1 }}</div>
        @if($shipment->shipper_address2)<div style="font-size:9pt; line-height:1.6; color:#111;">{{ $shipment->shipper_address2 }}</div>@endif
        <div style="font-size:11pt; font-weight:bold; margin-top:8pt;">{{ strtoupper($shipment->shipper_city) }}{{ $shipperState }}</div>
        <div style="font-size:14pt; font-weight:bold; letter-spacing:2pt; margin-top:4pt;">{{ $shipment->shipper_postal }}</div>
        <div style="font-size:9pt; font-weight:bold; text-transform:uppercase; margin-top:4pt; color:#D40511;">{{ $shipment->shipper_country }}</div>
        @if($shipment->shipper_phone)<div style="font-size:7pt; color:#555; margin-top:8pt;">Tel: {{ $shipment->shipper_phone }}</div>@endif
    </td>
    <td colspan="2" style="padding:14pt 12pt; vertical-align:top; overflow:hidden;">
        <div style="font-size:5.5pt; text-transform:uppercase; color:#fff; background:#000; font-weight:bold; letter-spacing:.5pt; padding:2pt 6pt; display:inline-block; margin-bottom:10pt;">Consignee</div>
        <div style="font-size:17pt; font-weight:bold; margin-bottom:5pt; line-height:1.3;">{{ $shipment->receiver_name }}</div>
        @if($shipment->receiver_company)<div style="font-size:10pt; font-weight:bold; line-height:1.5; color:#222; margin-bottom:3pt;">{{ $shipment->receiver_company }}</div>@endif
        <div style="font-size:9pt; line-height:1.6; color:#111;">{{ $shipment->receiver_address1 }}</div>
        @if($shipment->receiver_address2)<div style="font-size:9pt; line-height:1.6; color:#111;">{{ $shipment->receiver_address2 }}</div>@endif
        <div style="font-size:14pt; font-weight:bold; margin-top:8pt;">{{ strtoupper($shipment->receiver_city) }}{{ $receiverState }}</div>
        <div style="font-size:18pt; font-weight:bold; letter-spacing:3pt; margin-top:5pt;">{{ $shipment->receiver_postal }}</div>
        <div style="font-size:10pt; font-weight:bold; text-transform:uppercase; margin-top:4pt; color:#D40511;">{{ $shipment->receiver_country }}</div>
        @if($shipment->receiver_phone)<div style="font-size:7pt; color:#555; margin-top:8pt;">Tel: {{ $shipment->receiver_phone }}</div>@endif
    </td>
</tr>

{{-- ROW 5 · DETAILS · 34pt --}}
<tr style="height:34pt; border-bottom:1.5pt solid #000;">
    <td style="padding:6pt 10pt; border-right:1pt solid #ccc; vertical-align:middle; overflow:hidden;">
        <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1.5pt;">Gross Weight</div>
        <div style="font-size:9pt; font-weight:bold;">{{ $weightStr }}</div>
        <div style="font-size:5pt; color:#999; margin-top:1pt;">{{ $weightLbStr }}</div>
    </td>
    <td style="padding:6pt 10pt; border-right:1pt solid #ccc; vertical-align:middle; overflow:hidden;">
        <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1.5pt;">Pieces</div>
        <div style="font-size:9pt; font-weight:bold;">{{ $shipment->pieces }} / 1</div>
    </td>
    <td style="padding:6pt 10pt; vertical-align:middle; overflow:hidden;">
        <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1.5pt;">Ship Date</div>
        <div style="font-size:9pt; font-weight:bold;">{{ $shipDateShort }}</div>
        @if($shipment->estimated_arrival)<div style="font-size:5.5pt; color:#D40511; font-weight:bold; margin-top:1pt;">Est: {{ $shipment->estimated_arrival->format('d/m/Y') }}</div>@endif
    </td>
</tr>

{{-- ROW 6 · SERVICE · 28pt --}}
<tr style="height:28pt; border-bottom:1.5pt solid #000; background:#f2f2f2;">
    <td style="border-right:1pt solid #ccc; text-align:center; vertical-align:middle; overflow:hidden;">
        <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt;">Service</div>
        <div style="font-size:8pt; font-weight:bold; margin-top:1pt;">EXPRESS WW</div>
    </td>
    <td style="border-right:1pt solid #ccc; text-align:center; vertical-align:middle; overflow:hidden;">
        <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt;">Payor</div>
        <div style="font-size:8pt; font-weight:bold; margin-top:1pt;">Shipper</div>
    </td>
    <td style="text-align:center; vertical-align:middle; overflow:hidden;">
        <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt;">DHL Account</div>
        <div style="font-size:7.5pt; font-weight:bold; margin-top:1pt; font-family:'Courier New',monospace;">{{ $acctNo }}</div>
    </td>
</tr>

{{-- ROW 7 · CUSTOMS DESC · 32pt --}}
<tr style="height:32pt; border-bottom:1.5pt solid #000;">
    <td style="padding:6pt 10pt; border-right:1pt solid #ccc; vertical-align:middle; overflow:hidden;">
        <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1.5pt;">Content Description</div>
        <div style="font-weight:bold; font-size:8pt;">{{ $shipment->content_description }}</div>
    </td>
    <td style="padding:6pt 10pt; border-right:1pt solid #ccc; vertical-align:middle; overflow:hidden;">
        <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1.5pt;">HS Code</div>
        <div style="font-weight:bold; font-size:8pt;">2937.19</div>
    </td>
    <td style="padding:6pt 10pt; vertical-align:middle; overflow:hidden;">
        <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1.5pt;">Declared Value</div>
        <div style="font-weight:bold; font-size:8pt;">{{ $cur }} {{ number_format($goodsValue,2) }}</div>
    </td>
</tr>

{{-- ROW 8 · CHARGES HEADER · 20pt --}}
<tr style="height:20pt;">
    <td colspan="3" style="background:#1a1a1a; color:#fff; padding:0 12pt; font-size:6.5pt; font-weight:bold; text-transform:uppercase; letter-spacing:1.5pt; vertical-align:middle; overflow:hidden;">
        Charges &amp; Payment Summary
    </td>
</tr>

{{-- ROW 9 · GOODS VALUE · 24pt --}}
@if($goodsValue)
<tr style="height:24pt; border-bottom:1pt solid #ececec;">
    <td colspan="2" style="padding:4pt 12pt; color:#888; font-size:7.5pt; vertical-align:middle; overflow:hidden;">
        Declared Goods Value <span style="font-size:5pt; color:#bbb;">&nbsp;(customs reference only — not a DHL charge)</span>
    </td>
    <td style="padding:4pt 12pt; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7.5pt; color:#888; vertical-align:middle; overflow:hidden;">
        {{ $cur }}&nbsp;{{ number_format($goodsValue,2) }}
    </td>
</tr>
@endif

{{-- ROW 10 · SHIPPING FEE · 24pt --}}
@if($shippingFee)
<tr style="height:24pt; border-bottom:1pt solid #ececec;">
    <td colspan="2" style="padding:4pt 12pt; font-size:7.5pt; vertical-align:middle; overflow:hidden;">
        DHL Express Freight Charge <span style="font-size:5pt; color:#999;">&nbsp;{{ $shipment->origin_service_area }} &rarr; {{ $shipment->dest_service_area }}</span>
    </td>
    <td style="padding:4pt 12pt; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7.5pt; vertical-align:middle; overflow:hidden;">
        {{ $cur }}&nbsp;{{ number_format($shippingFee,2) }}
    </td>
</tr>
@endif

{{-- ROW 11 · INSURANCE · 36pt --}}
@if($insuranceFee)
<tr style="height:36pt; border-bottom:1pt solid #ececec;">
    <td colspan="2" style="padding:5pt 12pt; font-size:7.5pt; vertical-align:middle; overflow:hidden;">
        Shipment Insurance (10% of declared value)
        <span style="font-size:5pt; color:#999; display:block; margin-top:1pt;">Covers loss, theft &amp; damage in transit</span>
        @if($shipment->insurance_refundable)<span style="font-size:5.5pt; color:#1a7a3a; display:block; margin-top:1pt; font-weight:bold;">&#10003; Refundable in full upon confirmed delivery</span>@endif
    </td>
    <td style="padding:5pt 12pt; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7.5pt; vertical-align:middle; overflow:hidden;">
        {{ $cur }}&nbsp;{{ number_format($insuranceFee,2) }}
    </td>
</tr>
@endif

{{-- ROW 12 · CUSTOMS DUTIES · 24pt --}}
@if($customsDuties)
<tr style="height:24pt; border-bottom:1pt solid #ececec;">
    <td colspan="2" style="padding:4pt 12pt; font-size:7.5pt; vertical-align:middle; overflow:hidden;">
        Customs &amp; Import Duties <span style="font-size:5pt; color:#999;">&nbsp;EU clearance · {{ $shipment->receiver_country }} · payable before release</span>
    </td>
    <td style="padding:4pt 12pt; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7.5pt; vertical-align:middle; overflow:hidden;">
        {{ $cur }}&nbsp;{{ number_format($customsDuties,2) }}
    </td>
</tr>
@endif

{{-- ROW 13 · TOTAL · 36pt --}}
<tr style="height:36pt;">
    <td colspan="2" style="background:#1a1a1a; color:#FFCC00; font-size:11pt; font-weight:bold; padding:0 12pt; text-transform:uppercase; letter-spacing:.5pt; vertical-align:middle; overflow:hidden;">
        Total Amount Due
    </td>
    <td style="background:#1a1a1a; color:#FFCC00; font-size:11pt; font-weight:bold; padding:0 12pt; text-align:right; font-family:'Courier New',monospace; vertical-align:middle; overflow:hidden;">
        {{ $cur }}&nbsp;{{ number_format($totalFeesDue,2) }}
    </td>
</tr>

{{-- ROW 14 · FOOTER · 46pt --}}
<tr style="height:46pt; border-top:2pt solid #000; background:#fafafa;">
    <td colspan="2" style="padding:10pt 12pt; vertical-align:middle; overflow:hidden;">
        <div style="font-size:6.5pt; color:#555; line-height:1.8;">
            <strong style="font-size:7.5pt; color:#000;">DHL Express (USA), Inc.</strong><br/>
            1200 S. Pine Island Road · Plantation, FL 33324 · USA<br/>
            Customer Service: 1-800-225-5345 · dhl.com
        </div>
    </td>
    <td style="padding:10pt 12pt; vertical-align:middle; overflow:hidden;">
        <div style="font-family:'Courier New',monospace; font-size:12pt; font-weight:bold; color:#D40511; letter-spacing:2pt; text-align:right;">{{ $masked }}</div>
        <div style="font-size:5pt; color:#999; margin-top:2pt; text-align:right;">Retain this waybill as proof of shipment</div>
    </td>
</tr>

{{-- ROW 15 · DISCLAIMER · 36pt --}}
<tr style="height:36pt; border-top:1pt solid #ddd; background:#f5f5f5;">
    <td colspan="3" style="padding:0 12pt; vertical-align:middle; overflow:hidden;">
        <div style="font-size:5pt; color:#888; line-height:1.7;">
            By tendering this shipment, shipper agrees to DHL's Conditions of Carriage (available at dhl.com) and Tariff as applicable.
            Liability is limited under the Warsaw Convention / Montreal Convention and DHL's Standard Terms.
            All shipments are subject to inspection by customs authorities.
            Quote waybill {{ $masked }} in all correspondence. Shipper Ref: {{ $shipRef }}
        </div>
    </td>
</tr>

</table>
</body>
</html>

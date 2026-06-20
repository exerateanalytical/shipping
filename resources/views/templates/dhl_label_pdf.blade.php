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
    for ($i = 0; $i < 60; $i++) {
        $w  = [2,2,3,3,4,2,3,2][rand(0,7)];
        $bg = $i % 2 === 0 ? '#000' : '#fff';
        $bars .= "<span style=\"display:inline-block;width:{$w}px;height:36px;background:{$bg};font-size:0;\"></span>";
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
    $qrSvg  = (string)(new \SimpleSoftwareIO\QrCode\Generator)->format('svg')->size(72)->margin(1)->generate($qrText);
    $qrB64  = 'data:image/svg+xml;base64,'.base64_encode($qrSvg);
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { margin: 0; size: 595pt 842pt; }
html, body { margin: 0; padding: 0; width: 595pt; height: 842pt; font-family: Arial, Helvetica, sans-serif; font-size: 7.5pt; color: #000; background: #fff; }
* { box-sizing: border-box; }
table { border-collapse: collapse; width: 100%; }
td { vertical-align: top; }
.b2 { border-bottom: 2pt solid #000; }
.b1 { border-bottom: 1.5pt solid #000; }
.bs { border-bottom: 1pt solid #ddd; }
.br { border-right: 2pt solid #000; }
.brs { border-right: 1pt solid #ccc; }
.lbl { font-size: 4.5pt; text-transform: uppercase; color: #888; letter-spacing: .3pt; margin-bottom: 1.5pt; }
.val { font-size: 7.5pt; font-weight: bold; }
.cap { font-size: 5pt; text-transform: uppercase; color: #fff; background: #000; font-weight: bold; letter-spacing: .5pt; padding: 1.5pt 5pt; display: inline-block; margin-bottom: 6pt; }
.note { font-size: 4.5pt; color: #999; display: block; margin-top: 1pt; }
.mono { font-family: 'Courier New', monospace; }
</style>
</head>
<body>
<table style="width:595pt; border:1pt solid #999;">

{{-- ▌ HEADER --}}
<tr class="b2">
    <td class="br" style="background:#D40511; width:78pt; padding:7pt 10pt; text-align:center; vertical-align:middle;">
        <div style="font-size:30pt; font-weight:bold; color:#FFCC00; letter-spacing:-2pt; line-height:1;">DHL</div>
    </td>
    <td class="br" style="padding:5pt 10pt; vertical-align:middle;">
        <div style="font-size:5.5pt; color:#D40511; font-weight:bold; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">DHL Express</div>
        <div style="font-size:13pt; font-weight:bold; text-transform:uppercase; line-height:1;">{{ $shipment->service_type }}</div>
        <div style="font-size:5.5pt; color:#555; margin-top:3pt;">Time Definite International Delivery &middot; Door to Door</div>
    </td>
    <td style="background:#FFCC00; width:56pt; text-align:center; vertical-align:middle; padding:5pt 6pt;">
        <div style="font-size:26pt; font-weight:bold; line-height:1;">{{ $shipment->product_code }}</div>
        <div style="font-size:4.5pt; font-weight:bold; text-transform:uppercase; color:#333; margin-top:2pt;">Product<br/>Code</div>
    </td>
</tr>

{{-- ▌ DESTINATION BANNER --}}
<tr class="b2" style="background:#FFCC00;">
    <td class="br" colspan="2" style="padding:6pt 10pt; vertical-align:middle;">
        <div style="font-size:5pt; text-transform:uppercase; color:#555; letter-spacing:.5pt; margin-bottom:2pt;">Routing Code</div>
        <div style="font-size:17pt; font-weight:bold; letter-spacing:2pt; line-height:1;">{{ $shipment->routing_code ?? ($shipment->origin_service_area.'-'.$shipment->dest_service_area) }}</div>
        <div style="font-size:6pt; color:#333; margin-top:3pt; font-weight:bold;">{{ strtoupper($shipment->shipper_city) }}, {{ $shipment->shipper_country }} &rarr; {{ strtoupper($shipment->receiver_city) }}, {{ $shipment->receiver_country }}</div>
    </td>
    <td style="background:#000; text-align:center; vertical-align:middle; padding:5pt 6pt;">
        <div style="font-size:30pt; font-weight:bold; color:#FFCC00; line-height:1; letter-spacing:3pt;">{{ $shipment->dest_service_area }}</div>
        <div style="font-size:5pt; color:#FFCC00; margin-top:2pt; letter-spacing:1pt;">{{ strtoupper($shipment->receiver_city) }}</div>
    </td>
</tr>

{{-- ▌ BARCODE + QR --}}
<tr class="b2">
    <td class="br" colspan="2" style="padding:7pt 10pt 6pt;">
        <table style="border-collapse:collapse; width:100%;">
            <tr>
                <td style="width:52%; vertical-align:top;">
                    <div class="lbl">Waybill Barcode &middot; Code 128</div>
                    <div style="height:36pt; overflow:hidden; white-space:nowrap;">{!! $bars !!}</div>
                    <div class="mono" style="font-size:7.5pt; font-weight:bold; letter-spacing:2pt; margin-top:3pt;">{{ $masked }}</div>
                </td>
                <td style="width:48%; border-left:1.5pt solid #ddd; padding-left:9pt; vertical-align:top;">
                    <div class="lbl">Waybill No.</div>
                    <div class="mono" style="font-size:12pt; font-weight:bold; letter-spacing:1.5pt; line-height:1.2;">{{ $masked }}</div>
                    <div style="font-size:5.5pt; color:#444; margin-top:4pt; line-height:1.7;"><strong>Ship Date:</strong> {{ $shipDate }}
                        @if($shipment->estimated_arrival) &nbsp;&nbsp;<strong style="color:#D40511;">Est:</strong> {{ $shipment->estimated_arrival->format('d M Y') }}@endif
                    </div>
                </td>
            </tr>
        </table>
    </td>
    <td style="text-align:center; vertical-align:middle; border-left:2pt solid #000; padding:5pt;">
        <img src="{{ $qrB64 }}" width="60" height="60" style="display:block; margin:0 auto;"/>
        <div style="font-size:4pt; color:#888; margin-top:2pt; text-transform:uppercase; letter-spacing:.3pt;">Scan to Track</div>
    </td>
</tr>

{{-- ▌ ADDRESSES --}}
<tr class="b2">
    <td class="br" style="padding:10pt; width:190pt; vertical-align:top;">
        <div class="cap">Shipper</div>
        <div style="font-size:10pt; font-weight:bold; margin-bottom:3pt; line-height:1.2;">{{ $shipment->shipper_name }}</div>
        @if($shipment->shipper_company)<div style="font-size:7.5pt; font-weight:bold; line-height:1.5; color:#222;">{{ $shipment->shipper_company }}</div>@endif
        <div style="font-size:7.5pt; line-height:1.55; color:#111;">{{ $shipment->shipper_address1 }}</div>
        @if($shipment->shipper_address2)<div style="font-size:7.5pt; line-height:1.55; color:#111;">{{ $shipment->shipper_address2 }}</div>@endif
        <div style="font-size:9pt; font-weight:bold; margin-top:5pt;">{{ strtoupper($shipment->shipper_city) }}{{ $shipperState }}</div>
        <div style="font-size:11pt; font-weight:bold; letter-spacing:2pt; margin-top:2pt;">{{ $shipment->shipper_postal }}</div>
        <div style="font-size:7.5pt; font-weight:bold; text-transform:uppercase; margin-top:2pt; color:#D40511;">{{ $shipment->shipper_country }}</div>
        @if($shipment->shipper_phone)<div style="font-size:6.5pt; color:#555; margin-top:5pt;">Tel: {{ $shipment->shipper_phone }}</div>@endif
    </td>
    <td colspan="2" style="padding:10pt; vertical-align:top;">
        <div class="cap">Consignee</div>
        <div style="font-size:13pt; font-weight:bold; margin-bottom:3pt; line-height:1.2;">{{ $shipment->receiver_name }}</div>
        @if($shipment->receiver_company)<div style="font-size:8.5pt; font-weight:bold; line-height:1.5; color:#222;">{{ $shipment->receiver_company }}</div>@endif
        <div style="font-size:7.5pt; line-height:1.55; color:#111;">{{ $shipment->receiver_address1 }}</div>
        @if($shipment->receiver_address2)<div style="font-size:7.5pt; line-height:1.55; color:#111;">{{ $shipment->receiver_address2 }}</div>@endif
        <div style="font-size:11pt; font-weight:bold; margin-top:5pt;">{{ strtoupper($shipment->receiver_city) }}{{ $receiverState }}</div>
        <div style="font-size:14pt; font-weight:bold; letter-spacing:2.5pt; margin-top:3pt;">{{ $shipment->receiver_postal }}</div>
        <div style="font-size:8pt; font-weight:bold; text-transform:uppercase; margin-top:2pt; color:#D40511;">{{ $shipment->receiver_country }}</div>
        @if($shipment->receiver_phone)<div style="font-size:6.5pt; color:#555; margin-top:5pt;">Tel: {{ $shipment->receiver_phone }}</div>@endif
    </td>
</tr>

{{-- ▌ DETAILS --}}
<tr class="b1">
    <td class="brs" style="padding:5pt 9pt; vertical-align:middle;">
        <div class="lbl">Gross Weight</div>
        <div class="val">{{ $weightStr }}</div>
        <div style="font-size:4.5pt; color:#999; margin-top:1pt;">{{ $weightLbStr }}</div>
    </td>
    <td class="brs" style="padding:5pt 9pt; vertical-align:middle;">
        <div class="lbl">Pieces</div>
        <div class="val">{{ $shipment->pieces }} / 1</div>
    </td>
    <td style="padding:5pt 9pt; vertical-align:middle;">
        <div class="lbl">Ship Date</div>
        <div class="val">{{ $shipDateShort }}</div>
        @if($shipment->estimated_arrival)<div style="font-size:5pt; color:#D40511; font-weight:bold; margin-top:1pt;">Est: {{ $shipment->estimated_arrival->format('d/m/Y') }}</div>@endif
    </td>
</tr>

{{-- ▌ SERVICE --}}
<tr class="b1" style="background:#f2f2f2;">
    <td class="brs" style="padding:4pt 9pt; text-align:center; vertical-align:middle;">
        <div class="lbl">Service</div>
        <div class="val">EXPRESS WW</div>
    </td>
    <td class="brs" style="padding:4pt 9pt; text-align:center; vertical-align:middle;">
        <div class="lbl">Payor</div>
        <div class="val">Shipper</div>
    </td>
    <td style="padding:4pt 9pt; text-align:center; vertical-align:middle;">
        <div class="lbl">DHL Account</div>
        <div class="val mono" style="font-size:6.5pt;">{{ $acctNo }}</div>
    </td>
</tr>

{{-- ▌ CUSTOMS --}}
<tr class="b1">
    <td class="brs" style="padding:5pt 9pt; vertical-align:middle;">
        <div class="lbl">Content Description</div>
        <div class="val">{{ $shipment->content_description }}</div>
    </td>
    <td class="brs" style="padding:5pt 9pt; vertical-align:middle;">
        <div class="lbl">HS Code</div>
        <div class="val">2937.19</div>
    </td>
    <td style="padding:5pt 9pt; vertical-align:middle;">
        <div class="lbl">Declared Value</div>
        <div class="val">{{ $cur }} {{ number_format($goodsValue,2) }}</div>
    </td>
</tr>

{{-- ▌ CHARGES HEADER --}}
<tr>
    <td colspan="3" style="background:#1a1a1a; color:#fff; padding:4pt 10pt; font-size:6pt; font-weight:bold; text-transform:uppercase; letter-spacing:1.5pt; vertical-align:middle;">
        Charges &amp; Payment Summary
    </td>
</tr>

{{-- Goods value --}}
@if($goodsValue)
<tr class="bs">
    <td colspan="2" style="padding:4pt 10pt; color:#888; font-size:7pt; vertical-align:middle;">
        Declared Goods Value <span style="font-size:4.5pt; color:#bbb;">(customs reference — not a DHL charge)</span>
    </td>
    <td style="padding:4pt 10pt; text-align:right; font-weight:bold; font-size:7pt; color:#888; vertical-align:middle;" class="mono">
        {{ $cur }}&nbsp;{{ number_format($goodsValue,2) }}
    </td>
</tr>
@endif

{{-- Shipping --}}
@if($shippingFee)
<tr class="bs">
    <td colspan="2" style="padding:4pt 10pt; font-size:7pt; vertical-align:middle;">
        DHL Express Freight Charge
        <span class="note">{{ $shipment->origin_service_area }} &rarr; {{ $shipment->dest_service_area }} &middot; {{ $shipment->service_type }}</span>
    </td>
    <td style="padding:4pt 10pt; text-align:right; font-weight:bold; font-size:7pt; vertical-align:middle;" class="mono">
        {{ $cur }}&nbsp;{{ number_format($shippingFee,2) }}
    </td>
</tr>
@endif

{{-- Insurance --}}
@if($insuranceFee)
<tr class="bs">
    <td colspan="2" style="padding:4pt 10pt; font-size:7pt; vertical-align:middle;">
        Shipment Insurance (10% of declared value)
        <span class="note">Covers loss, theft &amp; damage in transit</span>
        @if($shipment->insurance_refundable)<span style="font-size:5pt; color:#1a7a3a; display:block; margin-top:1pt; font-weight:bold;">&#10003; Refundable in full upon confirmed delivery</span>@endif
    </td>
    <td style="padding:4pt 10pt; text-align:right; font-weight:bold; font-size:7pt; vertical-align:middle;" class="mono">
        {{ $cur }}&nbsp;{{ number_format($insuranceFee,2) }}
    </td>
</tr>
@endif

{{-- Customs duties --}}
@if($customsDuties)
<tr class="bs">
    <td colspan="2" style="padding:4pt 10pt; font-size:7pt; vertical-align:middle;">
        Customs &amp; Import Duties
        <span class="note">EU import clearance &middot; {{ $shipment->receiver_country }} &middot; payable before release</span>
    </td>
    <td style="padding:4pt 10pt; text-align:right; font-weight:bold; font-size:7pt; vertical-align:middle;" class="mono">
        {{ $cur }}&nbsp;{{ number_format($customsDuties,2) }}
    </td>
</tr>
@endif

{{-- Total --}}
<tr>
    <td colspan="2" style="background:#1a1a1a; color:#FFCC00; font-size:10.5pt; font-weight:bold; padding:6pt 10pt; text-transform:uppercase; letter-spacing:.5pt; vertical-align:middle;">
        Total Amount Due
    </td>
    <td style="background:#1a1a1a; color:#FFCC00; font-size:10.5pt; font-weight:bold; padding:6pt 10pt; text-align:right; vertical-align:middle;" class="mono">
        {{ $cur }}&nbsp;{{ number_format($totalFeesDue,2) }}
    </td>
</tr>

{{-- ▌ FOOTER --}}
<tr style="border-top:2pt solid #000; background:#fafafa;">
    <td colspan="2" style="padding:8pt 10pt; vertical-align:middle;">
        <div style="font-size:6pt; color:#555; line-height:1.8;">
            <strong style="font-size:7pt; color:#000;">DHL Express (USA), Inc.</strong><br/>
            1200 S. Pine Island Road &middot; Plantation, FL 33324 &middot; USA<br/>
            Customer Service: 1-800-225-5345 &middot; dhl.com
        </div>
    </td>
    <td style="padding:8pt 10pt; vertical-align:middle;">
        <div class="mono" style="font-size:11pt; font-weight:bold; color:#D40511; letter-spacing:2pt; text-align:right;">{{ $masked }}</div>
        <div style="font-size:4.5pt; color:#999; margin-top:2pt; text-align:right;">Retain this waybill as proof of shipment</div>
    </td>
</tr>

{{-- ▌ DISCLAIMER --}}
<tr style="border-top:1pt solid #ddd; background:#f5f5f5;">
    <td colspan="3" style="padding:5pt 10pt;">
        <div style="font-size:4.5pt; color:#888; line-height:1.7;">
            By tendering this shipment, shipper agrees to DHL's Conditions of Carriage (available at dhl.com) and Tariff as applicable. Liability is limited under the Warsaw Convention / Montreal Convention and DHL's Standard Terms. All shipments are subject to inspection by customs authorities. Quote waybill {{ $masked }} in all correspondence. Shipper Ref: {{ $shipRef }}
        </div>
    </td>
</tr>

</table>
</body>
</html>

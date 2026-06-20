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
    for ($i = 0; $i < 80; $i++) {
        $w = [1,1,1,2,2,3,1,2][rand(0,7)];
        $bg = $i % 2 === 0 ? '#000' : '#fff';
        $bars .= "<span style=\"display:inline-block;width:{$w}px;height:52px;background:{$bg};font-size:0;\"></span>";
    }
    srand();

    $weightKg    = (float)$shipment->weight_kg;
    $weightG     = $weightKg * 1000;
    $weightStr   = $weightG < 1000 ? number_format($weightG, 0) . ' g' : number_format($weightKg, 3) . ' kg';
    $weightLbStr = number_format($weightKg * 2.20462, 3) . ' lb';

    $acctNo  = strtoupper(substr(md5($waybill), 0, 8));
    $shipRef = strtoupper(substr(md5($waybill . $shipment->shipper_name), 0, 12));

    $receiverState = $shipment->receiver_state ? ', ' . $shipment->receiver_state : '';
    $shipperState  = $shipment->shipper_state  ? ', ' . $shipment->shipper_state  : '';

    $qrText = "DHL EXPRESS\nWaybill: {$waybill}\nStatus: PENDING\nInsurance: PENDING (Refundable on delivery)\nInsurance Fee: {$cur} " . number_format($insuranceFee, 2) . "\nCustoms Duties: {$cur} " . number_format($customsDuties, 2) . "\nTotal Fees Due: {$cur} " . number_format($totalFeesDue, 2);
    $qrSvg  = (string)(new \SimpleSoftwareIO\QrCode\Generator)->format('svg')->size(90)->margin(1)->generate($qrText);
    $qrB64  = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; color: #000; background: #fff; }
table { border-collapse: collapse; }
td { vertical-align: top; }

.hdr-logo-text { font-size: 36pt; font-weight: bold; color: #FFCC00; letter-spacing: -2pt; line-height: 1; }
.hdr-svc-sub   { font-size: 6pt; color: #D40511; font-weight: bold; text-transform: uppercase; letter-spacing: 2pt; margin-bottom: 2pt; }
.hdr-svc-name  { font-size: 15pt; font-weight: bold; text-transform: uppercase; line-height: 1; }
.hdr-svc-desc  { font-size: 6pt; color: #555; margin-top: 2pt; }
.hdr-prod-ltr  { font-size: 30pt; font-weight: bold; line-height: 1; }
.hdr-prod-tag  { font-size: 5pt; font-weight: bold; text-transform: uppercase; color: #333; letter-spacing: .5pt; margin-top: 2pt; }

.dest-route-tag    { font-size: 6pt; text-transform: uppercase; color: #555; letter-spacing: .5pt; margin-bottom: 2pt; }
.dest-route-code   { font-size: 19pt; font-weight: bold; letter-spacing: 2pt; color: #000; line-height: 1; }
.dest-route-cities { font-size: 7pt; color: #333; margin-top: 3pt; font-weight: bold; }
.dest-code         { font-size: 36pt; font-weight: bold; color: #FFCC00; line-height: 1; letter-spacing: 4pt; }
.dest-name         { font-size: 6pt; color: #FFCC00; margin-top: 2pt; letter-spacing: 1pt; }

.addr-cap    { font-size: 5.5pt; text-transform: uppercase; color: #fff; background: #000; font-weight: bold; letter-spacing: .5pt; padding: 2pt 5pt; display: inline-block; margin-bottom: 5pt; }
.addr-name   { font-size: 10pt; font-weight: bold; margin-bottom: 2pt; line-height: 1.2; }
.addr-line   { font-size: 8pt; line-height: 1.6; color: #111; }
.addr-city   { font-size: 9.5pt; font-weight: bold; margin-top: 3pt; }
.addr-postal { font-size: 11pt; font-weight: bold; letter-spacing: 2pt; margin-top: 1pt; }
.addr-country{ font-size: 8pt; font-weight: bold; text-transform: uppercase; margin-top: 1pt; color: #D40511; }
.addr-phone  { font-size: 7pt; color: #555; margin-top: 3pt; }

.dc-lbl { font-size: 5pt; text-transform: uppercase; color: #888; letter-spacing: .3pt; margin-bottom: 2pt; }
.dc-val { font-size: 8.5pt; font-weight: bold; }
.dc-sub { font-size: 5.5pt; color: #999; margin-top: 1pt; }

.sv-lbl { font-size: 5pt; text-transform: uppercase; color: #888; letter-spacing: .3pt; }
.sv-val { font-size: 8pt; font-weight: bold; margin-top: 1pt; }

.ci-lbl { font-size: 5pt; text-transform: uppercase; color: #888; letter-spacing: .3pt; display: block; margin-bottom: 1pt; }
.ci-val { font-weight: bold; font-size: 7.5pt; }

.row-note   { font-size: 5.5pt; color: #999; display: block; margin-top: 1pt; }
.row-refund { font-size: 6pt; color: #1a7a3a; display: block; margin-top: 2pt; font-weight: bold; }

.ftr-corp { font-size: 6pt; color: #555; line-height: 1.8; }
.ftr-wb   { font-family: 'Courier New', monospace; font-size: 12pt; font-weight: bold; color: #D40511; letter-spacing: 2pt; text-align: right; }
.ftr-note { font-size: 5pt; color: #999; margin-top: 1pt; text-align: right; }
.disc     { font-size: 5pt; color: #888; line-height: 1.6; }
</style>
</head>
<body>

<table style="border:1pt solid #aaa; width:530pt; border-collapse:collapse;">

{{-- HEADER --}}
<tr style="border-bottom:2pt solid #000;">
    <td style="background:#D40511; width:80pt; padding:8pt 12pt; text-align:center; border-right:2pt solid #000; vertical-align:middle;">
        <div class="hdr-logo-text">DHL</div>
    </td>
    <td style="padding:7pt 12pt; border-right:2pt solid #000;">
        <div class="hdr-svc-sub">DHL Express</div>
        <div class="hdr-svc-name">{{ $shipment->service_type }}</div>
        <div class="hdr-svc-desc">Time Definite International Delivery &nbsp;&middot;&nbsp; Door to Door</div>
    </td>
    <td style="background:#FFCC00; width:60pt; text-align:center; padding:7pt 10pt; vertical-align:middle;">
        <div class="hdr-prod-ltr">{{ $shipment->product_code }}</div>
        <div class="hdr-prod-tag">Product<br/>Code</div>
    </td>
</tr>

{{-- DESTINATION BANNER --}}
<tr style="background:#FFCC00; border-top:2pt solid #000; border-bottom:2pt solid #000;">
    <td colspan="2" style="padding:7pt 12pt; border-right:2pt solid #000;">
        <div class="dest-route-tag">Routing Code</div>
        <div class="dest-route-code">{{ $shipment->routing_code ?? ($shipment->origin_service_area . '-' . $shipment->dest_service_area) }}</div>
        <div class="dest-route-cities">{{ strtoupper($shipment->shipper_city) }}, {{ $shipment->shipper_country }} &nbsp;&rarr;&nbsp; {{ strtoupper($shipment->receiver_city) }}, {{ $shipment->receiver_country }}</div>
    </td>
    <td style="background:#000; text-align:center; padding:8pt 10pt; vertical-align:middle;">
        <div class="dest-code">{{ $shipment->dest_service_area }}</div>
        <div class="dest-name">{{ strtoupper($shipment->receiver_city) }}</div>
    </td>
</tr>

{{-- BARCODE + WAYBILL + QR --}}
<tr style="border-bottom:2pt solid #000;">
    <td colspan="2" style="padding:9pt 12pt 7pt;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:55%;">
                    <div class="dc-lbl">Waybill Barcode &nbsp;&middot;&nbsp; Code 128</div>
                    <div style="height:52pt; overflow:hidden; white-space:nowrap;">{!! $bars !!}</div>
                    <div style="font-family:'Courier New',monospace; font-size:9pt; font-weight:bold; letter-spacing:3pt; margin-top:4pt;">{{ $masked }}</div>
                </td>
                <td style="width:45%; border-left:2pt solid #eee; padding-left:10pt; vertical-align:top;">
                    <div style="font-size:5.5pt; text-transform:uppercase; color:#888; letter-spacing:.5pt; margin-bottom:2pt;">Waybill No.</div>
                    <div style="font-family:'Courier New',monospace; font-size:14pt; font-weight:bold; letter-spacing:2pt; line-height:1.2;">{{ $masked }}</div>
                    <div style="font-size:6.5pt; color:#444; margin-top:4pt; line-height:1.7;">
                        <strong>Ship Date:</strong> {{ $shipDate }}<br/>
                        @if($shipment->estimated_arrival)
                        <strong style="color:#D40511;">Est. Delivery:</strong> {{ $shipment->estimated_arrival->format('d M Y') }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </td>
    <td style="width:90pt; text-align:center; padding:9pt 8pt; border-left:2pt solid #000; vertical-align:middle;">
        <img src="{{ $qrB64 }}" width="80" height="80" style="display:block; margin:0 auto;"/>
        <div style="font-size:5pt; color:#888; margin-top:3pt; text-align:center; text-transform:uppercase; letter-spacing:.3pt;">Scan to Track</div>
    </td>
</tr>

{{-- ADDRESSES --}}
<tr style="border-bottom:2pt solid #000;">
    <td style="padding:9pt 12pt; border-right:2pt solid #000; width:200pt;">
        <div class="addr-cap">Shipper</div>
        <div class="addr-name">{{ $shipment->shipper_name }}</div>
        @if($shipment->shipper_company)<div class="addr-line" style="font-weight:bold;">{{ $shipment->shipper_company }}</div>@endif
        <div class="addr-line">{{ $shipment->shipper_address1 }}</div>
        @if($shipment->shipper_address2)<div class="addr-line">{{ $shipment->shipper_address2 }}</div>@endif
        <div class="addr-city">{{ strtoupper($shipment->shipper_city) }}{{ $shipperState }}</div>
        <div class="addr-postal">{{ $shipment->shipper_postal }}</div>
        <div class="addr-country">{{ $shipment->shipper_country }}</div>
        @if($shipment->shipper_phone)<div class="addr-phone">Tel: {{ $shipment->shipper_phone }}</div>@endif
    </td>
    <td colspan="2" style="padding:9pt 12pt;">
        <div class="addr-cap">Consignee</div>
        <div class="addr-name" style="font-size:12pt;">{{ $shipment->receiver_name }}</div>
        @if($shipment->receiver_company)<div class="addr-line" style="font-size:9pt; font-weight:bold;">{{ $shipment->receiver_company }}</div>@endif
        <div class="addr-line">{{ $shipment->receiver_address1 }}</div>
        @if($shipment->receiver_address2)<div class="addr-line">{{ $shipment->receiver_address2 }}</div>@endif
        <div class="addr-city" style="font-size:10.5pt;">{{ strtoupper($shipment->receiver_city) }}{{ $receiverState }}</div>
        <div class="addr-postal" style="font-size:13pt;">{{ $shipment->receiver_postal }}</div>
        <div class="addr-country">{{ $shipment->receiver_country }}</div>
        @if($shipment->receiver_phone)<div class="addr-phone">Tel: {{ $shipment->receiver_phone }}</div>@endif
    </td>
</tr>

{{-- DETAILS BAR --}}
<tr style="border-bottom:1.5pt solid #000;">
    <td style="padding:5pt 10pt; border-right:1pt solid #ccc;">
        <div class="dc-lbl">Gross Weight</div>
        <div class="dc-val">{{ $weightStr }}</div>
        <div class="dc-sub">{{ $weightLbStr }}</div>
    </td>
    <td style="padding:5pt 10pt; border-right:1pt solid #ccc;">
        <div class="dc-lbl">Pieces</div>
        <div class="dc-val">{{ $shipment->pieces }} / 1</div>
    </td>
    <td style="padding:5pt 10pt;">
        <div class="dc-lbl">Ship Date</div>
        <div class="dc-val">{{ $shipDateShort }}</div>
        @if($shipment->estimated_arrival)
        <div style="font-size:6pt; color:#D40511; font-weight:bold; margin-top:1pt;">Est: {{ $shipment->estimated_arrival->format('d/m/Y') }}</div>
        @endif
    </td>
</tr>

{{-- SERVICE ROW --}}
<tr style="border-bottom:1.5pt solid #000; background:#f2f2f2;">
    <td style="padding:4pt 10pt; border-right:1pt solid #ccc; text-align:center;">
        <div class="sv-lbl">Service</div>
        <div class="sv-val">EXPRESS WW</div>
    </td>
    <td style="padding:4pt 10pt; border-right:1pt solid #ccc; text-align:center;">
        <div class="sv-lbl">Payor</div>
        <div class="sv-val">Shipper</div>
    </td>
    <td style="padding:4pt 10pt; text-align:center;">
        <div class="sv-lbl">DHL Account</div>
        <div class="sv-val" style="font-family:'Courier New',monospace; font-size:7.5pt;">{{ $acctNo }}</div>
    </td>
</tr>

{{-- CUSTOMS --}}
<tr style="border-bottom:1.5pt solid #000;">
    <td style="padding:6pt 10pt; border-right:1pt solid #ccc;">
        <span class="ci-lbl">Content Description</span>
        <span class="ci-val">{{ $shipment->content_description }}</span>
    </td>
    <td style="padding:6pt 10pt; border-right:1pt solid #ccc;">
        <span class="ci-lbl">HS Code</span>
        <span class="ci-val">2937.19</span>
    </td>
    <td style="padding:6pt 10pt;">
        <span class="ci-lbl">Declared Customs Value</span>
        <span class="ci-val">{{ $cur }} {{ number_format($goodsValue, 2) }}</span>
    </td>
</tr>

{{-- PAYMENT HEADER --}}
<tr>
    <td colspan="3" style="background:#1a1a1a; color:#fff; padding:4pt 12pt; font-size:6.5pt; font-weight:bold; text-transform:uppercase; letter-spacing:1.5pt; border-bottom:1pt solid #000;">
        Charges &amp; Payment Summary
    </td>
</tr>

{{-- DECLARED GOODS VALUE --}}
@if($goodsValue)
<tr>
    <td colspan="2" style="padding:5pt 12pt; border-bottom:1pt solid #e8e8e8; color:#888; font-size:7.5pt;">
        Declared Goods Value
        <span class="row-note">For customs declaration only — not a DHL charge</span>
    </td>
    <td style="padding:5pt 12pt; border-bottom:1pt solid #e8e8e8; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7.5pt; color:#888;">
        {{ $cur }}&nbsp;{{ number_format($goodsValue, 2) }}
    </td>
</tr>
@endif

{{-- SHIPPING FEE --}}
@if($shippingFee)
<tr>
    <td colspan="2" style="padding:5pt 12pt; border-bottom:1pt solid #e8e8e8; font-size:7.5pt;">
        DHL Express Freight Charge
        <span class="row-note">{{ $shipment->origin_service_area }} &rarr; {{ $shipment->dest_service_area }} &nbsp;&middot;&nbsp; {{ $shipment->service_type }}</span>
    </td>
    <td style="padding:5pt 12pt; border-bottom:1pt solid #e8e8e8; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7.5pt;">
        {{ $cur }}&nbsp;{{ number_format($shippingFee, 2) }}
    </td>
</tr>
@endif

{{-- INSURANCE --}}
@if($insuranceFee)
<tr>
    <td colspan="2" style="padding:5pt 12pt; border-bottom:1pt solid #e8e8e8; font-size:7.5pt;">
        Shipment Insurance (10% of declared value)
        <span class="row-note">Covers loss, theft &amp; damage in transit</span>
        @if($shipment->insurance_refundable)
            <span class="row-refund">&#10003; Refundable in full upon confirmed delivery</span>
        @endif
    </td>
    <td style="padding:5pt 12pt; border-bottom:1pt solid #e8e8e8; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7.5pt;">
        {{ $cur }}&nbsp;{{ number_format($insuranceFee, 2) }}
    </td>
</tr>
@endif

{{-- CUSTOMS DUTIES --}}
@if($customsDuties)
<tr>
    <td colspan="2" style="padding:5pt 12pt; border-bottom:1pt solid #e8e8e8; font-size:7.5pt;">
        Customs &amp; Import Duties
        <span class="row-note">EU import clearance &nbsp;&middot;&nbsp; Destination: {{ $shipment->receiver_country }} &nbsp;&middot;&nbsp; Payable before release</span>
    </td>
    <td style="padding:5pt 12pt; border-bottom:1pt solid #e8e8e8; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7.5pt;">
        {{ $cur }}&nbsp;{{ number_format($customsDuties, 2) }}
    </td>
</tr>
@endif

{{-- TOTAL --}}
<tr>
    <td colspan="2" style="background:#1a1a1a; color:#FFCC00; font-size:10pt; font-weight:bold; padding:7pt 12pt;">
        Total Amount Due
    </td>
    <td style="background:#1a1a1a; color:#FFCC00; font-size:10pt; font-weight:bold; padding:7pt 12pt; text-align:right; font-family:'Courier New',monospace;">
        {{ $cur }}&nbsp;{{ number_format($totalFeesDue, 2) }}
    </td>
</tr>

{{-- FOOTER --}}
<tr style="border-top:1.5pt solid #000; background:#fafafa;">
    <td colspan="2" style="padding:6pt 12pt;">
        <div class="ftr-corp">
            <strong>DHL Express (USA), Inc.</strong><br/>
            1200 S. Pine Island Road &nbsp;&middot;&nbsp; Plantation, FL 33324 &nbsp;&middot;&nbsp; USA<br/>
            Customer Service: 1-800-225-5345 &nbsp;&middot;&nbsp; dhl.com
        </div>
    </td>
    <td style="padding:6pt 12pt; vertical-align:bottom;">
        <div class="ftr-wb">{{ $masked }}</div>
        <div class="ftr-note">Retain this waybill as proof of shipment</div>
    </td>
</tr>

{{-- DISCLAIMER --}}
<tr style="border-top:1pt solid #ddd; background:#f5f5f5;">
    <td colspan="3" style="padding:5pt 12pt;">
        <div class="disc">
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

@php
    $waybill       = $shipment->waybill_number;
    $shipDate      = $shipment->ship_date->format('d M Y');
    $shipDateShort = $shipment->ship_date->format('d/m/Y');

    $wLen   = mb_strlen($waybill);
    $masked = mb_substr($waybill, 0, 3)
            . str_repeat('•', max(0, $wLen - 5))
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
        $w  = [1,1,1,2,2,3,1,2][rand(0,7)];
        $bg = $i % 2 === 0 ? '#000' : '#fff';
        $bars .= "<span style=\"display:inline-block;width:{$w}px;height:38px;background:{$bg};font-size:0;\"></span>";
    }
    srand();

    $weightKg    = (float)$shipment->weight_kg;
    $weightG     = $weightKg * 1000;
    $weightStr   = $weightG < 1000 ? number_format($weightG,0).' g' : number_format($weightKg,3).' kg';
    $weightLbStr = number_format(round($weightKg*2.20462,3),3).' lb';

    $acctNo  = strtoupper(substr(md5($waybill),0,8));
    $shipRef = strtoupper(substr(md5($waybill.$shipment->shipper_name),0,12));

    $receiverState = $shipment->receiver_state ? ', '.$shipment->receiver_state : '';
    $shipperState  = $shipment->shipper_state  ? ', '.$shipment->shipper_state  : '';

    $qrText = "DHL EXPRESS\nWaybill: {$waybill}\nStatus: PENDING\nInsurance: PENDING (Refundable on delivery)\nInsurance Fee: EUR ".number_format($insuranceFee,2)."\nCustoms Duties: EUR ".number_format($customsDuties,2)."\nTotal Fees Due: EUR ".number_format($totalFeesDue,2);
    $qrSvg  = (string)(new \SimpleSoftwareIO\QrCode\Generator)->format('svg')->size(72)->margin(1)->generate($qrText);
    $qrB64  = 'data:image/svg+xml;base64,'.base64_encode($qrSvg);

    // Masked chars rendered as bullet spans for PDF
    $maskedChars = mb_str_split($masked);
    $maskedBc = '';
    $maskedWb = '';
    $maskedFt = '';
    foreach ($maskedChars as $ch) {
        if ($ch === '•') {
            $maskedBc .= '<span style="color:#D40511;font-size:9pt;vertical-align:middle;line-height:.9;">&#8226;</span>';
            $maskedWb .= '<span style="color:#D40511;font-size:13pt;vertical-align:middle;line-height:.85;">&#8226;</span>';
            $maskedFt .= '<span style="color:#bbb;font-size:10pt;vertical-align:middle;">&#8226;</span>';
        } else {
            $maskedBc .= htmlspecialchars($ch);
            $maskedWb .= htmlspecialchars($ch);
            $maskedFt .= htmlspecialchars($ch);
        }
    }
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { margin: 0; size: 595pt 842pt; }
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Arial,Helvetica,sans-serif; font-size:7.5pt; color:#000; background:#fff; margin:0; padding:0; }
table { border-collapse:collapse; }
td { vertical-align:top; }
</style>
</head>
<body>
{{-- Outer table: fixed 3-column structure --}}
<table style="width:595pt; border:1pt solid #999; border-collapse:collapse;">

{{-- ▌ HEADER --}}
<tr style="border-bottom:2pt solid #000;">
    <td style="background:#D40511; width:78pt; padding:8pt 12pt; border-right:2pt solid #000; text-align:center; vertical-align:middle;">
        <span style="font-size:32pt; font-weight:bold; color:#FFCC00; letter-spacing:-2pt; line-height:1; font-family:Arial,Helvetica,sans-serif;">DHL</span>
    </td>
    <td style="padding:6pt 12pt; border-right:2pt solid #000; vertical-align:middle;">
        <div style="font-size:6pt; color:#D40511; font-weight:bold; text-transform:uppercase; letter-spacing:2pt; margin-bottom:2pt;">DHL Express</div>
        <div style="font-size:14pt; font-weight:bold; text-transform:uppercase; line-height:1; letter-spacing:.5pt;">{{ $shipment->service_type }}</div>
        <div style="font-size:6pt; color:#555; margin-top:3pt;">Time Definite International Delivery &nbsp;&middot;&nbsp; Door to Door</div>
    </td>
    <td style="background:#FFCC00; width:56pt; text-align:center; padding:7pt 10pt; vertical-align:middle;">
        <div style="font-size:28pt; font-weight:bold; line-height:1; color:#000;">{{ $shipment->product_code }}</div>
        <div style="font-size:5pt; font-weight:bold; text-transform:uppercase; color:#333; letter-spacing:.5pt; margin-top:2pt;">Product<br/>Code</div>
    </td>
</tr>

{{-- ▌ DESTINATION BANNER --}}
<tr style="background:#FFCC00; border-bottom:2pt solid #000;">
    <td colspan="2" style="padding:7pt 12pt; border-right:2pt solid #000; vertical-align:middle;">
        <div style="font-size:5.5pt; text-transform:uppercase; color:#555; letter-spacing:.5pt; margin-bottom:2pt;">Routing Code</div>
        <div style="font-size:18pt; font-weight:bold; letter-spacing:3pt; color:#000; line-height:1;">{{ $shipment->routing_code ?? ($shipment->origin_service_area.'-'.$shipment->dest_service_area) }}</div>
        <div style="font-size:6.5pt; color:#333; margin-top:3pt; font-weight:600;">{{ strtoupper($shipment->shipper_city) }}, {{ $shipment->shipper_country }} &nbsp;&rarr;&nbsp; {{ strtoupper($shipment->receiver_city) }}, {{ $shipment->receiver_country }}</div>
    </td>
    <td style="background:#000; text-align:center; vertical-align:middle; padding:8pt 10pt;">
        <div style="font-size:32pt; font-weight:bold; color:#FFCC00; line-height:1; letter-spacing:4pt;">{{ $shipment->dest_service_area }}</div>
        <div style="font-size:6pt; color:#FFCC00; margin-top:3pt; letter-spacing:1pt; text-align:center;">{{ strtoupper($shipment->receiver_city) }}</div>
    </td>
</tr>

{{-- ▌ BARCODE + WAYBILL + QR --}}
<tr style="border-bottom:2pt solid #000;">
    <td colspan="2" style="padding:9pt 12pt 7pt;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:52%; vertical-align:top;">
                    <div style="font-size:5.5pt; text-transform:uppercase; color:#888; letter-spacing:.5pt; margin-bottom:4pt;">Waybill Barcode &nbsp;&middot;&nbsp; Code 128</div>
                    <div style="height:38pt; overflow:hidden; white-space:nowrap;">{!! $bars !!}</div>
                    <div style="font-family:'Courier New',monospace; font-size:8.5pt; font-weight:bold; letter-spacing:4pt; margin-top:4pt; color:#000;">{!! $maskedBc !!}</div>
                </td>
                <td style="width:48%; border-left:2pt solid #eee; padding-left:12pt; vertical-align:top;">
                    <div style="font-size:5.5pt; text-transform:uppercase; color:#888; letter-spacing:.5pt; margin-bottom:2pt;">Waybill No.</div>
                    <div style="font-family:'Courier New',monospace; font-size:13pt; font-weight:bold; letter-spacing:2pt; line-height:1.15;">{!! $maskedWb !!}</div>
                    <div style="font-size:6.5pt; color:#444; margin-top:4pt; line-height:1.7;">
                        <strong>Ship Date:</strong> {{ $shipDate }}<br/>
                        @if($shipment->estimated_arrival)<strong style="color:#D40511;">Est. Delivery:</strong> {{ $shipment->estimated_arrival->format('d M Y') }}@endif
                    </div>
                </td>
            </tr>
        </table>
    </td>
    <td style="width:56pt; text-align:center; border-left:2pt solid #000; vertical-align:middle; padding:7pt 6pt;">
        <img src="{{ $qrB64 }}" width="60" height="60" style="display:block; margin:0 auto;"/>
        <div style="font-size:4.5pt; color:#888; margin-top:2pt; text-align:center; text-transform:uppercase; letter-spacing:.3pt;">Scan to Track</div>
    </td>
</tr>

{{-- ▌ ADDRESSES --}}
<tr style="border-bottom:2pt solid #000;">
    <td style="padding:9pt 12pt; border-right:2pt solid #000; width:185pt; vertical-align:top;">
        <div style="font-size:5.5pt; text-transform:uppercase; color:#fff; background:#000; font-weight:bold; letter-spacing:.5pt; padding:2pt 5pt; display:inline-block; margin-bottom:5pt;">Shipper</div>
        <div style="font-size:10pt; font-weight:bold; margin-bottom:2pt; line-height:1.2;">{{ $shipment->shipper_name }}</div>
        @if($shipment->shipper_company)<div style="font-size:8.5pt; font-weight:bold; margin-bottom:1pt; color:#222;">{{ $shipment->shipper_company }}</div>@endif
        <div style="font-size:8pt; line-height:1.55; color:#111;">{{ $shipment->shipper_address1 }}</div>
        @if($shipment->shipper_address2)<div style="font-size:8pt; line-height:1.55; color:#111;">{{ $shipment->shipper_address2 }}</div>@endif
        <div style="font-size:9.5pt; font-weight:bold; margin-top:3pt;">{{ strtoupper($shipment->shipper_city) }}{{ $shipperState }}</div>
        <div style="font-size:11pt; font-weight:bold; letter-spacing:2pt; margin-top:1pt;">{{ $shipment->shipper_postal }}</div>
        <div style="font-size:8pt; font-weight:bold; text-transform:uppercase; margin-top:1pt; color:#D40511;">{{ $shipment->shipper_country }}</div>
        @if($shipment->shipper_phone)<div style="font-size:7pt; color:#555; margin-top:3pt;">Tel: {{ $shipment->shipper_phone }}</div>@endif
    </td>
    <td colspan="2" style="padding:9pt 12pt; vertical-align:top;">
        <div style="font-size:5.5pt; text-transform:uppercase; color:#fff; background:#000; font-weight:bold; letter-spacing:.5pt; padding:2pt 5pt; display:inline-block; margin-bottom:5pt;">Consignee</div>
        <div style="font-size:12pt; font-weight:bold; margin-bottom:2pt; line-height:1.2;">{{ $shipment->receiver_name }}</div>
        @if($shipment->receiver_company)<div style="font-size:9.5pt; font-weight:bold; margin-bottom:1pt; color:#222;">{{ $shipment->receiver_company }}</div>@endif
        <div style="font-size:8pt; line-height:1.55; color:#111;">{{ $shipment->receiver_address1 }}</div>
        @if($shipment->receiver_address2)<div style="font-size:8pt; line-height:1.55; color:#111;">{{ $shipment->receiver_address2 }}</div>@endif
        <div style="font-size:10.5pt; font-weight:bold; margin-top:3pt;">{{ strtoupper($shipment->receiver_city) }}{{ $receiverState }}</div>
        <div style="font-size:13pt; font-weight:bold; letter-spacing:2pt; margin-top:1pt;">{{ $shipment->receiver_postal }}</div>
        <div style="font-size:8.5pt; font-weight:bold; text-transform:uppercase; margin-top:1pt; color:#D40511;">{{ $shipment->receiver_country }}</div>
        @if($shipment->receiver_phone)<div style="font-size:7pt; color:#555; margin-top:3pt;">Tel: {{ $shipment->receiver_phone }}</div>@endif
    </td>
</tr>

{{-- ▌ SENSITIVE STRIP --}}
@if($shipment->is_sensitive)
<tr style="border-bottom:2pt solid #000;">
    <td colspan="3" style="background:#D40511; color:#fff; padding:4pt 12pt; font-size:8pt; font-weight:bold; letter-spacing:2pt; text-transform:uppercase; vertical-align:middle;">
        ! &nbsp; SENSITIVE SHIPMENT
        <span style="font-size:6pt; font-weight:normal; opacity:.9; margin-left:8pt; letter-spacing:.5pt;">Handle with care &nbsp;&middot;&nbsp; Do not stack &nbsp;&middot;&nbsp; Special handling required at all transit points</span>
    </td>
</tr>
@endif

{{-- ▌ DETAILS BAR — inner 5-column table inside colspan=3 --}}
<tr style="border-bottom:1.5pt solid #000;">
    <td colspan="3" style="padding:0;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:20%; padding:5pt 9pt; border-right:1pt solid #ccc; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:2pt;">Gross Weight</div>
                    <div style="font-size:8.5pt; font-weight:bold;">{{ $weightStr }}</div>
                    <div style="font-size:5.5pt; color:#999; margin-top:1pt;">{{ $weightLbStr }}</div>
                </td>
                <td style="width:20%; padding:5pt 9pt; border-right:1pt solid #ccc; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:2pt;">Dimensions</div>
                    <div style="font-size:7.5pt; font-weight:bold;">{{ $shipment->dimensions ?? 'N/A' }}</div>
                </td>
                <td style="width:20%; padding:5pt 9pt; border-right:1pt solid #ccc; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:2pt;">Pieces</div>
                    <div style="font-size:8.5pt; font-weight:bold;">{{ $shipment->pieces }} / 1</div>
                </td>
                <td style="width:20%; padding:5pt 9pt; border-right:1pt solid #ccc; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:2pt;">Ship Date</div>
                    <div style="font-size:8pt; font-weight:bold;">{{ $shipDateShort }}</div>
                </td>
                <td style="width:20%; padding:5pt 9pt; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:2pt;">Est. Delivery</div>
                    @if($shipment->estimated_arrival)
                    <div style="font-size:8pt; font-weight:bold; color:#D40511;">{{ $shipment->estimated_arrival->format('d/m/Y') }}</div>
                    @else
                    <div style="font-size:8pt; font-weight:bold;">N/A</div>
                    @endif
                </td>
            </tr>
        </table>
    </td>
</tr>

{{-- ▌ SERVICE ROW — inner 5-column table --}}
<tr style="border-bottom:1.5pt solid #000; background:#f2f2f2;">
    <td colspan="3" style="padding:0; background:#f2f2f2;">
        <table style="width:100%; border-collapse:collapse; background:#f2f2f2;">
            <tr>
                <td style="width:20%; padding:4pt 9pt; border-right:1pt solid #ccc; text-align:center; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt;">Service</div>
                    <div style="font-size:8pt; font-weight:bold; margin-top:1pt;">EXPRESS WW</div>
                </td>
                <td style="width:20%; padding:4pt 9pt; border-right:1pt solid #ccc; text-align:center; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt;">Payor</div>
                    <div style="font-size:8pt; font-weight:bold; margin-top:1pt;">Shipper</div>
                </td>
                <td style="width:20%; padding:4pt 9pt; border-right:1pt solid #ccc; text-align:center; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt;">Incoterms</div>
                    <div style="font-size:8pt; font-weight:bold; margin-top:1pt;">DAP</div>
                </td>
                <td style="width:20%; padding:4pt 9pt; border-right:1pt solid #ccc; text-align:center; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt;">DHL Account</div>
                    <div style="font-size:7.5pt; font-weight:bold; margin-top:1pt; font-family:'Courier New',monospace;">{{ $acctNo }}</div>
                </td>
                <td style="width:20%; padding:4pt 9pt; text-align:center; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt;">Shipper Ref.</div>
                    <div style="font-size:6.5pt; font-weight:bold; margin-top:1pt; font-family:'Courier New',monospace;">{{ $shipRef }}</div>
                </td>
            </tr>
        </table>
    </td>
</tr>

{{-- ▌ CUSTOMS — inner 5-column table --}}
<tr style="border-bottom:1.5pt solid #000;">
    <td colspan="3" style="padding:0;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:22%; padding:5pt 9pt; border-right:1pt solid #ccc; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1pt;">Content Description</div>
                    <div style="font-weight:bold; font-size:7.5pt;">{{ $shipment->content_description }}</div>
                </td>
                <td style="width:14%; padding:5pt 9pt; border-right:1pt solid #ccc; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1pt;">HS Code</div>
                    <div style="font-weight:bold; font-size:7.5pt;">2937.19</div>
                </td>
                <td style="width:14%; padding:5pt 9pt; border-right:1pt solid #ccc; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1pt;">Currency</div>
                    <div style="font-weight:bold; font-size:7.5pt;">{{ $cur }}</div>
                </td>
                <td style="width:28%; padding:5pt 9pt; border-right:1pt solid #ccc; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1pt;">Declared Customs Value</div>
                    <div style="font-weight:bold; font-size:7.5pt;">{{ $cur }} {{ $goodsValue > 0 ? number_format($goodsValue,2) : 'N/A' }}</div>
                </td>
                <td style="width:22%; padding:5pt 9pt; vertical-align:middle;">
                    <div style="font-size:5pt; text-transform:uppercase; color:#888; letter-spacing:.3pt; margin-bottom:1pt;">Country of Origin</div>
                    <div style="font-weight:bold; font-size:7.5pt;">{{ $shipment->shipper_country }}</div>
                </td>
            </tr>
        </table>
    </td>
</tr>

{{-- ▌ CHARGES — inner 2-column table (label | amount) --}}
@if($goodsValue || $totalFeesDue)
<tr>
    <td colspan="3" style="background:#1a1a1a; color:#fff; padding:4pt 12pt; font-size:6.5pt; font-weight:bold; text-transform:uppercase; letter-spacing:1.5pt; border-bottom:1pt solid #000; vertical-align:middle;">
        Charges &amp; Payment Summary
        <span style="font-size:5.5pt; font-weight:normal; color:#aaa; letter-spacing:0; float:right;">All amounts due before release. Contact DHL for payment.</span>
    </td>
</tr>
<tr>
    <td colspan="3" style="padding:0;">
        <table style="width:100%; border-collapse:collapse;">
            @if($goodsValue)
            <tr style="border-bottom:1pt solid #e8e8e8;">
                <td style="padding:5pt 12pt; color:#888; font-size:7.5pt; vertical-align:middle;">
                    Declared Goods Value
                    <span style="font-size:5.5pt; color:#999; display:block; margin-top:1pt;">For customs declaration only — not a DHL charge</span>
                </td>
                <td style="width:100pt; padding:5pt 12pt; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7.5pt; color:#666; vertical-align:middle; white-space:nowrap;">
                    {{ $cur }}&nbsp;{{ number_format($goodsValue,2) }}
                </td>
            </tr>
            @endif
            @if($shippingFee)
            <tr style="border-bottom:1pt solid #e8e8e8;">
                <td style="padding:5pt 12pt; font-size:7.5pt; vertical-align:middle;">
                    DHL Express Freight Charge
                    <span style="font-size:5.5pt; color:#999; display:block; margin-top:1pt;">{{ $shipment->origin_service_area }} &rarr; {{ $shipment->dest_service_area }} &nbsp;&middot;&nbsp; {{ $shipment->service_type }}</span>
                </td>
                <td style="width:100pt; padding:5pt 12pt; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7.5pt; vertical-align:middle; white-space:nowrap;">
                    {{ $cur }}&nbsp;{{ number_format($shippingFee,2) }}
                </td>
            </tr>
            @endif
            @if($insuranceFee)
            <tr style="border-bottom:1pt solid #e8e8e8;">
                <td style="padding:5pt 12pt; font-size:7.5pt; vertical-align:middle;">
                    Shipment Insurance (10% of declared value)
                    <span style="font-size:5.5pt; color:#999; display:block; margin-top:1pt;">Covers loss, theft &amp; damage in transit</span>
                    @if($shipment->insurance_refundable)<span style="font-size:6pt; color:#1a7a3a; display:block; margin-top:1pt; font-weight:600;">Refundable in full upon confirmed delivery</span>@endif
                </td>
                <td style="width:100pt; padding:5pt 12pt; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7.5pt; vertical-align:middle; white-space:nowrap;">
                    {{ $cur }}&nbsp;{{ number_format($insuranceFee,2) }}
                </td>
            </tr>
            @endif
            @if($customsDuties)
            <tr style="border-bottom:1pt solid #e8e8e8;">
                <td style="padding:5pt 12pt; font-size:7.5pt; vertical-align:middle;">
                    Customs &amp; Import Duties
                    <span style="font-size:5.5pt; color:#999; display:block; margin-top:1pt;">EU import clearance &nbsp;&middot;&nbsp; Destination: {{ $shipment->receiver_country }} &nbsp;&middot;&nbsp; Payable before release</span>
                </td>
                <td style="width:100pt; padding:5pt 12pt; text-align:right; font-weight:bold; font-family:'Courier New',monospace; font-size:7.5pt; vertical-align:middle; white-space:nowrap;">
                    {{ $cur }}&nbsp;{{ number_format($customsDuties,2) }}
                </td>
            </tr>
            @endif
            <tr>
                <td style="background:#1a1a1a; color:#FFCC00; font-size:8pt; font-weight:bold; padding:7pt 12pt; letter-spacing:1pt; text-transform:uppercase; vertical-align:middle;">
                    Total Amount Due
                </td>
                <td style="width:100pt; background:#1a1a1a; color:#FFCC00; font-size:10pt; font-weight:bold; padding:7pt 12pt; text-align:right; font-family:'Courier New',monospace; vertical-align:middle; letter-spacing:.5pt; white-space:nowrap;">
                    {{ $cur }}&nbsp;{{ number_format($totalFeesDue,2) }}
                </td>
            </tr>
        </table>
    </td>
</tr>
@endif

{{-- ▌ FOOTER --}}
<tr style="border-top:2pt solid #000; background:#fafafa;">
    <td colspan="2" style="padding:7pt 12pt; vertical-align:middle;">
        <div style="font-size:6pt; color:#555; line-height:1.7;">
            <strong style="color:#000;">DHL Express (USA), Inc.</strong><br/>
            1200 S. Pine Island Road &nbsp;&middot;&nbsp; Plantation, FL 33324 &nbsp;&middot;&nbsp; USA<br/>
            Customer Service: 1-800-225-5345 &nbsp;&middot;&nbsp; dhl.com<br/>
            <span style="color:#999;">Printed: {{ now()->format('d M Y H:i') }} UTC</span>
        </div>
    </td>
    <td style="padding:7pt 12pt; vertical-align:bottom;">
        <div style="font-family:'Courier New',monospace; font-size:11pt; font-weight:bold; color:#D40511; letter-spacing:2pt; text-align:right;">{!! $maskedFt !!}</div>
        <div style="font-size:5pt; color:#999; margin-top:2pt; text-align:right;">Retain this waybill as proof of shipment</div>
    </td>
</tr>

{{-- ▌ DISCLAIMER --}}
<tr style="border-top:1pt solid #ddd; background:#f5f5f5;">
    <td colspan="3" style="padding:5pt 12pt;">
        <div style="font-size:5pt; color:#888; line-height:1.6;">
            By tendering this shipment, shipper agrees to DHL's Conditions of Carriage (available at dhl.com) and Tariff as applicable.
            Liability is limited under the Warsaw Convention / Montreal Convention and DHL's Standard Terms. All shipments are subject to inspection
            by customs authorities. Quote waybill number {!! $maskedBc !!} in all correspondence.
            Shipper's Ref: {{ $shipRef }}
        </div>
    </td>
</tr>

</table>
</body>
</html>

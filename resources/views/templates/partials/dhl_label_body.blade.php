@php
    $waybill       = $shipment->waybill_number;
    $shipDate      = $shipment->ship_date->format('d M Y');
    $shipDateShort = $shipment->ship_date->format('d/m/Y');

    // Telegram-style masked tracking — first 3, masked middle, last 2
    $wLen   = mb_strlen($waybill);
    $masked = mb_substr($waybill, 0, 3)
            . str_repeat('•', max(0, $wLen - 5))
            . mb_substr($waybill, -2);

    // Waybill formatted as XXXX XXXX XX (DHL standard grouping)
    $waybillGrouped = implode(' ', [
        substr($waybill, 0, 4),
        substr($waybill, 4, 4),
        substr($waybill, 8),
    ]);

    // Fees
    $goodsValue    = (float)($shipment->goods_value   ?? 0);
    $insuranceFee  = (float)($shipment->insurance_fee ?? 0);
    $customsDuties = (float)($shipment->customs_duties ?? 0);
    $shippingFee   = (float)($shipment->shipping_fee  ?? 0);
    $totalFeesDue  = $shippingFee + $insuranceFee + $customsDuties;
    $cur           = $shipment->shipping_fee_currency ?? $shipment->currency ?? 'EUR';

    // Deterministic barcode pattern seeded from waybill
    srand(crc32($waybill));
    $barcodePattern = [];
    for ($i = 0; $i < 80; $i++) {
        $barcodePattern[] = [1, 1, 1, 2, 2, 3, 1, 2][rand(0, 7)];
    }
    srand();

    // Weight display
    $weightKg  = (float)$shipment->weight_kg;
    $weightG   = $weightKg * 1000;
    $weightLb  = round($weightKg * 2.20462, 3);
    $weightStr = $weightG < 1000
        ? number_format($weightG, 0) . ' g'
        : number_format($weightKg, 3) . ' kg';
    $weightLbStr = number_format($weightLb, 3) . ' lb';

    $receiverState = $shipment->receiver_state ? ', ' . $shipment->receiver_state : '';
    $shipperState  = $shipment->shipper_state  ? ', ' . $shipment->shipper_state  : '';

    // Account number (deterministic)
    $acctNo = strtoupper(substr(md5($waybill), 0, 8));
    // Shipper reference
    $shipRef = strtoupper(substr(md5($waybill . $shipment->shipper_name), 0, 12));

    // Real QR code — encodes shipment status message
    $qrText = "DHL EXPRESS\nWaybill: {$waybill}\nStatus: PENDING\nInsurance: PENDING (Refundable on delivery)\nInsurance Fee: EUR " . number_format($insuranceFee, 2) . "\nCustoms Duties: EUR " . number_format($customsDuties, 2) . "\nTotal Fees Due: EUR " . number_format($totalFeesDue, 2);
    $qrSvg = \SimpleSoftware\QrCode\Facades\QrCode::format('svg')->size(120)->margin(1)->generate($qrText);
@endphp
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body,html{background:#e8e8e8;}

/* ── OUTER CONTAINER ─────────────────────────────────── */
.lbl{
    max-width:680px;
    width:100%;
    font-family:Arial,Helvetica,sans-serif;
    font-size:9pt;
    color:#000;
    background:#fff;
    border:1px solid #aaa;
    box-shadow:2px 2px 8px rgba(0,0,0,.18);
}

/* ── HEADER ─────────────────────────────────────────── */
.lbl-hdr{
    display:flex;
    border-bottom:3px solid #000;
}
.lbl-logo-cell{
    background:#D40511;
    padding:10px 18px;
    display:flex;align-items:center;justify-content:center;
    border-right:3px solid #000;
    flex-shrink:0;
    min-width:100px;
}
.lbl-logo{
    font-size:44px;font-weight:900;
    color:#FFCC00;
    letter-spacing:-3px;
    font-family:'Arial Black',Arial,sans-serif;
    line-height:1;
}
.lbl-svc-cell{
    flex:1;
    padding:8px 14px;
    display:flex;flex-direction:column;justify-content:center;
    border-right:3px solid #000;
}
.lbl-svc-sub{font-size:6.5pt;color:#D40511;font-weight:700;text-transform:uppercase;letter-spacing:2px;margin-bottom:2px;}
.lbl-svc-name{font-size:16pt;font-weight:900;text-transform:uppercase;line-height:1;letter-spacing:.5px;}
.lbl-svc-desc{font-size:6.5pt;color:#555;margin-top:3px;}
.lbl-prod-cell{
    background:#FFCC00;
    padding:8px 14px;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    text-align:center;
    flex-shrink:0;
    min-width:62px;
}
.lbl-prod-ltr{font-size:36pt;font-weight:900;line-height:1;color:#000;}
.lbl-prod-tag{font-size:5.5pt;font-weight:700;text-transform:uppercase;color:#333;letter-spacing:.5px;margin-top:2px;}

/* ── DESTINATION BANNER ─────────────────────────────── */
.lbl-dest-banner{
    background:#FFCC00;
    border-bottom:3px solid #000;
    display:flex;
    align-items:stretch;
}
.lbl-routing-cell{
    flex:1;
    padding:8px 14px;
    border-right:3px solid #000;
}
.lbl-route-tag{font-size:6pt;text-transform:uppercase;color:#555;letter-spacing:.5px;margin-bottom:3px;}
.lbl-route-code{font-size:22pt;font-weight:900;letter-spacing:3px;color:#000;line-height:1;}
.lbl-route-cities{font-size:7pt;color:#333;margin-top:4px;font-weight:600;}
.lbl-dest-cell{
    background:#000;
    padding:10px 20px;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    min-width:100px;
    flex-shrink:0;
}
.lbl-dest-code{
    font-size:42pt;font-weight:900;
    color:#FFCC00;
    line-height:1;
    letter-spacing:4px;
    font-family:'Arial Black',Arial,sans-serif;
}
.lbl-dest-name{font-size:7pt;color:#FFCC00;margin-top:3px;letter-spacing:1px;text-align:center;}

/* ── BARCODE SECTION ────────────────────────────────── */
.lbl-bc-section{
    border-bottom:3px solid #000;
    padding:12px 14px 10px;
    display:flex;align-items:flex-start;gap:14px;
    flex-wrap:wrap;
}
.lbl-bc-main{flex:1;min-width:200px;}
.lbl-bc-tag{font-size:6pt;text-transform:uppercase;color:#888;letter-spacing:.5px;margin-bottom:5px;}
.lbl-bars{display:flex;align-items:stretch;height:64px;overflow:hidden;}
.lbl-bar{display:inline-block;background:#000;flex-shrink:0;}
.lbl-gap{display:inline-block;background:#fff;flex-shrink:0;}
.lbl-bc-num{
    font-family:'Courier New',monospace;
    font-size:9.5pt;font-weight:700;
    letter-spacing:4px;
    margin-top:5px;
    color:#000;
}
.lbl-wb-group{
    flex-shrink:0;
    border-left:2px solid #eee;
    padding-left:14px;
    min-width:140px;
}
.lbl-wb-tag{font-size:6pt;text-transform:uppercase;color:#888;letter-spacing:.5px;margin-bottom:3px;}
.lbl-wb-num{
    font-family:'Courier New',monospace;
    font-size:15pt;font-weight:900;
    letter-spacing:2px;line-height:1.15;
}
.lbl-wb-dates{font-size:7pt;color:#444;margin-top:5px;line-height:1.7;}
.lbl-wb-dates strong{color:#000;}
.lbl-qr-cell{
    flex-shrink:0;
    display:flex;flex-direction:column;align-items:center;
}
.lbl-qr{
    width:80px;height:80px;
}
.lbl-qr svg{width:100%;height:100%;display:block;}
.lbl-qr-tag{font-size:5pt;color:#888;margin-top:3px;text-align:center;text-transform:uppercase;letter-spacing:.3px;}

/* ── ADDRESSES ──────────────────────────────────────── */
.lbl-addrs{
    display:flex;
    border-bottom:3px solid #000;
    flex-wrap:wrap;
}
.lbl-from{
    flex:1;
    min-width:200px;
    padding:10px 14px;
    border-right:2px solid #000;
}
@media(max-width:440px){.lbl-from{border-right:none;border-bottom:2px solid #000;}}
.lbl-to{
    flex:1.4;
    min-width:200px;
    padding:10px 14px;
}
.addr-cap{
    font-size:6pt;text-transform:uppercase;color:#fff;
    background:#000;
    font-weight:700;letter-spacing:.5px;
    padding:2px 6px;
    display:inline-block;
    margin-bottom:6px;
}
.addr-name{font-size:10.5pt;font-weight:900;margin-bottom:2px;line-height:1.2;}
.addr-company{font-size:9pt;font-weight:700;margin-bottom:1px;color:#222;}
.addr-line{font-size:8.5pt;line-height:1.55;color:#111;}
.addr-city{font-size:10pt;font-weight:700;margin-top:4px;}
.addr-postal{font-size:12pt;font-weight:900;letter-spacing:2px;margin-top:1px;}
.addr-country{font-size:8.5pt;font-weight:700;text-transform:uppercase;margin-top:1px;color:#D40511;}
.addr-phone{font-size:7.5pt;color:#555;margin-top:4px;}

/* TO block: slightly larger */
.lbl-to .addr-name{font-size:12pt;}
.lbl-to .addr-company{font-size:10pt;}
.lbl-to .addr-city{font-size:11pt;}
.lbl-to .addr-postal{font-size:14pt;}
.lbl-to .addr-country{font-size:9pt;}

/* ── SENSITIVE STRIP ────────────────────────────────── */
.lbl-sensitive{
    background:#D40511;color:#fff;
    padding:5px 14px;
    font-size:8.5pt;font-weight:900;
    letter-spacing:2px;text-transform:uppercase;
    border-bottom:3px solid #000;
    display:flex;align-items:center;gap:10px;
}
.lbl-sensitive::before{
    content:"!";
    display:inline-flex;align-items:center;justify-content:center;
    width:18px;height:18px;
    border:2px solid #fff;
    border-radius:50%;
    font-size:11pt;font-weight:900;
    flex-shrink:0;
    line-height:1;
}
.lbl-sensitive-sub{font-size:6.5pt;font-weight:400;opacity:.9;margin-left:6px;letter-spacing:.5px;}

/* ── DETAILS BAR ────────────────────────────────────── */
.lbl-details{
    display:flex;
    border-bottom:2px solid #000;
    flex-wrap:wrap;
}
.lbl-dc{
    flex:1;min-width:90px;
    padding:6px 10px;
    border-right:1px solid #ccc;
}
.lbl-dc:last-child{border-right:none;}
@media(max-width:480px){.lbl-dc{min-width:45%;}}
.dc-lbl{font-size:5.5pt;text-transform:uppercase;color:#888;letter-spacing:.3px;margin-bottom:2px;}
.dc-val{font-size:9pt;font-weight:700;}
.dc-sub{font-size:6pt;color:#999;margin-top:1px;}

/* ── SERVICE / CUSTOMS ROW ──────────────────────────── */
.lbl-svcrow{
    display:flex;
    border-bottom:2px solid #000;
    background:#f2f2f2;
    flex-wrap:wrap;
}
.lbl-sv{
    flex:1;min-width:80px;
    padding:5px 10px;
    border-right:1px solid #ccc;
    text-align:center;
}
.lbl-sv:last-child{border-right:none;}
.sv-lbl{font-size:5.5pt;text-transform:uppercase;color:#888;letter-spacing:.3px;}
.sv-val{font-size:8.5pt;font-weight:700;margin-top:1px;}

/* ── CUSTOMS DESCRIPTION ────────────────────────────── */
.lbl-customs{
    padding:7px 14px;
    border-bottom:2px solid #000;
    display:flex;gap:20px;flex-wrap:wrap;font-size:7.5pt;
}
.ci label{
    font-size:5.5pt;color:#888;text-transform:uppercase;
    display:block;letter-spacing:.3px;margin-bottom:1px;
}
.ci span{font-weight:700;color:#000;}

/* ── PAYMENT / FINANCIAL SECTION ────────────────────── */
.lbl-pay-hdr{
    background:#1a1a1a;color:#fff;
    padding:5px 14px;
    font-size:7pt;font-weight:700;
    text-transform:uppercase;letter-spacing:1.5px;
    border-bottom:1px solid #000;
    display:flex;justify-content:space-between;align-items:center;
}
.lbl-pay-notice{font-size:6pt;color:#aaa;font-weight:400;letter-spacing:0;}
.lbl-pay-tbl{width:100%;border-collapse:collapse;font-size:8pt;}
.lbl-pay-tbl tr{border-bottom:1px solid #e8e8e8;}
.lbl-pay-tbl td{padding:6px 14px;vertical-align:top;}
.lbl-pay-tbl td:last-child{text-align:right;font-weight:700;white-space:nowrap;font-family:'Courier New',monospace;}
.row-ref td{color:#888;}
.row-ref td:last-child{color:#666;}
.row-note{font-size:6pt;color:#999;display:block;margin-top:1px;font-weight:400;}
.row-refund{font-size:6.5pt;color:#1a7a3a;display:block;margin-top:2px;font-weight:600;}
.row-total td{
    background:#1a1a1a;color:#FFCC00;
    font-size:10.5pt;font-weight:900;
    border-bottom:none;padding:8px 14px;
    letter-spacing:.5px;
}
.row-total td:first-child{letter-spacing:1px;text-transform:uppercase;font-size:8pt;}

/* ── FOOTER ─────────────────────────────────────────── */
.lbl-ftr{
    padding:8px 14px;
    border-top:2px solid #000;
    display:flex;justify-content:space-between;align-items:flex-end;
    flex-wrap:wrap;gap:8px;
    background:#fafafa;
}
.ftr-corp{font-size:6.5pt;color:#555;line-height:1.7;}
.ftr-corp strong{color:#000;}
.ftr-wb{
    font-family:'Courier New',monospace;
    font-size:13pt;font-weight:900;
    color:#D40511;letter-spacing:2px;
    text-align:right;
}
.ftr-note{font-size:5.5pt;color:#999;margin-top:2px;text-align:right;}

/* ── DISCLAIMER ─────────────────────────────────────── */
.lbl-disc{
    padding:6px 14px;
    font-size:5.5pt;color:#888;line-height:1.6;
    border-top:1px solid #ddd;
    background:#f5f5f5;
}

@media(max-width:520px){
    .lbl-logo{font-size:32px;}
    .lbl-svc-name{font-size:12pt;}
    .lbl-dest-code{font-size:28pt;}
    .lbl-route-code{font-size:16pt;}
    .lbl-prod-ltr{font-size:26pt;}
    .lbl-wb-num{font-size:11pt;}
}
</style>

<div class="lbl">

{{-- ── HEADER ──────────────────────────────────────────────── --}}
<div class="lbl-hdr">
    <div class="lbl-logo-cell">
        <span class="lbl-logo">DHL</span>
    </div>
    <div class="lbl-svc-cell">
        <div class="lbl-svc-sub">DHL Express</div>
        <div class="lbl-svc-name">{{ $shipment->service_type }}</div>
        <div class="lbl-svc-desc">Time Definite International Delivery &nbsp;·&nbsp; Door to Door</div>
    </div>
    <div class="lbl-prod-cell">
        <div class="lbl-prod-ltr">{{ $shipment->product_code }}</div>
        <div class="lbl-prod-tag">Product<br>Code</div>
    </div>
</div>

{{-- ── DESTINATION BANNER ───────────────────────────────────── --}}
<div class="lbl-dest-banner">
    <div class="lbl-routing-cell">
        <div class="lbl-route-tag">Routing Code</div>
        <div class="lbl-route-code">{{ $shipment->routing_code ?? ($shipment->origin_service_area . '-' . $shipment->dest_service_area) }}</div>
        <div class="lbl-route-cities">
            {{ strtoupper($shipment->shipper_city) }}, {{ $shipment->shipper_country }}
            &nbsp;&rarr;&nbsp;
            {{ strtoupper($shipment->receiver_city) }}, {{ $shipment->receiver_country }}
        </div>
    </div>
    <div class="lbl-dest-cell">
        <div class="lbl-dest-code">{{ $shipment->dest_service_area }}</div>
        <div class="lbl-dest-name">{{ strtoupper($shipment->receiver_city) }}</div>
    </div>
</div>

{{-- ── BARCODE + WAYBILL + QR ──────────────────────────────── --}}
<div class="lbl-bc-section">
    <div class="lbl-bc-main">
        <div class="lbl-bc-tag">Waybill Barcode &nbsp;·&nbsp; Code 128</div>
        <div class="lbl-bars">
            @foreach($barcodePattern as $width)
                @php $idx = $loop->index; @endphp
                @if($idx % 2 === 0)
                    <div class="lbl-bar" style="width:{{ $width }}px;"></div>
                @else
                    <div class="lbl-gap" style="width:{{ $width }}px;"></div>
                @endif
            @endforeach
        </div>
        <div class="lbl-bc-num">
            @foreach(mb_str_split($masked) as $ch)
                @if($ch==='•')<span style="color:#D40511;font-size:11pt;vertical-align:middle;line-height:.9;">•</span>@else{{ $ch }}@endif
            @endforeach
        </div>
    </div>

    <div class="lbl-wb-group">
        <div class="lbl-wb-tag">Waybill No.</div>
        <div class="lbl-wb-num">
            @foreach(mb_str_split($masked) as $ch)
                @if($ch==='•')<span style="color:#D40511;font-size:17pt;vertical-align:middle;line-height:.85;">•</span>@else{{ $ch }}@endif
            @endforeach
        </div>
        <div class="lbl-wb-dates">
            <strong>Ship Date:</strong> {{ $shipDate }}<br>
            @if($shipment->estimated_arrival)
            <strong style="color:#D40511;">Est. Delivery:</strong> {{ $shipment->estimated_arrival->format('d M Y') }}
            @endif
        </div>
    </div>

    <div class="lbl-qr-cell">
        <div class="lbl-qr">{!! $qrSvg !!}</div>
        <div class="lbl-qr-tag">Scan to Track</div>
    </div>
</div>

{{-- ── ADDRESSES ────────────────────────────────────────────── --}}
<div class="lbl-addrs">
    <div class="lbl-from">
        <div class="addr-cap">Shipper</div>
        <div class="addr-name">{{ $shipment->shipper_name }}</div>
        @if($shipment->shipper_company)
            <div class="addr-company">{{ $shipment->shipper_company }}</div>
        @endif
        <div class="addr-line">{{ $shipment->shipper_address1 }}</div>
        @if($shipment->shipper_address2)
            <div class="addr-line">{{ $shipment->shipper_address2 }}</div>
        @endif
        <div class="addr-city">{{ strtoupper($shipment->shipper_city) }}{{ $shipperState }}</div>
        <div class="addr-postal">{{ $shipment->shipper_postal }}</div>
        <div class="addr-country">{{ $shipment->shipper_country }}</div>
        @if($shipment->shipper_phone)
            <div class="addr-phone">Tel: {{ $shipment->shipper_phone }}</div>
        @endif
    </div>
    <div class="lbl-to">
        <div class="addr-cap">Consignee</div>
        <div class="addr-name">{{ $shipment->receiver_name }}</div>
        @if($shipment->receiver_company)
            <div class="addr-company">{{ $shipment->receiver_company }}</div>
        @endif
        <div class="addr-line">{{ $shipment->receiver_address1 }}</div>
        @if($shipment->receiver_address2)
            <div class="addr-line">{{ $shipment->receiver_address2 }}</div>
        @endif
        <div class="addr-city">{{ strtoupper($shipment->receiver_city) }}{{ $receiverState }}</div>
        <div class="addr-postal">{{ $shipment->receiver_postal }}</div>
        <div class="addr-country">{{ $shipment->receiver_country }}</div>
        @if($shipment->receiver_phone)
            <div class="addr-phone">Tel: {{ $shipment->receiver_phone }}</div>
        @endif
    </div>
</div>

{{-- ── SENSITIVE STRIP ──────────────────────────────────────── --}}
@if($shipment->is_sensitive)
<div class="lbl-sensitive">
    SENSITIVE SHIPMENT
    <span class="lbl-sensitive-sub">Handle with care &nbsp;·&nbsp; Do not stack &nbsp;·&nbsp; Special handling required at all transit points</span>
</div>
@endif

{{-- ── DETAILS BAR ──────────────────────────────────────────── --}}
<div class="lbl-details">
    <div class="lbl-dc">
        <div class="dc-lbl">Gross Weight</div>
        <div class="dc-val">{{ $weightStr }}</div>
        <div class="dc-sub">{{ $weightLbStr }}</div>
    </div>
    <div class="lbl-dc">
        <div class="dc-lbl">Dimensions</div>
        <div class="dc-val" style="font-size:8pt;">{{ $shipment->dimensions ?? 'N/A' }}</div>
    </div>
    <div class="lbl-dc">
        <div class="dc-lbl">Pieces</div>
        <div class="dc-val">{{ $shipment->pieces }}&nbsp;/&nbsp;1</div>
    </div>
    <div class="lbl-dc">
        <div class="dc-lbl">Ship Date</div>
        <div class="dc-val" style="font-size:8.5pt;">{{ $shipDateShort }}</div>
    </div>
    @if($shipment->estimated_arrival)
    <div class="lbl-dc">
        <div class="dc-lbl">Est. Delivery</div>
        <div class="dc-val" style="font-size:8.5pt;color:#D40511;">{{ $shipment->estimated_arrival->format('d/m/Y') }}</div>
    </div>
    @endif
</div>

{{-- ── SERVICE ROW ──────────────────────────────────────────── --}}
<div class="lbl-svcrow">
    <div class="lbl-sv">
        <div class="sv-lbl">Service</div>
        <div class="sv-val">EXPRESS WW</div>
    </div>
    <div class="lbl-sv">
        <div class="sv-lbl">Payor</div>
        <div class="sv-val">Shipper</div>
    </div>
    <div class="lbl-sv">
        <div class="sv-lbl">Incoterms</div>
        <div class="sv-val">DAP</div>
    </div>
    <div class="lbl-sv">
        <div class="sv-lbl">DHL Account</div>
        <div class="sv-val" style="font-family:'Courier New',monospace;">{{ $acctNo }}</div>
    </div>
    <div class="lbl-sv">
        <div class="sv-lbl">Shipper Ref.</div>
        <div class="sv-val" style="font-family:'Courier New',monospace;font-size:7.5pt;">{{ $shipRef }}</div>
    </div>
</div>

{{-- ── CUSTOMS ──────────────────────────────────────────────── --}}
<div class="lbl-customs">
    <div class="ci">
        <label>Content Description</label>
        <span>{{ $shipment->content_description }}</span>
    </div>
    <div class="ci">
        <label>HS Code</label>
        <span>2937.19</span>
    </div>
    <div class="ci">
        <label>Currency</label>
        <span>{{ $cur }}</span>
    </div>
    <div class="ci">
        <label>Declared Customs Value</label>
        <span>{{ $cur }} {{ $goodsValue > 0 ? number_format($goodsValue, 2) : 'N/A' }}</span>
    </div>
    <div class="ci">
        <label>Country of Origin</label>
        <span>{{ $shipment->shipper_country }}</span>
    </div>
</div>

{{-- ── PAYMENT & CHARGES ────────────────────────────────────── --}}
@if($goodsValue || $totalFeesDue)
<div>
    <div class="lbl-pay-hdr">
        Charges &amp; Payment Summary
        <span class="lbl-pay-notice">All amounts due before release. Contact DHL for payment.</span>
    </div>
    <table class="lbl-pay-tbl">
        @if($goodsValue)
        <tr class="row-ref">
            <td>
                Declared Goods Value
                <span class="row-note">For customs declaration only — not a DHL charge</span>
            </td>
            <td>{{ $cur }}&nbsp;{{ number_format($goodsValue, 2) }}</td>
        </tr>
        @endif
        @if($shippingFee)
        <tr>
            <td>
                DHL Express Freight Charge
                <span class="row-note">{{ $shipment->origin_service_area }} &rarr; {{ $shipment->dest_service_area }} &nbsp;&middot;&nbsp; {{ $shipment->service_type }}</span>
            </td>
            <td>{{ $cur }}&nbsp;{{ number_format($shippingFee, 2) }}</td>
        </tr>
        @endif
        @if($insuranceFee)
        <tr>
            <td>
                Shipment Insurance (10% of declared value)
                <span class="row-note">Covers loss, theft &amp; damage in transit</span>
                @if($shipment->insurance_refundable)
                    <span class="row-refund">Refundable in full upon confirmed delivery</span>
                @endif
            </td>
            <td>{{ $cur }}&nbsp;{{ number_format($insuranceFee, 2) }}</td>
        </tr>
        @endif
        @if($customsDuties)
        <tr>
            <td>
                Customs &amp; Import Duties
                <span class="row-note">EU import clearance &nbsp;&middot;&nbsp; Destination: {{ $shipment->receiver_country }} &nbsp;&middot;&nbsp; Payable before release</span>
            </td>
            <td>{{ $cur }}&nbsp;{{ number_format($customsDuties, 2) }}</td>
        </tr>
        @endif
        <tr class="row-total">
            <td>Total Amount Due</td>
            <td>{{ $cur }}&nbsp;{{ number_format($totalFeesDue, 2) }}</td>
        </tr>
    </table>
</div>
@endif

{{-- ── FOOTER ───────────────────────────────────────────────── --}}
<div class="lbl-ftr">
    <div class="ftr-corp">
        <strong>DHL Express (USA), Inc.</strong><br>
        1200 S. Pine Island Road &nbsp;·&nbsp; Plantation, FL 33324 &nbsp;·&nbsp; USA<br>
        Customer Service: 1-800-225-5345 &nbsp;·&nbsp; dhl.com<br>
        <span style="color:#999;">Printed: {{ now()->format('d M Y H:i') }} UTC</span>
    </div>
    <div>
        <div class="ftr-wb">
            @foreach(mb_str_split($masked) as $ch)
                @if($ch==='•')<span style="color:#bbb;font-size:12pt;vertical-align:middle;">•</span>@else{{ $ch }}@endif
            @endforeach
        </div>
        <div class="ftr-note">Retain this waybill as proof of shipment</div>
    </div>
</div>

{{-- ── DISCLAIMER ───────────────────────────────────────────── --}}
<div class="lbl-disc">
    By tendering this shipment, shipper agrees to DHL's Conditions of Carriage (available at dhl.com) and Tariff as applicable.
    Liability is limited under the Warsaw Convention / Montreal Convention and DHL's Standard Terms. All shipments are subject to inspection
    by customs authorities. Quote waybill number {{ $masked }} in all correspondence.
    Shipper's Ref: {{ $shipRef }}
</div>

</div>

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

    // Fees (goods value is reference only, not a fee)
    $goodsValue    = (float)($shipment->goods_value   ?? 0);
    $insuranceFee  = (float)($shipment->insurance_fee ?? 0);
    $customsDuties = (float)($shipment->customs_duties ?? 0);
    $shippingFee   = (float)($shipment->shipping_fee  ?? 0);
    $totalFeesDue  = $shippingFee + $insuranceFee + $customsDuties;
    $cur           = $shipment->shipping_fee_currency ?? $shipment->currency ?? 'EUR';

    $statusMap = [
        'pending'    => ['bg' => '#f39c12', 'text' => '#fff', 'label' => 'PENDING'],
        'in_transit' => ['bg' => '#2980b9', 'text' => '#fff', 'label' => 'IN TRANSIT'],
        'delivered'  => ['bg' => '#27ae60', 'text' => '#fff', 'label' => 'DELIVERED'],
        'failed'     => ['bg' => '#c0392b', 'text' => '#fff', 'label' => 'FAILED'],
    ];
    $st = $statusMap[$shipment->status ?? 'pending'] ?? $statusMap['pending'];

    // Deterministic barcode pattern seeded from waybill
    srand(crc32($waybill));
    $barcodePattern = [];
    for ($i = 0; $i < 68; $i++) {
        $barcodePattern[] = [1, 1, 2, 2, 3, 1, 1, 2][rand(0, 7)];
    }
    srand(); // restore randomness

    // Weight display
    $weightKg  = (float)$shipment->weight_kg;
    $weightG   = $weightKg * 1000;
    $weightLb  = round($weightKg * 2.20462, 3);
    $weightStr = $weightG < 1000
        ? number_format($weightG, 0) . ' g / ' . $weightLb . ' lb'
        : number_format($weightKg, 2) . ' kg / ' . $weightLb . ' lb';

    $receiverState = $shipment->receiver_state ? ', ' . $shipment->receiver_state : '';
    $shipperState  = $shipment->shipper_state  ? ', ' . $shipment->shipper_state  : '';
@endphp
<!--- DHL Label Body --->
<style>
*{box-sizing:border-box;margin:0;padding:0;}
.dhl-wrap{
    max-width:700px;
    width:100%;
    font-family:Arial,Helvetica,sans-serif;
    font-size:9pt;
    color:#000;
    background:#fff;
    border:2.5px solid #000;
}

/* ── HEADER ─────────────────────────────────────────── */
.dhl-header{display:flex;align-items:stretch;border-bottom:2.5px solid #000;}
.dhl-logo-cell{
    background:#D40511;
    min-width:110px;
    display:flex;align-items:center;justify-content:center;
    padding:10px 16px;
    border-right:2.5px solid #000;
    flex-shrink:0;
}
.dhl-logo{
    font-size:46px;font-weight:900;
    color:#FFCC00;
    letter-spacing:-2px;
    font-family:'Arial Black',Arial,sans-serif;
    line-height:1;
    text-shadow:1px 1px 0 rgba(0,0,0,.2);
}
.dhl-service-cell{
    flex:1;padding:8px 12px;
    display:flex;flex-direction:column;justify-content:center;
}
.dhl-express-label{
    font-size:7pt;font-weight:700;color:#D40511;
    text-transform:uppercase;letter-spacing:2px;
    border-bottom:1px solid #eee;padding-bottom:3px;margin-bottom:4px;
}
.dhl-service-name{
    font-size:15pt;font-weight:900;color:#000;
    text-transform:uppercase;line-height:1.1;
}
.dhl-service-sub{font-size:7pt;color:#666;margin-top:2px;}
.dhl-product-cell{
    background:#FFCC00;
    border-left:2.5px solid #000;
    min-width:64px;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    padding:8px 12px;text-align:center;flex-shrink:0;
}
.dhl-product-letter{font-size:38pt;font-weight:900;color:#000;line-height:1;}
.dhl-product-sub{font-size:6pt;font-weight:700;text-transform:uppercase;color:#333;margin-top:2px;letter-spacing:.5px;}

/* ── STATUS BAR ─────────────────────────────────────── */
.dhl-status-bar{
    display:flex;align-items:center;justify-content:space-between;
    padding:6px 12px;border-bottom:2px solid #000;
    background:#f8f8f8;
    flex-wrap:wrap;gap:8px;
}
.dhl-status-pill{
    display:inline-flex;align-items:center;gap:6px;
    padding:4px 14px;border-radius:20px;
    font-size:8pt;font-weight:900;letter-spacing:1.5px;
}
.dhl-status-dot{
    width:7px;height:7px;border-radius:50%;
    background:rgba(255,255,255,.85);
}
.dhl-tracking-area{text-align:right;}
.dhl-tracking-label{font-size:6pt;color:#999;text-transform:uppercase;letter-spacing:.5px;}
.dhl-tracking-num{
    font-family:'Courier New',monospace;
    font-size:14pt;font-weight:900;letter-spacing:4px;color:#000;
    display:block;margin-top:1px;
}
.dhl-tracking-hint{font-size:6pt;color:#bbb;display:block;margin-top:1px;}

/* ── WAYBILL / BARCODE ──────────────────────────────── */
.dhl-waybill-section{
    border-bottom:2.5px solid #000;
    padding:10px 12px 8px;
    display:flex;align-items:flex-start;gap:12px;
    flex-wrap:wrap;
}
.dhl-waybill-left{min-width:160px;flex-shrink:0;}
.dhl-wb-tag{font-size:6.5pt;color:#888;text-transform:uppercase;letter-spacing:.5px;}
.dhl-wb-number{
    font-size:17pt;font-weight:900;letter-spacing:2px;
    color:#000;line-height:1.1;margin:2px 0;
    font-family:'Courier New',monospace;
}
.dhl-wb-date{font-size:7pt;color:#666;margin-top:2px;}
.dhl-barcode-wrap{flex:1;min-width:160px;display:flex;flex-direction:column;align-items:center;}
.dhl-bars{display:flex;align-items:stretch;height:56px;}
.dhl-bar{display:inline-block;background:#000;}
.dhl-gap{display:inline-block;background:#fff;}
.dhl-barcode-num{font-size:7pt;letter-spacing:3px;margin-top:3px;font-family:'Courier New',monospace;color:#000;}
.dhl-qr-area{flex-shrink:0;text-align:center;}
.dhl-qr{
    width:70px;height:70px;
    border:2px solid #000;
    display:grid;grid-template-columns:repeat(7,1fr);
    overflow:hidden;
}
.dhl-qr-c{width:100%;aspect-ratio:1;}
.dhl-qr-lbl{font-size:5.5pt;color:#888;margin-top:2px;text-align:center;}

/* ── ROUTING BANNER ─────────────────────────────────── */
.dhl-routing{
    background:#FFCC00;
    border-bottom:2.5px solid #000;
    padding:6px 12px;
    display:flex;align-items:center;justify-content:space-between;
    flex-wrap:wrap;gap:6px;
}
.dhl-routing-left{}
.dhl-routing-tag{font-size:6.5pt;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;}
.dhl-routing-code{font-size:26pt;font-weight:900;letter-spacing:4px;color:#000;line-height:1;}
.dhl-routing-right{text-align:right;}
.dhl-dest-box{
    display:inline-block;
    border:2.5px solid #000;
    padding:4px 12px;
    background:#000;color:#FFCC00;
    font-size:18pt;font-weight:900;letter-spacing:3px;
    line-height:1;
}
.dhl-dest-label{font-size:6.5pt;color:#555;margin-top:3px;}

/* ── ADDRESSES ──────────────────────────────────────── */
.dhl-addresses{display:flex;border-bottom:2.5px solid #000;flex-wrap:wrap;}
.dhl-from-box,.dhl-to-box{flex:1;min-width:200px;padding:10px 12px;}
.dhl-from-box{border-right:2px solid #000;}
@media(max-width:440px){.dhl-from-box{border-right:none;border-bottom:2px solid #000;}}
.dhl-addr-cap{
    font-size:6.5pt;text-transform:uppercase;color:#666;
    font-weight:700;letter-spacing:.5px;
    border-bottom:1px solid #ddd;padding-bottom:3px;margin-bottom:5px;
}
.dhl-addr-name{font-size:10.5pt;font-weight:800;margin-bottom:1px;}
.dhl-addr-co{font-size:9pt;font-weight:600;margin-bottom:1px;color:#333;}
.dhl-addr-line{font-size:8.5pt;line-height:1.5;color:#222;}
.dhl-addr-city{font-size:10pt;font-weight:700;margin-top:4px;}
.dhl-addr-postal{font-size:13pt;font-weight:900;letter-spacing:2px;margin-top:2px;}
.dhl-addr-country{font-size:9pt;font-weight:700;color:#D40511;text-transform:uppercase;margin-top:1px;}
.dhl-addr-phone{font-size:7.5pt;color:#555;margin-top:4px;}

/* ── SENSITIVE ALERT ────────────────────────────────── */
.dhl-sensitive{
    background:#D40511;color:#fff;
    padding:6px 12px;border-bottom:2.5px solid #000;
    display:flex;align-items:center;gap:10px;flex-wrap:wrap;
}
.dhl-sensitive-icon{font-size:16pt;flex-shrink:0;line-height:1;}
.dhl-sensitive-title{font-size:9.5pt;font-weight:900;letter-spacing:1px;text-transform:uppercase;}
.dhl-sensitive-sub{font-size:7pt;opacity:.9;margin-top:1px;}

/* ── DETAILS BAR ────────────────────────────────────── */
.dhl-details{display:flex;border-bottom:2px solid #000;flex-wrap:wrap;}
.dhl-dc{flex:1;min-width:90px;padding:6px 10px;border-right:1px solid #ccc;}
.dhl-dc:last-child{border-right:none;}
@media(max-width:480px){.dhl-dc{min-width:45%;}}
.dhl-dc-lbl{font-size:6pt;text-transform:uppercase;color:#999;letter-spacing:.3px;}
.dhl-dc-val{font-size:9.5pt;font-weight:700;margin-top:2px;}
.dhl-dc-val.red{color:#D40511;}

/* ── SERVICE ROW ────────────────────────────────────── */
.dhl-svc-row{display:flex;border-bottom:2px solid #000;background:#f5f5f5;flex-wrap:wrap;}
.dhl-svc{flex:1;min-width:80px;padding:5px 8px;border-right:1px solid #ccc;text-align:center;}
.dhl-svc:last-child{border-right:none;}
.dhl-svc-lbl{font-size:6pt;text-transform:uppercase;color:#888;}
.dhl-svc-val{font-size:8.5pt;font-weight:700;}

/* ── CUSTOMS LINE ───────────────────────────────────── */
.dhl-customs{
    padding:6px 12px;border-bottom:2px solid #000;
    display:flex;gap:16px;flex-wrap:wrap;font-size:7.5pt;
}
.dhl-ci label{font-size:6pt;color:#888;text-transform:uppercase;display:block;letter-spacing:.3px;}
.dhl-ci span{font-weight:700;}

/* ── FINANCIAL RECEIPT ──────────────────────────────── */
.dhl-receipt{border-top:2.5px solid #000;}
.dhl-receipt-hdr{
    background:#1c1c1c;color:#fff;
    padding:6px 12px;
    font-size:7.5pt;font-weight:700;text-transform:uppercase;letter-spacing:1px;
    display:flex;align-items:center;gap:8px;
}
.dhl-rtable{width:100%;border-collapse:collapse;font-size:8.5pt;}
.dhl-rtable td{padding:6px 12px;border-bottom:1px solid #eee;vertical-align:top;}
.dhl-rtable td:last-child{text-align:right;font-weight:700;white-space:nowrap;}
.dhl-rtable .row-ref td{color:#555;}
.dhl-rtable .row-ref td:last-child{color:#333;}
.dhl-rtable .row-ship td{background:#fff;}
.dhl-rtable .row-ins td{background:#fffde7;}
.dhl-rtable .row-cust td{background:#fff3e0;}
.dhl-rtable .row-total td{
    background:#1c1c1c;color:#FFCC00;
    font-size:10pt;font-weight:900;border-bottom:none;
    padding:8px 12px;
}
.dhl-row-sub{font-size:6.5pt;color:#999;display:block;margin-top:1px;font-weight:400;}
.dhl-refund-tag{
    font-size:6.5pt;color:#27ae60;font-style:italic;display:block;margin-top:2px;
}

/* ── FOOTER ─────────────────────────────────────────── */
.dhl-footer{
    padding:8px 12px;border-top:2.5px solid #000;
    display:flex;justify-content:space-between;align-items:flex-end;
    flex-wrap:wrap;gap:8px;background:#fafafa;
}
.dhl-footer-left{font-size:7pt;color:#555;line-height:1.6;}
.dhl-footer-right{text-align:right;}
.dhl-footer-waybill{
    font-family:'Courier New',monospace;
    font-size:16pt;font-weight:900;color:#D40511;letter-spacing:2px;
}
.dhl-footer-note{font-size:6pt;color:#999;margin-top:2px;}

/* ── DISCLAIMER ─────────────────────────────────────── */
.dhl-disclaimer{
    padding:6px 12px;font-size:6pt;color:#888;
    line-height:1.5;border-top:1px solid #e0e0e0;background:#f5f5f5;
}

@media(max-width:520px){
    .dhl-logo{font-size:34px;}
    .dhl-service-name{font-size:11pt;}
    .dhl-routing-code{font-size:18pt;}
    .dhl-dest-box{font-size:13pt;}
    .dhl-wb-number{font-size:13pt;}
    .dhl-product-letter{font-size:26pt;}
}
</style>

<div class="dhl-wrap">

{{-- ── HEADER ──────────────────────────────────────────────── --}}
<div class="dhl-header">
    <div class="dhl-logo-cell">
        <span class="dhl-logo">DHL</span>
    </div>
    <div class="dhl-service-cell">
        <div class="dhl-express-label">DHL Express</div>
        <div class="dhl-service-name">{{ $shipment->service_type }}</div>
        <div class="dhl-service-sub">Time Definite International · Door to Door</div>
    </div>
    <div class="dhl-product-cell">
        <div class="dhl-product-letter">{{ $shipment->product_code }}</div>
        <div class="dhl-product-sub">Product<br>Code</div>
    </div>
</div>

{{-- ── STATUS + MASKED TRACKING ────────────────────────────── --}}
<div class="dhl-status-bar">
    <div>
        <div style="font-size:6.5pt;color:#999;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Shipment Status</div>
        <span class="dhl-status-pill" style="background:{{ $st['bg'] }};color:{{ $st['text'] }};">
            <span class="dhl-status-dot"></span>
            {{ $st['label'] }}
        </span>
    </div>
    <div class="dhl-tracking-area">
        <span class="dhl-tracking-label">Tracking Number</span>
        <span class="dhl-tracking-num">
            @foreach(mb_str_split($masked) as $ch)
                @if($ch==='•')<span style="color:#D40511;font-size:17pt;vertical-align:middle;line-height:.9;">•</span>@else{{ $ch }}@endif
            @endforeach
        </span>
        <span class="dhl-tracking-hint">Full number released upon payment clearance</span>
    </div>
</div>

{{-- ── WAYBILL + BARCODE ───────────────────────────────────── --}}
<div class="dhl-waybill-section">
    <div class="dhl-waybill-left">
        <div class="dhl-wb-tag">Waybill No.</div>
        <div class="dhl-wb-number">
            @foreach(mb_str_split($masked) as $ch)
                @if($ch==='•')<span style="color:#D40511;font-size:19pt;vertical-align:middle;line-height:.85;">•</span>@else{{ $ch }}@endif
            @endforeach
        </div>
        <div class="dhl-wb-date">Ship Date: {{ $shipDate }}</div>
        @if($shipment->estimated_arrival)
        <div class="dhl-wb-date" style="color:#D40511;font-weight:700;">Est. Delivery: {{ $shipment->estimated_arrival->format('d M Y') }}</div>
        @endif
    </div>

    <div class="dhl-barcode-wrap">
        <div class="dhl-bars">
            @foreach($barcodePattern as $width)
                @php $idx = $loop->index; @endphp
                @if($idx % 2 === 0)
                    <div class="dhl-bar" style="width:{{ $width }}px;"></div>
                @else
                    <div class="dhl-gap" style="width:{{ $width }}px;"></div>
                @endif
            @endforeach
        </div>
        <div class="dhl-barcode-num">{{ implode(' ', str_split($waybill, 2)) }}</div>
    </div>

    <div class="dhl-qr-area">
        @php
            // Deterministic QR-like grid from waybill hash
            $qrSeed = hexdec(substr(md5($waybill), 0, 8));
            $qrCells = [];
            for ($r = 0; $r < 7; $r++) {
                for ($c = 0; $c < 7; $c++) {
                    // Force finder patterns in corners
                    if (($r < 2 && $c < 2) || ($r < 2 && $c >= 5) || ($r >= 5 && $c < 2)) {
                        $qrCells[] = true;
                    } else {
                        $qrCells[] = (bool)(($qrSeed >> (($r * 7 + $c) % 30)) & 1);
                    }
                }
            }
        @endphp
        <div class="dhl-qr">
            @foreach($qrCells as $filled)
                <div class="dhl-qr-c" style="background:{{ $filled ? '#000' : '#fff' }};"></div>
            @endforeach
        </div>
        <div class="dhl-qr-lbl">Scan to Track</div>
    </div>
</div>

{{-- ── ROUTING BANNER ──────────────────────────────────────── --}}
<div class="dhl-routing">
    <div class="dhl-routing-left">
        <div class="dhl-routing-tag">Routing Code</div>
        <div class="dhl-routing-code">{{ $shipment->routing_code ?? ($shipment->origin_service_area . '-' . $shipment->dest_service_area) }}</div>
    </div>
    <div class="dhl-routing-right">
        <div class="dhl-routing-tag" style="margin-bottom:4px;">Destination Service Area</div>
        <div class="dhl-dest-box">{{ $shipment->dest_service_area }}</div>
        <div class="dhl-dest-label">{{ strtoupper($shipment->receiver_city) }} · {{ $shipment->receiver_country }}</div>
    </div>
</div>

{{-- ── ADDRESSES ────────────────────────────────────────────── --}}
<div class="dhl-addresses">
    <div class="dhl-from-box">
        <div class="dhl-addr-cap">From / Shipper</div>
        <div class="dhl-addr-name">{{ $shipment->shipper_name }}</div>
        @if($shipment->shipper_company)
            <div class="dhl-addr-co">{{ $shipment->shipper_company }}</div>
        @endif
        <div class="dhl-addr-line">{{ $shipment->shipper_address1 }}</div>
        @if($shipment->shipper_address2)
            <div class="dhl-addr-line">{{ $shipment->shipper_address2 }}</div>
        @endif
        <div class="dhl-addr-city">{{ strtoupper($shipment->shipper_city) }}{{ $shipperState }}</div>
        <div class="dhl-addr-postal">{{ $shipment->shipper_postal }}</div>
        <div class="dhl-addr-country">{{ $shipment->shipper_country }}</div>
        @if($shipment->shipper_phone)
            <div class="dhl-addr-phone">Tel: {{ $shipment->shipper_phone }}</div>
        @endif
    </div>
    <div class="dhl-to-box">
        <div class="dhl-addr-cap">To / Receiver</div>
        <div class="dhl-addr-name" style="font-size:12pt;">{{ $shipment->receiver_name }}</div>
        @if($shipment->receiver_company)
            <div class="dhl-addr-co" style="font-size:10pt;">{{ $shipment->receiver_company }}</div>
        @endif
        <div class="dhl-addr-line">{{ $shipment->receiver_address1 }}</div>
        @if($shipment->receiver_address2)
            <div class="dhl-addr-line">{{ $shipment->receiver_address2 }}</div>
        @endif
        <div class="dhl-addr-city" style="font-size:11pt;">{{ strtoupper($shipment->receiver_city) }}{{ $receiverState }}</div>
        <div class="dhl-addr-postal">{{ $shipment->receiver_postal }}</div>
        <div class="dhl-addr-country" style="font-size:10pt;">{{ $shipment->receiver_country }}</div>
        @if($shipment->receiver_phone)
            <div class="dhl-addr-phone">Tel: {{ $shipment->receiver_phone }}</div>
        @endif
    </div>
</div>

{{-- ── SENSITIVE ALERT ─────────────────────────────────────── --}}
@if($shipment->is_sensitive)
<div class="dhl-sensitive">
    <div class="dhl-sensitive-icon">⚠</div>
    <div>
        <div class="dhl-sensitive-title">Sensitive Shipment</div>
        <div class="dhl-sensitive-sub">Handle with care · Special handling required at all transit points · Do not stack</div>
    </div>
</div>
@endif

{{-- ── DETAILS BAR ─────────────────────────────────────────── --}}
<div class="dhl-details">
    <div class="dhl-dc">
        <div class="dhl-dc-lbl">Gross Weight</div>
        <div class="dhl-dc-val">{{ $weightStr }}</div>
    </div>
    <div class="dhl-dc">
        <div class="dhl-dc-lbl">Dimensions</div>
        <div class="dhl-dc-val" style="font-size:8pt;">{{ $shipment->dimensions ?? 'N/A' }}</div>
    </div>
    <div class="dhl-dc">
        <div class="dhl-dc-lbl">Pieces</div>
        <div class="dhl-dc-val">{{ $shipment->pieces }}/1</div>
    </div>
    <div class="dhl-dc">
        <div class="dhl-dc-lbl">Ship Date</div>
        <div class="dhl-dc-val" style="font-size:8.5pt;">{{ $shipDateShort }}</div>
    </div>
    @if($shipment->estimated_arrival)
    <div class="dhl-dc">
        <div class="dhl-dc-lbl">Est. Delivery</div>
        <div class="dhl-dc-val red" style="font-size:8.5pt;">{{ $shipment->estimated_arrival->format('d/m/Y') }}</div>
    </div>
    @endif
</div>

{{-- ── SERVICE ROW ─────────────────────────────────────────── --}}
<div class="dhl-svc-row">
    <div class="dhl-svc">
        <div class="dhl-svc-lbl">Service Level</div>
        <div class="dhl-svc-val">EXPRESS WW</div>
    </div>
    <div class="dhl-svc">
        <div class="dhl-svc-lbl">Payor</div>
        <div class="dhl-svc-val">Shipper</div>
    </div>
    <div class="dhl-svc">
        <div class="dhl-svc-lbl">Incoterms</div>
        <div class="dhl-svc-val">DAP</div>
    </div>
    <div class="dhl-svc">
        <div class="dhl-svc-lbl">Acct No.</div>
        <div class="dhl-svc-val">{{ strtoupper(substr(md5($waybill), 0, 8)) }}</div>
    </div>
    @if($shipment->is_sensitive)
    <div class="dhl-svc">
        <div class="dhl-svc-lbl">Handling</div>
        <div class="dhl-svc-val" style="color:#D40511;">SENSITIVE</div>
    </div>
    @endif
</div>

{{-- ── CUSTOMS LINE ─────────────────────────────────────────── --}}
<div class="dhl-customs">
    <div class="dhl-ci">
        <label>Content</label>
        <span>{{ $shipment->content_description }}</span>
    </div>
    <div class="dhl-ci">
        <label>Currency</label>
        <span>{{ $cur }}</span>
    </div>
    <div class="dhl-ci">
        <label>Declared Value</label>
        <span>{{ $cur }} {{ $goodsValue > 0 ? number_format($goodsValue, 2) : 'N/A' }}</span>
    </div>
    <div class="dhl-ci">
        <label>HS Code</label>
        <span>2937.19</span>
    </div>
    <div class="dhl-ci">
        <label>Origin</label>
        <span>{{ $shipment->shipper_country }}</span>
    </div>
</div>

{{-- ── FINANCIAL RECEIPT ────────────────────────────────────── --}}
@if($goodsValue || $totalFeesDue)
<div class="dhl-receipt">
    <div class="dhl-receipt-hdr">
        <span>&#x1F4CB;</span>
        <span>Shipment Cost Summary &amp; Charges</span>
    </div>
    <table class="dhl-rtable">
        @if($goodsValue)
        <tr class="row-ref">
            <td>
                Declared Goods Value
                <span class="dhl-row-sub">Reference only — not a charge payable to DHL</span>
            </td>
            <td>{{ $cur }} {{ number_format($goodsValue, 2) }}</td>
        </tr>
        @endif
        @if($shippingFee)
        <tr class="row-ship">
            <td>
                DHL Express Shipping Fee
                <span class="dhl-row-sub">{{ $shipment->origin_service_area }} → {{ $shipment->dest_service_area }} · {{ $shipment->service_type }}</span>
            </td>
            <td>{{ $cur }} {{ number_format($shippingFee, 2) }}</td>
        </tr>
        @endif
        @if($insuranceFee)
        <tr class="row-ins">
            <td>
                Shipment Insurance (10% of declared value)
                <span class="dhl-row-sub">Covers loss, theft &amp; damage in transit</span>
                @if($shipment->insurance_refundable)
                    <span class="dhl-refund-tag">&#x2713; Fully refundable upon confirmed delivery</span>
                @endif
            </td>
            <td>{{ $cur }} {{ number_format($insuranceFee, 2) }}</td>
        </tr>
        @endif
        @if($customsDuties)
        <tr class="row-cust">
            <td>
                Customs &amp; Import Duties
                <span class="dhl-row-sub">EU import clearance · Destination: {{ $shipment->receiver_country }} · Payable before release</span>
            </td>
            <td>{{ $cur }} {{ number_format($customsDuties, 2) }}</td>
        </tr>
        @endif
        <tr class="row-total">
            <td>Total Charges Due</td>
            <td>{{ $cur }} {{ number_format($totalFeesDue, 2) }}</td>
        </tr>
    </table>
</div>
@endif

{{-- ── FOOTER ───────────────────────────────────────────────── --}}
<div class="dhl-footer">
    <div class="dhl-footer-left">
        <strong>DHL Express (USA), Inc.</strong><br>
        1200 S. Pine Island Road, Plantation, FL 33324, USA<br>
        Customer Service: 1-800-225-5345 · www.dhl.com<br>
        <span style="font-size:6pt;">Generated: {{ now()->format('d M Y H:i') }} UTC</span>
    </div>
    <div class="dhl-footer-right">
        <div class="dhl-footer-waybill">
            @foreach(mb_str_split($masked) as $ch)
                @if($ch==='•')<span style="color:#bbb;font-size:14pt;vertical-align:middle;">•</span>@else{{ $ch }}@endif
            @endforeach
        </div>
        <div class="dhl-footer-note">Retain this waybill as your shipment receipt</div>
    </div>
</div>

{{-- ── DISCLAIMER ───────────────────────────────────────────── --}}
<div class="dhl-disclaimer">
    By tendering this shipment you agree to DHL's Terms &amp; Conditions of Carriage (available at www.dhl.com). Liability
    is limited per the Warsaw/Montreal Convention as applicable. All shipments are subject to inspection and retention
    by customs authorities. DHL account number and waybill number must be quoted in all correspondence.
    Shipper's Reference: {{ strtoupper(substr(md5($waybill . $shipment->shipper_name), 0, 12)) }}
</div>

</div>

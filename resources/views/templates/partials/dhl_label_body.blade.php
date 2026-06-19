@php
    $waybill      = $shipment->waybill_number;
    $shipDate     = $shipment->ship_date->format('d M Y');
    $shipDateShort = $shipment->ship_date->format('d/m/Y');

    // Telegram-style masked tracking: show first 3 and last 2 chars, mask the middle with •
    $wLen   = mb_strlen($waybill);
    $masked = mb_substr($waybill, 0, 3)
            . str_repeat('•', max(0, $wLen - 5))
            . mb_substr($waybill, -2);

    // Financial totals
    $goodsValue    = $shipment->goods_value    ?? 0;
    $insuranceFee  = $shipment->insurance_fee  ?? 0;
    $customsDuties = $shipment->customs_duties ?? 0;
    $shippingFee   = $shipment->shipping_fee   ?? 0;
    $grandTotal    = $goodsValue + $insuranceFee + $customsDuties + $shippingFee;
    $feesCurrency  = $shipment->shipping_fee_currency ?? $shipment->currency ?? 'EUR';

    $statusColors = [
        'pending'    => ['bg' => '#f39c12', 'text' => '#fff'],
        'in_transit' => ['bg' => '#2980b9', 'text' => '#fff'],
        'delivered'  => ['bg' => '#27ae60', 'text' => '#fff'],
        'failed'     => ['bg' => '#c0392b', 'text' => '#fff'],
    ];
    $statusLabel = strtoupper(str_replace('_', ' ', $shipment->status ?? 'pending'));
    $statusColor = $statusColors[$shipment->status ?? 'pending'] ?? $statusColors['pending'];
@endphp
<style>
.dhl-label {
    width: 5.5in;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 8pt;
    color: #000;
    border: 2px solid #000;
    background: #fff;
}
.dhl-header {
    display: flex;
    align-items: stretch;
    border-bottom: 2px solid #000;
}
.dhl-logo-box {
    background: #D40511;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 120px;
    border-right: 2px solid #000;
}
.dhl-logo {
    font-size: 42px;
    font-weight: 900;
    color: #FFCC00;
    letter-spacing: -2px;
    font-family: Arial Black, Arial, sans-serif;
    line-height: 1;
}
.dhl-header-info {
    flex: 1;
    padding: 6px 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.dhl-service-type {
    font-size: 16pt;
    font-weight: 900;
    color: #D40511;
    text-transform: uppercase;
    line-height: 1.1;
}
.dhl-service-sub {
    font-size: 7pt;
    color: #555;
    margin-top: 2px;
}
.dhl-product-box {
    background: #FFCC00;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 6px 14px;
    border-left: 2px solid #000;
    min-width: 60px;
    text-align: center;
}
.dhl-product-letter {
    font-size: 36pt;
    font-weight: 900;
    color: #000;
    line-height: 1;
}
.dhl-product-label {
    font-size: 6.5pt;
    font-weight: bold;
    color: #000;
    text-transform: uppercase;
    margin-top: 2px;
}
/* Waybill barcode section */
.dhl-waybill-section {
    border-bottom: 2px solid #000;
    padding: 8px 10px 6px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.dhl-waybill-left { flex: 1; }
.dhl-waybill-label { font-size: 7pt; color: #555; text-transform: uppercase; letter-spacing: 0.5px; }
.dhl-waybill-number {
    font-size: 18pt;
    font-weight: 900;
    letter-spacing: 2px;
    color: #000;
    line-height: 1.1;
}
.dhl-waybill-right { text-align: right; }
/* CSS Barcode */
.barcode-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 4px 0;
}
.barcode-bars {
    display: flex;
    align-items: flex-end;
    height: 52px;
    gap: 0;
}
.bar { display: inline-block; height: 52px; background: #000; }
.bar-thin { width: 1.5px; margin: 0 0.5px; }
.bar-mid  { width: 2.5px; margin: 0 0.5px; }
.bar-wide { width: 3.5px; margin: 0 0.5px; }
.bar-space{ width: 2px; background: #fff; }
.barcode-text { font-size: 7.5pt; letter-spacing: 3px; margin-top: 3px; font-family: 'Courier New', monospace; }
/* Routing code banner */
.dhl-routing {
    background: #FFCC00;
    border-bottom: 2px solid #000;
    padding: 4px 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.dhl-routing-code {
    font-size: 24pt;
    font-weight: 900;
    letter-spacing: 3px;
    color: #000;
    line-height: 1;
}
.dhl-routing-label { font-size: 7pt; color: #333; font-weight: bold; }
.dhl-dest-area {
    margin-left: auto;
    font-size: 9pt;
    font-weight: bold;
    color: #000;
}
/* Address section */
.dhl-addresses {
    display: flex;
    border-bottom: 2px solid #000;
}
.dhl-from-box, .dhl-to-box {
    flex: 1;
    padding: 8px 10px;
}
.dhl-from-box {
    border-right: 1.5px solid #000;
}
.dhl-addr-label {
    font-size: 7pt;
    text-transform: uppercase;
    color: #555;
    font-weight: bold;
    letter-spacing: 0.5px;
    margin-bottom: 3px;
    border-bottom: 1px solid #ccc;
    padding-bottom: 2px;
}
.dhl-addr-name  { font-size: 9.5pt; font-weight: 700; margin-bottom: 1px; }
.dhl-addr-company { font-size: 8.5pt; font-weight: 600; margin-bottom: 1px; }
.dhl-addr-line  { font-size: 8pt; line-height: 1.4; color: #222; }
.dhl-addr-city  { font-size: 9pt; font-weight: 700; margin-top: 3px; }
.dhl-addr-country { font-size: 8pt; font-weight: 700; color: #D40511; text-transform: uppercase; }
/* Shipment details */
.dhl-details {
    display: flex;
    border-bottom: 2px solid #000;
}
.dhl-detail-cell {
    flex: 1;
    padding: 5px 8px;
    border-right: 1px solid #ccc;
}
.dhl-detail-cell:last-child { border-right: none; }
.dhl-detail-label { font-size: 6.5pt; text-transform: uppercase; color: #888; letter-spacing: 0.3px; }
.dhl-detail-value { font-size: 9.5pt; font-weight: 700; margin-top: 1px; }
/* Service indicators */
.dhl-service-row {
    display: flex;
    border-bottom: 2px solid #000;
    background: #f5f5f5;
}
.dhl-service-indicator {
    flex: 1;
    padding: 4px 8px;
    border-right: 1px solid #ccc;
    text-align: center;
}
.dhl-service-indicator:last-child { border-right: none; }
.dhl-si-label { font-size: 6pt; text-transform: uppercase; color: #777; }
.dhl-si-value  { font-size: 8pt; font-weight: 700; color: #000; }
/* Footer / reference */
.dhl-footer {
    padding: 6px 10px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    border-bottom: 2px solid #000;
}
.dhl-footer-left { font-size: 7pt; color: #555; }
.dhl-footer-right { text-align: right; }
/* QR / 2D code placeholder */
.dhl-qr-box {
    width: 64px;
    height: 64px;
    border: 2px solid #000;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: repeating-linear-gradient(
        45deg,
        #000,
        #000 2px,
        #fff 2px,
        #fff 6px
    );
    position: relative;
}
.dhl-qr-inner {
    width: 30px;
    height: 30px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 6pt;
    font-weight: bold;
    text-align: center;
    border: 1px solid #000;
}
/* Customs / content */
.dhl-customs {
    padding: 5px 10px;
    font-size: 7.5pt;
    display: flex;
    gap: 20px;
    border-bottom: 1.5px solid #000;
}
.dhl-customs-item label { color: #777; font-size: 6.5pt; text-transform: uppercase; display: block; }
.dhl-customs-item span  { font-weight: 700; font-size: 8pt; }
/* Disclaimer */
.dhl-disclaimer {
    padding: 5px 10px;
    font-size: 6pt;
    color: #777;
    line-height: 1.4;
    border-top: 1px solid #ddd;
    background: #fafafa;
}
/* Status badge */
.dhl-status-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 10px;
    border-bottom: 2px solid #000;
    background: #f9f9f9;
}
.dhl-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 8.5pt;
    font-weight: 900;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}
.dhl-status-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: currentColor;
    opacity: 0.7;
    animation: pulse 1.4s infinite;
}
@keyframes pulse { 0%,100%{opacity:.7} 50%{opacity:1} }
/* Tracking masked */
.dhl-tracking-masked {
    font-family: 'Courier New', monospace;
    font-size: 13pt;
    font-weight: 900;
    letter-spacing: 4px;
    color: #000;
}
.dhl-tracking-dot {
    color: #D40511;
    font-size: 16pt;
    vertical-align: middle;
    line-height: 1;
}
/* Financial receipt table */
.dhl-receipt {
    border-top: 2px solid #000;
    padding: 0;
}
.dhl-receipt-title {
    background: #1a1a1a;
    color: #fff;
    padding: 5px 10px;
    font-size: 7.5pt;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.dhl-receipt table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8pt;
}
.dhl-receipt td {
    padding: 5px 10px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}
.dhl-receipt td:last-child { text-align: right; font-weight: 700; }
.dhl-receipt .row-sub  td { color: #555; }
.dhl-receipt .row-ins  td { background: #fffde7; }
.dhl-receipt .row-ins td:first-child::before { content: '🔒 '; }
.dhl-receipt .row-customs td { background: #fff3e0; }
.dhl-receipt .row-total td {
    background: #1a1a1a;
    color: #fff;
    font-weight: 900;
    font-size: 9pt;
    border-bottom: none;
    padding: 7px 10px;
}
.dhl-refund-note {
    font-size: 6.5pt;
    color: #27ae60;
    font-style: italic;
    margin-top: 1px;
    display: block;
}
</style>

<div class="dhl-label">
    {{-- HEADER: DHL Logo + Service + Product Code --}}
    <div class="dhl-header">
        <div class="dhl-logo-box">
            <span class="dhl-logo">DHL</span>
        </div>
        <div class="dhl-header-info">
            <div class="dhl-service-type">{{ $shipment->service_type }}</div>
            <div class="dhl-service-sub">DHL EXPRESS — Worldwide Priority Delivery</div>
        </div>
        <div class="dhl-product-box">
            <div class="dhl-product-letter">{{ $shipment->product_code }}</div>
            <div class="dhl-product-label">Product</div>
        </div>
    </div>

    {{-- STATUS BAR --}}
    <div class="dhl-status-bar">
        <div>
            <span style="font-size:6.5pt; color:#888; text-transform:uppercase; letter-spacing:0.5px;">Shipment Status</span><br>
            <span class="dhl-status-badge" style="background:{{ $statusColor['bg'] }}; color:{{ $statusColor['text'] }}; margin-top:3px;">
                <span class="dhl-status-dot" style="background:{{ $statusColor['text'] }};"></span>
                {{ $statusLabel }}
            </span>
        </div>
        <div style="text-align:right;">
            <span style="font-size:6.5pt; color:#888; text-transform:uppercase; letter-spacing:0.5px; display:block;">Tracking Number</span>
            <span class="dhl-tracking-masked">
                @php
                    $chars = mb_str_split($masked);
                @endphp
                @foreach($chars as $ch)
                    @if($ch === '•')
                        <span class="dhl-tracking-dot">•</span>
                    @else
                        {{ $ch }}
                    @endif
                @endforeach
            </span>
            <span style="font-size:6pt; color:#aaa; display:block; margin-top:1px; letter-spacing:0.3px;">Full number revealed upon payment clearance</span>
        </div>
    </div>

    {{-- WAYBILL BARCODE SECTION --}}
    <div class="dhl-waybill-section">
        <div class="dhl-waybill-left">
            <div class="dhl-waybill-label">Waybill / Tracking</div>
            <div class="dhl-waybill-number" style="letter-spacing:3px;">
                @foreach(mb_str_split($masked) as $ch)
                    @if($ch === '•')<span style="color:#D40511; font-size:22pt; vertical-align:middle; line-height:0.8;">•</span>@else{{ $ch }}@endif
                @endforeach
            </div>
            <div style="font-size:7pt; color:#555; margin-top:2px;">Ship Date: {{ $shipDate }}</div>
        </div>
        <div style="flex:1; text-align:center;">
            <div class="barcode-wrapper">
                <div class="barcode-bars">
                    @php
                        // Generate pseudo-random barcode pattern from waybill number
                        $patterns = ['wide','thin','mid','space','thin','wide','mid','thin','wide','space','thin','mid','wide','thin','space','mid','wide','thin','thin','wide','space','mid','thin','wide','mid','space','thin'];
                        $seed = crc32($waybill);
                        srand($seed);
                    @endphp
                    @for($i = 0; $i < 80; $i++)
                        @php $r = rand(0,4); @endphp
                        @if($r === 0)
                            <span class="bar bar-thin"></span>
                        @elseif($r === 1)
                            <span class="bar bar-wide"></span>
                        @elseif($r === 2)
                            <span class="bar bar-mid"></span>
                        @elseif($r === 3)
                            <span class="bar bar-thin"></span>
                        @else
                            <span class="bar bar-space" style="background:#fff; width:2px; display:inline-block; height:52px;"></span>
                        @endif
                    @endfor
                </div>
                <div class="barcode-text">{{ implode(' ', str_split($waybill, 2)) }}</div>
            </div>
        </div>
        <div class="dhl-waybill-right">
            <div class="dhl-qr-box">
                <div class="dhl-qr-inner">
                    <span>{{ substr($waybill,0,4) }}</span>
                </div>
            </div>
            <div style="font-size:6pt; color:#888; margin-top:2px; text-align:center;">2D Code</div>
        </div>
    </div>

    {{-- ROUTING BANNER --}}
    <div class="dhl-routing">
        <div>
            <div class="dhl-routing-label">Routing Code</div>
            <div class="dhl-routing-code">{{ $shipment->routing_code ?? $shipment->origin_service_area . '-' . $shipment->dest_service_area }}</div>
        </div>
        <div style="margin-left:auto; text-align:right;">
            <div class="dhl-routing-label">Origin → Destination</div>
            <div style="font-size:13pt; font-weight:900; color:#000;">
                {{ $shipment->origin_service_area }} → {{ $shipment->dest_service_area }}
            </div>
        </div>
    </div>

    {{-- TO / FROM ADDRESSES --}}
    <div class="dhl-addresses">
        <div class="dhl-from-box">
            <div class="dhl-addr-label">From (Shipper)</div>
            <div class="dhl-addr-name">{{ $shipment->shipper_name }}</div>
            @if($shipment->shipper_company)
            <div class="dhl-addr-company">{{ $shipment->shipper_company }}</div>
            @endif
            <div class="dhl-addr-line">{{ $shipment->shipper_address1 }}</div>
            @if($shipment->shipper_address2)
            <div class="dhl-addr-line">{{ $shipment->shipper_address2 }}</div>
            @endif
            <div class="dhl-addr-city">{{ strtoupper($shipment->shipper_city) }}, {{ $shipment->shipper_state }} {{ $shipment->shipper_postal }}</div>
            <div class="dhl-addr-country">{{ $shipment->shipper_country }}</div>
            @if($shipment->shipper_phone)
            <div class="dhl-addr-line" style="margin-top:3px; font-size:7.5pt;">Tel: {{ $shipment->shipper_phone }}</div>
            @endif
        </div>
        <div class="dhl-to-box">
            <div class="dhl-addr-label">To (Receiver)</div>
            <div class="dhl-addr-name" style="font-size:11pt;">{{ $shipment->receiver_name }}</div>
            @if($shipment->receiver_company)
            <div class="dhl-addr-company" style="font-size:10pt;">{{ $shipment->receiver_company }}</div>
            @endif
            <div class="dhl-addr-line">{{ $shipment->receiver_address1 }}</div>
            @if($shipment->receiver_address2)
            <div class="dhl-addr-line">{{ $shipment->receiver_address2 }}</div>
            @endif
            <div class="dhl-addr-city" style="font-size:11pt;">{{ strtoupper($shipment->receiver_city) }}, {{ $shipment->receiver_state }}</div>
            <div style="font-size:12pt; font-weight:900; color:#000; margin-top:2px; letter-spacing:2px;">{{ $shipment->receiver_postal }}</div>
            <div class="dhl-addr-country" style="font-size:11pt;">{{ $shipment->receiver_country }}</div>
            @if($shipment->receiver_phone)
            <div class="dhl-addr-line" style="margin-top:3px; font-size:7.5pt;">Tel: {{ $shipment->receiver_phone }}</div>
            @endif
        </div>
    </div>

    {{-- SENSITIVE PACKAGE ALERT --}}
    @if($shipment->is_sensitive)
    <div style="background:#D40511; color:#fff; padding:6px 12px; display:flex; align-items:center; gap:8px; border-bottom:2px solid #000;">
        <span style="font-size:14pt; line-height:1;">⚠</span>
        <div>
            <span style="font-size:9pt; font-weight:900; letter-spacing:1px; text-transform:uppercase;">SENSITIVE SHIPMENT</span>
            <span style="font-size:7.5pt; margin-left:10px; opacity:0.9;">Handle with care — Special handling required at all transit points</span>
        </div>
    </div>
    @endif

    {{-- SHIPMENT DETAILS BAR --}}
    <div class="dhl-details">
        <div class="dhl-detail-cell">
            <div class="dhl-detail-label">Weight</div>
            <div class="dhl-detail-value">{{ $shipment->weight_kg }} KG</div>
        </div>
        <div class="dhl-detail-cell">
            <div class="dhl-detail-label">Dimensions</div>
            <div class="dhl-detail-value" style="font-size:8.5pt;">{{ $shipment->dimensions ?? 'N/A' }}</div>
        </div>
        <div class="dhl-detail-cell">
            <div class="dhl-detail-label">Pieces</div>
            <div class="dhl-detail-value">{{ $shipment->pieces }} / 1</div>
        </div>
        <div class="dhl-detail-cell">
            <div class="dhl-detail-label">Ship Date</div>
            <div class="dhl-detail-value" style="font-size:8.5pt;">{{ $shipDateShort }}</div>
        </div>
        @if($shipment->estimated_arrival)
        <div class="dhl-detail-cell">
            <div class="dhl-detail-label">Est. Delivery</div>
            <div class="dhl-detail-value" style="font-size:8.5pt; color:#D40511;">{{ $shipment->estimated_arrival->format('d/m/Y') }}</div>
        </div>
        @endif
    </div>

    {{-- SERVICE INDICATORS --}}
    <div class="dhl-service-row">
        <div class="dhl-service-indicator">
            <div class="dhl-si-label">Service</div>
            <div class="dhl-si-value">EXPRESS</div>
        </div>
        <div class="dhl-service-indicator">
            <div class="dhl-si-label">Description</div>
            <div class="dhl-si-value">{{ Str::limit($shipment->content_description, 20) }}</div>
        </div>
        <div class="dhl-service-indicator">
            <div class="dhl-si-label">Shipping Fee</div>
            <div class="dhl-si-value">
                @if($shipment->shipping_fee)
                    {{ $shipment->shipping_fee_currency }} {{ number_format($shipment->shipping_fee, 2) }}
                @else
                    —
                @endif
            </div>
        </div>
        <div class="dhl-service-indicator">
            <div class="dhl-si-label">Account</div>
            <div class="dhl-si-value">DHL ACCT</div>
        </div>
    </div>

    {{-- CUSTOMS LINE --}}
    <div class="dhl-customs">
        <div class="dhl-customs-item">
            <label>Content Description</label>
            <span>{{ $shipment->content_description }}</span>
        </div>
        <div class="dhl-customs-item">
            <label>Currency</label>
            <span>{{ $shipment->currency }}</span>
        </div>
        <div class="dhl-customs-item">
            <label>Incoterms</label>
            <span>DAP</span>
        </div>
        <div class="dhl-customs-item">
            <label>Payment</label>
            <span>Shipper</span>
        </div>
        @if($shipment->is_sensitive)
        <div class="dhl-customs-item">
            <label>Handling</label>
            <span style="color:#D40511; font-weight:900;">SENSITIVE</span>
        </div>
        @endif
    </div>

    {{-- FINANCIAL RECEIPT --}}
    @if($goodsValue || $insuranceFee || $customsDuties || $shippingFee)
    <div class="dhl-receipt">
        <div class="dhl-receipt-title">&#x1F4CB; Shipment Cost Summary &amp; Fees</div>
        <table>
            <tr class="row-sub">
                <td>Goods / Package Value</td>
                <td style="color:#555; font-size:7pt;">Declared market value</td>
                <td>{{ $feesCurrency }} {{ number_format($goodsValue, 2) }}</td>
            </tr>
            @if($shippingFee)
            <tr class="row-sub">
                <td>Shipping Fee</td>
                <td style="color:#555; font-size:7pt;">DHL Express Worldwide</td>
                <td>{{ $feesCurrency }} {{ number_format($shippingFee, 2) }}</td>
            </tr>
            @endif
            @if($insuranceFee)
            <tr class="row-ins">
                <td>
                    Insurance Fee
                    <span style="font-size:6.5pt; font-weight:400; color:#888; display:block;">10% of declared goods value</span>
                </td>
                <td style="font-size:7pt; color:#7a6000;">
                    Covers theft, loss &amp; damage in transit
                    @if($shipment->insurance_refundable)
                    <span class="dhl-refund-note">&#x2713; Fully refunded upon successful delivery</span>
                    @endif
                </td>
                <td>{{ $feesCurrency }} {{ number_format($insuranceFee, 2) }}</td>
            </tr>
            @endif
            @if($customsDuties)
            <tr class="row-customs">
                <td>
                    Customs &amp; Import Duties
                    <span style="font-size:6.5pt; font-weight:400; color:#888; display:block;">Destination country: Portugal (PT)</span>
                </td>
                <td style="font-size:7pt; color:#7a4400;">
                    EU import clearance — payable before release
                </td>
                <td>{{ $feesCurrency }} {{ number_format($customsDuties, 2) }}</td>
            </tr>
            @endif
            <tr class="row-total">
                <td colspan="2">TOTAL AMOUNT DUE</td>
                <td>{{ $feesCurrency }} {{ number_format($grandTotal, 2) }}</td>
            </tr>
        </table>
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="dhl-footer">
        <div class="dhl-footer-left">
            <div><strong>DHL Express (USA), Inc.</strong></div>
            <div>1200 S. Pine Island Road, Plantation, FL 33324</div>
            <div>Customer Service: 1-800-225-5345</div>
            <div style="margin-top:4px; font-size:6pt;">www.dhl.com</div>
        </div>
        <div class="dhl-footer-right">
            <div style="font-size:7pt; color:#888;">Waybill generated: {{ now()->format('d M Y H:i') }}</div>
            <div style="font-size:18pt; font-weight:900; color:#D40511; letter-spacing:1px;">
                @foreach(mb_str_split($masked) as $ch)
                    @if($ch === '•')<span style="color:#aaa; font-size:16pt; vertical-align:middle;">•</span>@else{{ $ch }}@endif
                @endforeach
            </div>
            <div style="font-size:6pt; color:#888;">Retain this waybill as your shipment receipt</div>
        </div>
    </div>

    {{-- DISCLAIMER --}}
    <div class="dhl-disclaimer">
        By tendering this shipment, you agree to DHL's Terms and Conditions of Carriage available at www.dhl.com. DHL Express accepts no liability for loss, damage or delay beyond the limitations set out in those Terms and Conditions. This shipment is subject to the Warsaw Convention and/or Montreal Convention as applicable. All goods are subject to examination and retention by customs or other authorities.
    </div>
</div>

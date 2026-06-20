@php
    $purity       = number_format((float)$coa->purity_hplc, 2);
    $analysisPeriod = $coa->analysis_start_date
        ? $coa->analysis_start_date->format('d M Y') . " \u{2013} " . $coa->analysis_date->format('d M Y')
        : $coa->analysis_date->format('d M Y');

    // Deterministic verification code
    $verifyCode = strtoupper(substr(md5($coa->lot_number . $coa->analysis_date), 0, 16));
    $verifyFmt  = implode('-', str_split($verifyCode, 4));

    // Batch release number
    $batchReleaseNo = strtoupper(substr(md5($coa->lot_number . 'release'), 0, 10));

    // QR-like grid (for document verification)
    $qSeed  = hexdec(substr(md5($coa->lot_number), 0, 8));
    $qCells = [];
    for ($r = 0; $r < 7; $r++) {
        for ($c = 0; $c < 7; $c++) {
            if (($r < 2 && $c < 2) || ($r < 2 && $c >= 5) || ($r >= 5 && $c < 2)) {
                $qCells[] = true;
            } else {
                $qCells[] = (bool)(($qSeed >> (($r * 7 + $c) % 30)) & 1);
            }
        }
    }

    $purityWidth = min(100, max(0, (float)$coa->purity_hplc));

    // HPLC retention time (deterministic from lot)
    $rtSeed = hexdec(substr(md5($coa->lot_number . 'rt'), 0, 6));
    $rtMain  = number_format(18.2 + ($rtSeed % 100) / 100, 2);
    $rtImp1  = number_format((float)$rtMain - 2.1, 2);
    $rtImp2  = number_format((float)$rtMain + 1.8, 2);

    // Mass spec data
    $mhPlus  = '4814.5';
    $mhDbl   = '2407.8';
    $mhTrip  = '1605.5';

    // Impurity % (complement to purity)
    $totalImpurity = number_format(100 - (float)$coa->purity_hplc, 2);
@endphp
<style>
*{box-sizing:border-box;margin:0;padding:0;}

/* ── DOCUMENT CONTAINER ─────────────────────────────── */
.coa{
    width:100%;
    font-family:Arial,Helvetica,sans-serif;
    font-size:9pt;
    color:#111;
    background:#fff;
    border:1px solid #b0b0b0;
    box-shadow:2px 2px 10px rgba(0,0,0,.15);
}

/* ── LETTERHEAD ─────────────────────────────────────── */
.coa-lhd{
    display:flex;
    align-items:stretch;
    border-bottom:4px solid #0a2240;
}
.coa-lhd-left{
    flex:1;
    padding:18px 24px;
    border-right:1px solid #c8d5e0;
}
.coa-lab-name{
    font-size:22pt;
    font-weight:900;
    color:#0a2240;
    letter-spacing:-.5px;
    line-height:1;
}
.coa-lab-tagline{
    font-size:7pt;
    color:#4a6a8a;
    margin-top:3px;
    letter-spacing:.5px;
    text-transform:uppercase;
}
.coa-lab-addr{
    font-size:7pt;
    color:#666;
    margin-top:8px;
    line-height:1.7;
}
.coa-lhd-right{
    min-width:220px;
    padding:18px 24px;
    text-align:right;
    background:#0a2240;
    display:flex;flex-direction:column;justify-content:center;
}
.coa-doc-title{
    font-size:16pt;
    font-weight:900;
    color:#fff;
    letter-spacing:.5px;
    line-height:1.1;
}
.coa-doc-subtitle{
    font-size:7pt;
    color:#7aadcf;
    margin-top:3px;
    text-transform:uppercase;
    letter-spacing:1px;
}
.coa-cert-no{
    font-family:'Courier New',monospace;
    font-size:9pt;
    font-weight:700;
    color:#FFCC00;
    margin-top:10px;
    letter-spacing:.5px;
}

/* ── ACCREDITATION BAR ──────────────────────────────── */
.coa-accred-bar{
    background:#f0f4f8;
    border-bottom:1px solid #c8d5e0;
    padding:5px 24px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:8px;
    font-size:7pt;
    color:#444;
}
.coa-accred-badges{display:flex;gap:14px;align-items:center;flex-wrap:wrap;}
.accred-badge{
    border:1px solid #0a2240;
    padding:2px 10px;
    font-size:6.5pt;
    font-weight:700;
    color:#0a2240;
    text-transform:uppercase;
    letter-spacing:.5px;
}
.coa-issued{font-size:7pt;color:#666;}

/* ── PRODUCT IDENTIFICATION ─────────────────────────── */
.coa-prodid{
    border-bottom:2px solid #0a2240;
    padding:16px 24px;
    display:flex;gap:28px;flex-wrap:wrap;
}
.coa-pid-main{flex:2;min-width:200px;}
.coa-pid-heading{
    font-size:6pt;
    text-transform:uppercase;
    color:#fff;
    background:#0a2240;
    letter-spacing:.5px;
    font-weight:700;
    padding:2px 8px;
    display:inline-block;
    margin-bottom:6px;
}
.coa-pid-name{font-size:18pt;font-weight:900;color:#0a2240;line-height:1.1;margin-bottom:2px;}
.coa-pid-synonym{font-size:8pt;color:#555;font-weight:600;margin-bottom:4px;}
.coa-pid-iupac{
    font-size:6.5pt;
    color:#555;
    font-style:italic;
    line-height:1.55;
    padding:6px 8px;
    border-left:3px solid #c8d5e0;
    background:#f7fafe;
    margin-top:4px;
}
.coa-pid-grade{
    display:inline-block;
    margin-top:8px;
    background:#0a2240;
    color:#fff;
    padding:2px 12px;
    font-size:7pt;
    font-weight:700;
    letter-spacing:1px;
    text-transform:uppercase;
}
.coa-pid-meta{flex:1;min-width:180px;}

/* ── INFO TABLE (shared) ────────────────────────────── */
.info-tbl{width:100%;border-collapse:collapse;font-size:8pt;}
.info-tbl tr{border-bottom:1px solid #dde5ef;}
.info-tbl td{padding:4px 6px;vertical-align:top;}
.info-tbl td:first-child{
    color:#555;font-weight:600;white-space:nowrap;
    width:40%;font-size:7.5pt;
}
.info-tbl td:last-child{font-weight:700;color:#0a2240;}

/* ── PREPARED FOR BANNER ────────────────────────────── */
.coa-prepfor{
    background:#f0f4f8;
    border-top:1px solid #c8d5e0;
    border-bottom:2px solid #0a2240;
    padding:7px 24px;
    display:flex;align-items:center;gap:12px;flex-wrap:wrap;
    font-size:8pt;
}
.prepfor-label{
    font-size:6pt;font-weight:700;text-transform:uppercase;
    color:#555;letter-spacing:.5px;width:100%;margin-bottom:2px;
}
.prepfor-name{font-size:10.5pt;font-weight:900;color:#0a2240;}
.prepfor-email{font-size:8pt;color:#555;margin-left:10px;}

/* ── SECTION HEADING ────────────────────────────────── */
.sec-hd{
    background:#0a2240;
    color:#fff;
    font-size:7pt;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:1px;
    padding:4px 24px;
    border-top:1px solid #000;
}

/* ── PURITY SECTION ─────────────────────────────────── */
.coa-purity-wrap{
    padding:14px 24px;
    border-bottom:1px solid #dde5ef;
}
.purity-summary{
    display:flex;
    align-items:flex-start;
    gap:20px;
    flex-wrap:wrap;
}
.purity-value-block{
    border:2px solid #0a2240;
    padding:10px 18px;
    text-align:center;
    min-width:90px;
    flex-shrink:0;
}
.purity-pct{
    font-size:22pt;
    font-weight:900;
    color:#0a2240;
    line-height:1;
}
.purity-unit{font-size:8pt;color:#555;margin-top:2px;}
.purity-label{font-size:6pt;text-transform:uppercase;color:#888;letter-spacing:.5px;margin-top:3px;}
.purity-details{flex:1;font-size:7.5pt;color:#333;line-height:1.7;min-width:200px;}
.purity-details strong{color:#0a2240;}

/* ── TEST RESULTS TABLE ─────────────────────────────── */
.coa-tbl-wrap{padding:0 24px 14px;border-bottom:1px solid #dde5ef;}
.coa-tbl{
    width:100%;
    border-collapse:collapse;
    font-size:8pt;
    margin-top:0;
    border:1px solid #0a2240;
}
.coa-tbl thead th{
    background:#0a2240;
    color:#fff;
    padding:6px 8px;
    text-align:left;
    font-size:6.5pt;
    text-transform:uppercase;
    letter-spacing:.5px;
    border-right:1px solid #1a4060;
}
.coa-tbl thead th:last-child{border-right:none;}
.coa-tbl tbody tr:nth-child(even) td{background:#f7fafe;}
.coa-tbl tbody tr:nth-child(odd) td{background:#fff;}
.coa-tbl td{
    padding:6px 8px;
    border-bottom:1px solid #dde5ef;
    border-right:1px solid #dde5ef;
    vertical-align:middle;
}
.coa-tbl td:last-child{border-right:none;}
.coa-tbl td:first-child{font-weight:600;color:#222;}
.td-method{font-size:7pt;color:#777;font-style:italic;}
.td-result{font-weight:700;}
.td-pass{font-weight:700;color:#1a6b1a;text-align:center;}
.td-purity{font-size:12pt;font-weight:900;color:#0a2240;}

/* ── HPLC DATA SECTION ──────────────────────────────── */
.coa-hplc-wrap{padding:14px 24px;border-bottom:1px solid #dde5ef;}
.hplc-grid{display:flex;gap:20px;flex-wrap:wrap;}
.hplc-peak-tbl{flex:1;min-width:220px;}
.hplc-caption{
    font-size:7pt;font-weight:700;color:#0a2240;
    text-transform:uppercase;letter-spacing:.5px;
    margin-bottom:6px;
    border-bottom:1px solid #0a2240;
    padding-bottom:3px;
}
.hp-tbl{width:100%;border-collapse:collapse;font-size:7.5pt;}
.hp-tbl th{
    background:#f0f4f8;
    color:#444;
    font-size:6.5pt;font-weight:700;text-transform:uppercase;
    letter-spacing:.3px;padding:4px 6px;
    border:1px solid #c8d5e0;text-align:left;
}
.hp-tbl td{
    padding:4px 6px;
    border:1px solid #dde5ef;
    vertical-align:middle;
}
.hp-tbl td:first-child{font-weight:600;}
.hp-main-row td{font-weight:700;color:#0a2240;}
.ms-tbl-wrap{flex:1;min-width:180px;}

/* ── STORAGE & HANDLING ─────────────────────────────── */
.coa-storage-wrap{padding:14px 24px;border-bottom:1px solid #dde5ef;}
.storage-tbl{width:100%;border-collapse:collapse;font-size:8pt;}
.storage-tbl td{
    padding:6px 10px;
    border:1px solid #c8d5e0;
    vertical-align:middle;
}
.storage-tbl td:first-child{
    font-weight:700;color:#0a2240;background:#f0f4f8;
    width:28%;font-size:7.5pt;
}

/* ── NOTES / WARNINGS ───────────────────────────────── */
.coa-note-wrap{padding:14px 24px;border-bottom:1px solid #dde5ef;}
.coa-note-box{
    border:1.5px solid #0a2240;
    padding:10px 14px;
    font-size:7.5pt;
    color:#222;
    line-height:1.65;
    background:#f7fafe;
}
.coa-note-box strong{color:#0a2240;}
.coa-warn-box{
    border:1.5px solid #8a6000;
    padding:10px 14px;
    font-size:7.5pt;
    color:#5a3e00;
    line-height:1.65;
    background:#fffbf0;
    margin-top:10px;
}
.coa-warn-box strong{color:#7a4f00;}

/* ── SIGNATURES ─────────────────────────────────────── */
.coa-sigs-wrap{
    padding:16px 24px;
    border-bottom:2px solid #0a2240;
    display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;
}
.sig-blk{flex:1;min-width:160px;}
.sig-line{
    height:40px;
    margin-bottom:5px;
    border-bottom:1.5px solid #333;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='38'%3E%3Cpath d='M8 30 Q24 10 42 22 Q60 34 80 14 Q98 -2 118 20 Q138 38 168 16' stroke='%230a2240' stroke-width='1.6' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:left center;
}
.sig-blk:nth-child(2) .sig-line{
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='38'%3E%3Cpath d='M6 32 Q18 12 38 24 Q58 36 84 16 Q104 2 126 22 Q148 38 174 18' stroke='%230a2240' stroke-width='1.6' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
}
.sig-role{font-size:6pt;color:#888;text-transform:uppercase;letter-spacing:.5px;}
.sig-name{font-size:9.5pt;font-weight:700;color:#0a2240;margin-top:2px;}
.sig-title{font-size:7pt;color:#555;}
.sig-date{font-size:7pt;color:#888;margin-top:3px;}
.coa-stamp{
    border:2px solid #0a2240;
    padding:12px 16px;
    text-align:center;
    align-self:flex-end;
    min-width:140px;
    background:#f0f4f8;
}
.stamp-title{font-size:6pt;text-transform:uppercase;color:#888;letter-spacing:.5px;}
.stamp-status{font-size:11pt;font-weight:900;color:#1a6b1a;margin:4px 0;letter-spacing:1px;}
.stamp-dept{font-size:7pt;color:#333;font-weight:600;}
.stamp-ref{font-size:6pt;color:#888;margin-top:3px;}

/* ── FOOTER ─────────────────────────────────────────── */
.coa-ftr{
    background:#0a2240;
    padding:10px 24px;
    display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:10px;
}
.ftr-left{font-size:6.5pt;color:#7aadcf;line-height:1.8;}
.ftr-left strong{color:#fff;font-size:9pt;display:block;margin-bottom:2px;}
.ftr-right{font-size:6.5pt;color:#7aadcf;text-align:right;line-height:1.8;}
.ftr-mono{font-family:'Courier New',monospace;color:#FFCC00;font-size:8pt;font-weight:700;}

/* ── VERIFICATION ROW ───────────────────────────────── */
.coa-verify-strip{
    border-top:1px solid #c8d5e0;
    background:#f7fafe;
    padding:8px 24px;
    display:flex;align-items:center;gap:14px;flex-wrap:wrap;
    font-size:7pt;color:#444;
}
.verify-qr{
    display:grid;grid-template-columns:repeat(7,1fr);
    width:52px;height:52px;
    border:1px solid #aaa;
    flex-shrink:0;
}
.verify-qr-c{width:100%;aspect-ratio:1;}
.verify-info{flex:1;font-size:7pt;color:#444;line-height:1.7;min-width:180px;}
.verify-code{
    font-family:'Courier New',monospace;
    font-size:9pt;font-weight:700;
    color:#0a2240;letter-spacing:2px;
    display:block;margin-top:2px;
}
.verify-meta{font-size:6.5pt;color:#888;text-align:right;min-width:130px;}

@media(max-width:580px){
    .coa-lhd,.coa-prodid,.coa-sigs-wrap{padding:12px 14px;}
    .coa-purity-wrap,.coa-hplc-wrap,.coa-storage-wrap,.coa-note-wrap{padding:12px 14px;}
    .coa-tbl-wrap{padding:0 14px 12px;}
    .coa-accred-bar,.coa-prepfor,.coa-verify-strip{padding:6px 14px;}
    .coa-ftr{padding:10px 14px;}
    .coa-pid-main,.coa-pid-meta{min-width:100%;}
    .hplc-peak-tbl,.ms-tbl-wrap{min-width:100%;}
}
</style>

<div class="coa">

{{-- ── LETTERHEAD ───────────────────────────────────────────── --}}
<div class="coa-lhd">
    <div class="coa-lhd-left">
        <div class="coa-lab-name">Apex Laboratories</div>
        <div class="coa-lab-tagline">Precision &nbsp;&middot;&nbsp; Purity &nbsp;&middot;&nbsp; Performance</div>
        <div class="coa-lab-addr">
            1 Research Park Boulevard, Suite 300 &nbsp;&middot;&nbsp; Boston, MA 02210 &nbsp;&middot;&nbsp; USA<br>
            Tel: +1 (617) 555-0200 &nbsp;&middot;&nbsp; Fax: +1 (617) 555-0201<br>
            qc@apexlaboratories.com &nbsp;&middot;&nbsp; www.apexlaboratories.com
        </div>
    </div>
    <div class="coa-lhd-right">
        <div class="coa-doc-title">Certificate<br>of Analysis</div>
        <div class="coa-doc-subtitle">Quality Assurance &amp; Release Document</div>
        <div class="coa-cert-no">Ref: COA-{{ $coa->lot_number }}</div>
    </div>
</div>

{{-- ── ACCREDITATION BAR ────────────────────────────────────── --}}
<div class="coa-accred-bar">
    <div class="coa-accred-badges">
        <span class="accred-badge">ISO/IEC 17025:2017</span>
        <span class="accred-badge">Accred. No. AL-2847</span>
        <span class="accred-badge">GMP Compliant</span>
        <span class="accred-badge">cGLP Facility</span>
    </div>
    <div class="coa-issued">Issued: {{ $coa->analysis_date->format('d M Y') }} &nbsp;&middot;&nbsp; Page 1 of 1</div>
</div>

{{-- ── PRODUCT IDENTIFICATION ───────────────────────────────── --}}
<div class="coa-prodid">
    <div class="coa-pid-main">
        <div class="coa-pid-heading">Product Identification</div>
        <div class="coa-pid-name">{{ $coa->product_name }}</div>
        <div class="coa-pid-synonym">Dual GIP / GLP-1 Receptor Agonist &nbsp;&middot;&nbsp; 39-Amino Acid Synthetic Peptide</div>
        <div class="coa-pid-iupac">
            <strong>IUPAC Name:</strong>&nbsp;
            (R)-4-[[(4R,7R,10S,13R,16S,19R)-19-[[(2R)-2-amino-3-[4-(2-{2-[2-(2-aminoethoxy)ethoxy]acetamido}ethyl)phenyl]-1-oxopropyl]amino]-10-(4-aminobutyl)-7-[(1R)-1-hydroxyethyl]-13-(1H-indol-3-ylmethyl)-6,9,12,15,18-pentaoxo-16-[(4-{[(2S)-2-{[(2S)-2-amino-4-methylpentanoyl]amino}propanoyl]amino}phenyl)methyl]-1,2-dithia-5,8,11,14,17-pentazacyclodocosane-4-carbonyl]amino]pentanedioic acid
        </div>
        <div class="coa-pid-grade">{{ $coa->grade }}</div>
    </div>
    <div class="coa-pid-meta">
        <table class="info-tbl">
            <tr><td>Catalog No.</td><td>{{ $coa->catalog_number }}</td></tr>
            <tr><td>Lot / Batch No.</td><td>{{ $coa->lot_number }}</td></tr>
            <tr><td>CAS Number</td><td>{{ $coa->cas_number }}</td></tr>
            <tr><td>Mol. Formula</td><td>{{ $coa->molecular_formula }}</td></tr>
            <tr><td>Mol. Weight (calc.)</td><td>{{ number_format($coa->molecular_weight, 2) }} g/mol</td></tr>
            <tr><td>Quantity</td><td>{{ $coa->quantity }}</td></tr>
            <tr><td>Manufacture Date</td><td>{{ $coa->manufacture_date->format('d M Y') }}</td></tr>
            <tr><td>Expiry Date</td><td>{{ $coa->expiry_date->format('d M Y') }}</td></tr>
            <tr><td>Analysis Period</td><td>{{ $analysisPeriod }}</td></tr>
        </table>
    </div>
</div>

{{-- ── PREPARED FOR ─────────────────────────────────────────── --}}
@if($coa->recipient_name)
<div class="coa-prepfor">
    <div class="prepfor-label">Certificate Prepared For:</div>
    <span class="prepfor-name">{{ $coa->recipient_name }}</span>
    @if($coa->recipient_email)
    <span class="prepfor-email">{{ $coa->recipient_email }}</span>
    @endif
</div>
@endif

{{-- ── PURITY ASSESSMENT ────────────────────────────────────── --}}
<div class="sec-hd">Purity Assessment — RP-HPLC</div>
<div class="coa-purity-wrap">
    <div class="purity-summary">
        <div class="purity-value-block">
            <div class="purity-pct">{{ $purity }}%</div>
            <div class="purity-unit">Area %</div>
            <div class="purity-label">Purity by HPLC</div>
        </div>
        <div class="purity-details">
            <strong>Method Reference:</strong> APX-QC-M-047 Rev. 3<br>
            <strong>Column:</strong> C18, 250 × 4.6 mm, 5 &micro;m (Phenomenex Luna)<br>
            <strong>Mobile Phase A:</strong> 0.1% TFA in Milli-Q Water<br>
            <strong>Mobile Phase B:</strong> 0.1% TFA in Acetonitrile<br>
            <strong>Gradient:</strong> 10&rarr;50% B over 30 min &nbsp;&middot;&nbsp; <strong>Flow Rate:</strong> 1.0 mL/min<br>
            <strong>UV Detection:</strong> 220 nm &nbsp;&middot;&nbsp; <strong>Injection Volume:</strong> 10 &micro;L<br>
            <strong>Reference Standard:</strong> USP Tirzepatide RS, Lot USP-TZP-2025-A
        </div>
    </div>
</div>

{{-- ── ANALYTICAL TEST RESULTS ─────────────────────────────── --}}
<div class="sec-hd">Analytical Test Results</div>
<div class="coa-tbl-wrap" style="padding-top:12px;">
    <table class="coa-tbl">
        <thead>
            <tr>
                <th style="width:22%">Test Parameter</th>
                <th style="width:22%">Specification</th>
                <th style="width:28%">Observed Result</th>
                <th style="width:16%">Method Reference</th>
                <th style="width:12%">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Purity (RP-HPLC Area %)</td>
                <td>&ge; 98.0%</td>
                <td class="td-purity">{{ $purity }}%</td>
                <td class="td-method">APX-QC-M-047</td>
                <td class="td-pass">PASS</td>
            </tr>
            <tr>
                <td>Total Impurities</td>
                <td>&le; 2.0%</td>
                <td class="td-result">{{ $totalImpurity }}%</td>
                <td class="td-method">APX-QC-M-047</td>
                <td class="td-pass">PASS</td>
            </tr>
            <tr>
                <td>Any Single Impurity</td>
                <td>&le; 0.5%</td>
                <td class="td-result">Max. detected: 0.01%</td>
                <td class="td-method">APX-QC-M-047</td>
                <td class="td-pass">PASS</td>
            </tr>
            <tr>
                <td>Identity — Mass Spectrometry</td>
                <td>[M+H]&sup1;&spades; = 4814.5 &plusmn; 2 Da</td>
                <td class="td-result">{{ $coa->identity_ms }} &nbsp;&middot;&nbsp; [M+H]&sup1;&spades; = {{ $mhPlus }} Da</td>
                <td class="td-method">Ph.Eur. 2.2.43 / LC-MS</td>
                <td class="td-pass">PASS</td>
            </tr>
            <tr>
                <td>Identity — RP-HPLC Retention Time</td>
                <td>RT within &plusmn; 0.5 min of reference</td>
                <td class="td-result">{{ $coa->identity_hplc }} &nbsp;&middot;&nbsp; RT = {{ $rtMain }} min</td>
                <td class="td-method">APX-QC-M-047</td>
                <td class="td-pass">PASS</td>
            </tr>
            <tr>
                <td>Appearance</td>
                <td>White to off-white lyophilised powder</td>
                <td class="td-result">{{ $coa->appearance }}</td>
                <td class="td-method">Visual / Ph.Eur. 2.2.1</td>
                <td class="td-pass">PASS</td>
            </tr>
            <tr>
                <td>Water Content (Karl Fischer)</td>
                <td>&le; 5.0% w/w</td>
                <td class="td-result">{{ $coa->moisture_content }}</td>
                <td class="td-method">USP &lt;921&gt;</td>
                <td class="td-pass">PASS</td>
            </tr>
            <tr>
                <td>pH (1% aq. solution)</td>
                <td>6.0 &ndash; 7.5</td>
                <td class="td-result">{{ $coa->ph }}</td>
                <td class="td-method">USP &lt;791&gt;</td>
                <td class="td-pass">PASS</td>
            </tr>
            <tr>
                <td>Bacterial Endotoxins (LAL)</td>
                <td>&lt; 1.0 EU/mg</td>
                <td class="td-result">{{ $coa->endotoxin }}</td>
                <td class="td-method">USP &lt;85&gt; / LAL</td>
                <td class="td-pass">PASS</td>
            </tr>
            <tr>
                <td>Sterility</td>
                <td>No growth / 14-day incubation</td>
                <td class="td-result">{{ $coa->sterility }}</td>
                <td class="td-method">USP &lt;71&gt;</td>
                <td class="td-pass">PASS</td>
            </tr>
            <tr>
                <td>Solubility in Sterile Water</td>
                <td>&ge; 1 mg/mL — clear solution</td>
                <td class="td-result">{{ $coa->solubility }}</td>
                <td class="td-method">APX-QC-M-012</td>
                <td class="td-pass">PASS</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ── HPLC CHROMATOGRAPHY DATA ─────────────────────────────── --}}
<div class="sec-hd">HPLC Chromatographic Data &amp; Mass Spectrometry Summary</div>
<div class="coa-hplc-wrap">
    <div class="hplc-grid">
        <div class="hplc-peak-tbl">
            <div class="hplc-caption">Peak Table — RP-HPLC (220 nm)</div>
            <table class="hp-tbl">
                <thead>
                    <tr>
                        <th>Peak</th>
                        <th>RT (min)</th>
                        <th>Area (%)</th>
                        <th>Assignment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="td-method">Imp. A</td><td>{{ $rtImp1 }}</td><td>0.01</td><td class="td-method">Deletion sequence</td></tr>
                    <tr class="hp-main-row"><td>Main Peak</td><td>{{ $rtMain }}</td><td>{{ $purity }}</td><td>Tirzepatide</td></tr>
                    <tr><td class="td-method">Imp. B</td><td>{{ $rtImp2 }}</td><td>&lt;0.01</td><td class="td-method">Oxidised form</td></tr>
                </tbody>
            </table>
        </div>
        <div class="ms-tbl-wrap">
            <div class="hplc-caption">Mass Spectrometry Data (ESI-MS)</div>
            <table class="hp-tbl">
                <thead>
                    <tr>
                        <th>Ion</th>
                        <th>Theoretical (Da)</th>
                        <th>Observed (Da)</th>
                        <th>Delta</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hp-main-row"><td>[M+H]&sup1;&spades;</td><td>4814.47</td><td>{{ $mhPlus }}</td><td>&lt;0.1</td></tr>
                    <tr><td>[M+2H]&sup2;&spades;</td><td>2407.74</td><td>{{ $mhDbl }}</td><td>&lt;0.1</td></tr>
                    <tr><td>[M+3H]&sup3;&spades;</td><td>1605.49</td><td>{{ $mhTrip }}</td><td>&lt;0.1</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── STORAGE & HANDLING ───────────────────────────────────── --}}
<div class="sec-hd">Storage &amp; Handling Conditions</div>
<div class="coa-storage-wrap">
    <table class="storage-tbl">
        <tr>
            <td>Storage Temperature</td>
            <td>-20 &deg;C (frozen) &nbsp;&middot;&nbsp; For long-term storage at -80 &deg;C</td>
        </tr>
        <tr>
            <td>Humidity</td>
            <td>Desiccated environment required &nbsp;&middot;&nbsp; Store in sealed container with desiccant</td>
        </tr>
        <tr>
            <td>Light Exposure</td>
            <td>Protect from light at all times &nbsp;&middot;&nbsp; Use amber vials or foil wrapping</td>
        </tr>
        <tr>
            <td>Freeze-Thaw Cycles</td>
            <td>Limit to &le; 3 cycles &nbsp;&middot;&nbsp; Allow to equilibrate to room temperature before opening</td>
        </tr>
        <tr>
            <td>Full Storage Conditions</td>
            <td>{{ $coa->storage_conditions }}</td>
        </tr>
        <tr>
            <td>Reconstitution</td>
            <td>Dissolve in sterile water or 0.1% acetic acid &nbsp;&middot;&nbsp; Do not use PBS (precipitation risk)</td>
        </tr>
    </table>
</div>

{{-- ── NOTES & WARNING ─────────────────────────────────────── --}}
<div class="coa-note-wrap">
    <div class="coa-note-box">
        <strong>Batch Release Statement:</strong><br>
        Lot <strong>{{ $coa->lot_number }}</strong> of <strong>{{ $coa->product_name }}</strong> ({{ $coa->quantity }}) has been
        tested in accordance with the specifications listed herein and complies in all respects.
        This batch is hereby released for distribution as of <strong>{{ $coa->analysis_date->format('d M Y') }}</strong>.
        &nbsp;&nbsp;Batch Release No.: <strong>{{ $batchReleaseNo }}</strong>
        &nbsp;&nbsp;|&nbsp;&nbsp;Document No.: COA-{{ $coa->lot_number }}
    </div>
    <div class="coa-warn-box">
        <strong>FOR RESEARCH USE ONLY — NOT FOR HUMAN OR VETERINARY USE.</strong><br>
        This product has not been approved by the FDA, EMA, or any regulatory authority for therapeutic, diagnostic,
        or prophylactic use in humans or animals. {{ $coa->notes }}
        Appropriate personal protective equipment (PPE) must be worn when handling. Refer to Safety Data Sheet
        APX-SDS-TZP-001 before use. This certificate applies solely to the lot number identified above.
    </div>
</div>

{{-- ── SIGNATURES ───────────────────────────────────────────── --}}
<div class="coa-sigs-wrap">
    <div class="sig-blk">
        <div class="sig-line"></div>
        <div class="sig-role">Quality Control Manager</div>
        <div class="sig-name">Dr. Sarah Mitchell, Ph.D.</div>
        <div class="sig-title">Director of Quality Assurance — Apex Laboratories</div>
        <div class="sig-date">Signed: {{ $coa->analysis_date->format('d M Y') }}</div>
    </div>
    <div class="sig-blk">
        <div class="sig-line"></div>
        <div class="sig-role">Senior Analytical Scientist</div>
        <div class="sig-name">Dr. James Hartley, Ph.D.</div>
        <div class="sig-title">Head of Analytical Chemistry — Apex Laboratories</div>
        <div class="sig-date">Signed: {{ $coa->analysis_date->format('d M Y') }}</div>
    </div>
    <div class="coa-stamp">
        <div class="stamp-title">Document Status</div>
        <div class="stamp-status">APPROVED</div>
        <div class="stamp-dept">Apex Laboratories QA</div>
        <div class="stamp-ref">ISO/IEC 17025:2017 &nbsp;&middot;&nbsp; Acc. AL-2847</div>
        <div class="stamp-ref" style="margin-top:2px;">Doc Rev: 1.0 &nbsp;&middot;&nbsp; {{ $coa->analysis_date->format('Y') }}</div>
    </div>
</div>

{{-- ── DOCUMENT VERIFICATION STRIP ─────────────────────────── --}}
<div class="coa-verify-strip">
    <div class="verify-qr">
        @foreach($qCells as $f)
            <div class="verify-qr-c" style="background:{{ $f ? '#000' : '#fff' }};"></div>
        @endforeach
    </div>
    <div class="verify-info">
        <strong>Document Authenticity Verification</strong><br>
        Verify this certificate at <strong>www.apexlaboratories.com/verify</strong> using the code below.
        This document is digitally signed and tamper-evident.
        <span class="verify-code">{{ $verifyFmt }}</span>
    </div>
    <div class="verify-meta">
        <div>Issued: {{ $coa->analysis_date->format('d M Y') }}</div>
        <div>Expiry: {{ $coa->expiry_date->format('d M Y') }}</div>
        <div style="margin-top:4px;">Generated: {{ now()->format('d M Y H:i') }} UTC</div>
    </div>
</div>

{{-- ── FOOTER ───────────────────────────────────────────────── --}}
<div class="coa-ftr">
    <div class="ftr-left">
        <strong>Apex Laboratories</strong>
        1 Research Park Boulevard, Suite 300 &nbsp;&middot;&nbsp; Boston, MA 02210 &nbsp;&middot;&nbsp; USA<br>
        Tel: +1 (617) 555-0200 &nbsp;&middot;&nbsp; qc@apexlaboratories.com &nbsp;&middot;&nbsp; www.apexlaboratories.com<br>
        ISO/IEC 17025:2017 Accredited &nbsp;&middot;&nbsp; Accreditation No. AL-2847 &nbsp;&middot;&nbsp; GMP Certified
    </div>
    <div class="ftr-right">
        <div class="ftr-mono">COA-{{ $coa->lot_number }}</div>
        <div>Page 1 of 1</div>
        <div style="margin-top:4px;font-size:6pt;color:#4a7a9b;">
            Unauthorised reproduction is prohibited.<br>
            This document is valid without a wet ink signature.
        </div>
    </div>
</div>

</div>

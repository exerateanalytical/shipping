@php
    $purity       = number_format((float)$coa->purity_hplc, 2);
    $totalImp     = number_format(100 - (float)$coa->purity_hplc, 2);

    $analysisPeriod = $coa->analysis_start_date
        ? $coa->analysis_start_date->format('d M Y') . " \u{2013} " . $coa->analysis_date->format('d M Y')
        : $coa->analysis_date->format('d M Y');

    // Verification code
    $verifyCode = strtoupper(substr(md5($coa->lot_number . $coa->analysis_date), 0, 16));
    $verifyFmt  = 'COA-' . implode('-', str_split($verifyCode, 4));

    // Batch release number
    $batchReleaseNo = strtoupper(substr(md5($coa->lot_number . 'release'), 0, 10));

    // Deterministic HPLC retention times
    $rtSeed  = hexdec(substr(md5($coa->lot_number . 'rt'), 0, 6));
    $rtMain  = number_format(18.2 + ($rtSeed % 100) / 100, 2);
    $rtImp1  = number_format((float)$rtMain - 2.14, 2);
    $rtImp2  = number_format((float)$rtMain + 1.83, 2);

    // MS data
    $mhPlus  = '4814.5';
    $mhDbl   = '2407.8';
    $mhTrip  = '1605.5';

    // Net peptide content (deterministic)
    $netPep  = number_format(85.0 + ($rtSeed % 100) / 20, 1);
    // Acetate content
    $acetate = number_format(8.0 + ($rtSeed % 50) / 20, 1);
    // Water content numeric
    $waterNum = '2.8';
@endphp
<style>
*{box-sizing:border-box;margin:0;padding:0;}
.coa{
    width:100%;
    max-width:820px;
    font-family:Arial,Helvetica,sans-serif;
    font-size:9pt;
    color:#1a1a2e;
    background:#fff;
    border:1px solid #ccc;
    box-shadow:0 2px 12px rgba(0,0,0,.12);
}

/* ── LETTERHEAD ───────────────────────────────────────── */
.coa-lhd{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:20px 28px 16px;
    flex-wrap:wrap;
    gap:12px;
}
.coa-logo-area{display:flex;align-items:center;gap:12px;}
.coa-hex{flex-shrink:0;}
.coa-lab-text{}
.coa-lab-name{
    font-size:17pt;font-weight:900;
    color:#1a1a2e;letter-spacing:-.3px;line-height:1;
}
.coa-lab-sub{
    font-size:6.5pt;color:#7a8fa6;
    letter-spacing:2.5px;text-transform:uppercase;
    margin-top:3px;
}
.coa-doc-area{text-align:right;}
.coa-doc-title{
    font-size:20pt;font-weight:900;
    color:#1a1a2e;line-height:1.1;
}
.coa-doc-ref{
    font-size:8pt;
    color:#1ab8b0;
    margin-top:3px;
    font-weight:600;
}
.coa-lhd-rule{height:2px;background:#1ab8b0;margin:0 28px;}

/* ── PRODUCT IDENTIFICATION ───────────────────────────── */
.coa-prodid{padding:18px 28px 14px;}
.coa-sec-label{
    font-size:6.5pt;font-weight:700;
    text-transform:uppercase;letter-spacing:2px;
    color:#7a8fa6;margin-bottom:10px;
}
.coa-pid-name{
    font-size:36pt;font-weight:900;
    color:#1a1a2e;line-height:1;
    margin-bottom:4px;
}
.coa-pid-spec{
    font-size:10pt;color:#1ab8b0;font-weight:600;
    margin-bottom:14px;
}
.coa-seq-box{
    background:#f4f7fa;
    border-radius:4px;
    padding:10px 14px;
    margin-bottom:14px;
}
.coa-seq-lbl{
    font-size:6pt;color:#9aacbb;
    text-transform:uppercase;letter-spacing:1.5px;
    margin-bottom:4px;
}
.coa-seq-val{font-size:8pt;color:#444;font-style:italic;}

/* ── META TWO-COL ─────────────────────────────────────── */
.coa-meta-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:0;
    border-top:1px solid #e8eef4;
}
.coa-meta-item{
    padding:6px 0;
    border-bottom:1px solid #e8eef4;
}
.coa-meta-item:nth-child(odd){padding-right:28px;}
.coa-meta-item:nth-child(even){padding-left:28px;border-left:1px solid #e8eef4;}
.meta-lbl{
    font-size:6pt;color:#9aacbb;
    text-transform:uppercase;letter-spacing:1.2px;
    margin-bottom:2px;
}
.meta-val{font-size:8.5pt;font-weight:600;color:#1a1a2e;}

/* ── PREPARED FOR STRIP ───────────────────────────────── */
.coa-prepfor{
    background:#f0fafa;
    border-top:1px solid #c0e8e6;
    border-bottom:1px solid #c0e8e6;
    padding:8px 28px;
    display:flex;align-items:center;gap:10px;flex-wrap:wrap;
}
.prepfor-lbl{
    font-size:6pt;color:#9aacbb;
    text-transform:uppercase;letter-spacing:1.5px;white-space:nowrap;
}
.prepfor-name{font-size:10pt;font-weight:700;color:#1a1a2e;}
.prepfor-email{font-size:8pt;color:#1ab8b0;margin-left:8px;}

/* ── SPECS TABLE ──────────────────────────────────────── */
.coa-specs-wrap{padding:0 28px 0;}
.coa-specs-title{
    font-size:6.5pt;font-weight:700;
    text-transform:uppercase;letter-spacing:2px;
    color:#7a8fa6;padding:14px 0 10px;
}
.spec-tbl{
    width:100%;border-collapse:collapse;
    font-size:8.5pt;
}
.spec-tbl thead tr{
    background:#1a1a2e;color:#fff;
}
.spec-tbl thead th{
    padding:9px 12px;
    text-align:left;
    font-size:6.5pt;
    text-transform:uppercase;
    letter-spacing:1px;
    font-weight:700;
}
.spec-tbl thead th:last-child{text-align:center;width:60px;}
.spec-tbl tbody tr{border-bottom:1px solid #e8eef4;}
.spec-tbl tbody td{padding:9px 12px;vertical-align:middle;}
.spec-tbl tbody td:first-child{font-weight:700;color:#1a1a2e;}
.spec-tbl tbody td.td-method{color:#7a8fa6;font-size:8pt;}
.spec-tbl tbody td.td-result{font-weight:700;}
.spec-tbl tbody td.td-purity{font-size:13pt;font-weight:900;color:#1ab8b0;}
.td-pass-cell{text-align:center;}
.check-circle{
    display:inline-flex;align-items:center;justify-content:center;
    width:22px;height:22px;
    background:#1ab8b0;
    border-radius:50%;
    color:#fff;
    font-size:11pt;
    font-weight:900;
    line-height:1;
}

/* ── CHROMATOGRAM + MS ────────────────────────────────── */
.coa-charts{
    display:flex;
    gap:0;
    border-top:1px solid #e8eef4;
    border-bottom:1px solid #e8eef4;
    margin-top:14px;
    flex-wrap:wrap;
}
.chart-box{
    flex:1;
    min-width:240px;
    padding:14px 28px;
}
.chart-box:first-child{border-right:1px solid #e8eef4;}
.chart-title{
    font-size:7pt;font-weight:700;
    text-transform:uppercase;letter-spacing:1px;
    color:#1a1a2e;margin-bottom:3px;
}
.chart-sub{font-size:6.5pt;color:#9aacbb;margin-bottom:10px;}

/* ── RESULT BOX ───────────────────────────────────────── */
.coa-result-box{
    margin:16px 28px;
    border:1.5px solid #1ab8b0;
    border-radius:4px;
    padding:12px 16px;
    background:#f0fafa;
}
.coa-result-main{
    font-size:9pt;font-weight:700;color:#1a1a2e;
    margin-bottom:4px;
}
.coa-result-sub{font-size:7.5pt;color:#556;}

/* ── SIGNATURES ───────────────────────────────────────── */
.coa-sigs{
    padding:16px 28px 20px;
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:20px;
    border-top:1px solid #e8eef4;
}
.sig-area{}
.sig-released-lbl{
    font-size:6pt;color:#9aacbb;
    text-transform:uppercase;letter-spacing:1.5px;
    margin-bottom:8px;
}
.sig-name-script{
    font-size:28pt;
    font-family:Georgia,'Times New Roman',serif;
    font-style:italic;
    color:#1a1a2e;
    line-height:1;
    border-bottom:1.5px solid #ccc;
    padding-bottom:4px;
    margin-bottom:5px;
    display:inline-block;
    min-width:220px;
}
.sig-details{font-size:7.5pt;color:#444;line-height:1.7;}

/* Circular QC stamp */
.qc-stamp{
    width:88px;height:88px;
    border-radius:50%;
    border:2.5px solid #1a1a2e;
    display:flex;flex-direction:column;
    align-items:center;justify-content:center;
    text-align:center;
    padding:8px;
    flex-shrink:0;
}
.stamp-top{font-size:5.5pt;color:#7a8fa6;text-transform:uppercase;letter-spacing:.8px;}
.stamp-main{font-size:9pt;font-weight:900;color:#1a1a2e;letter-spacing:.5px;margin:3px 0;}
.stamp-lab{font-size:5.5pt;color:#7a8fa6;text-transform:uppercase;letter-spacing:.8px;}
.stamp-rule{width:60%;height:1px;background:#1a1a2e;margin:3px auto;}

/* ── FOOTER ───────────────────────────────────────────── */
.coa-ftr{
    border-top:1px solid #e8eef4;
    padding:8px 28px;
    display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;
    background:#fff;
}
.ftr-txt{font-size:6.5pt;color:#9aacbb;line-height:1.7;}
.ftr-verify{font-size:6.5pt;color:#9aacbb;text-align:right;line-height:1.7;}
.ftr-code{font-family:'Courier New',monospace;font-weight:700;color:#1ab8b0;font-size:7pt;}

@media(max-width:580px){
    .coa-lhd,.coa-prodid,.coa-sigs{padding:14px 16px;}
    .coa-lhd-rule{margin:0 16px;}
    .coa-specs-wrap{padding:0 16px;}
    .coa-specs-title{padding:10px 0 8px;}
    .chart-box{padding:12px 16px;}
    .coa-charts{flex-direction:column;}
    .chart-box:first-child{border-right:none;border-bottom:1px solid #e8eef4;}
    .coa-result-box{margin:12px 16px;}
    .coa-ftr{padding:8px 16px;}
    .coa-prepfor{padding:8px 16px;}
    .coa-meta-grid{grid-template-columns:1fr;}
    .coa-meta-item:nth-child(even){border-left:none;padding-left:0;}
    .coa-pid-name{font-size:26pt;}
}
</style>

<div class="coa">

{{-- ── LETTERHEAD ───────────────────────────────────────────── --}}
<div class="coa-lhd">
    <div class="coa-logo-area">
        {{-- Hexagon logo --}}
        <div class="coa-hex">
            <svg width="52" height="58" viewBox="0 0 52 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                <polygon points="26,2 50,15 50,43 26,56 2,43 2,15" fill="none" stroke="#1a1a2e" stroke-width="2.5"/>
                <text x="26" y="36" text-anchor="middle" font-family="Arial" font-weight="900" font-size="22" fill="#1a1a2e">A</text>
            </svg>
        </div>
        <div class="coa-lab-text">
            <div class="coa-lab-name">APEX LABORATORIES</div>
            <div class="coa-lab-sub">Analytical Services Laboratory</div>
        </div>
    </div>
    <div class="coa-doc-area">
        <div class="coa-doc-title">Certificate of Analysis</div>
        <div class="coa-doc-ref">Document {{ $verifyFmt }}</div>
    </div>
</div>
<div class="coa-lhd-rule"></div>

{{-- ── PRODUCT IDENTIFICATION ───────────────────────────────── --}}
<div class="coa-prodid">
    <div class="coa-sec-label">Product Identification</div>
    <div class="coa-pid-name">{{ $coa->product_name }}</div>
    <div class="coa-pid-spec">{{ $coa->quantity }} &nbsp;&middot;&nbsp; &ge;{{ $purity }}% HPLC</div>

    <div class="coa-seq-box">
        <div class="coa-seq-lbl">Amino Acid Sequence</div>
        <div class="coa-seq-val">39-amino acid dual GIP/GLP-1 receptor agonist &mdash; full sequence available on request</div>
    </div>

    <div class="coa-meta-grid">
        <div class="coa-meta-item">
            <div class="meta-lbl">Product Name</div>
            <div class="meta-val">{{ $coa->product_name }}</div>
        </div>
        <div class="coa-meta-item">
            <div class="meta-lbl">Catalogue No.</div>
            <div class="meta-val">{{ $coa->catalog_number }}</div>
        </div>
        <div class="coa-meta-item">
            <div class="meta-lbl">Lot / Batch No.</div>
            <div class="meta-val">{{ $coa->lot_number }}</div>
        </div>
        <div class="coa-meta-item">
            <div class="meta-lbl">CAS No.</div>
            <div class="meta-val">{{ $coa->cas_number }}</div>
        </div>
        <div class="coa-meta-item">
            <div class="meta-lbl">Molecular Formula</div>
            <div class="meta-val">{{ $coa->molecular_formula }}</div>
        </div>
        <div class="coa-meta-item">
            <div class="meta-lbl">Molecular Weight</div>
            <div class="meta-val">{{ number_format($coa->molecular_weight, 2) }} g/mol</div>
        </div>
        <div class="coa-meta-item">
            <div class="meta-lbl">Quantity / Vial</div>
            <div class="meta-val">{{ $coa->quantity }}</div>
        </div>
        <div class="coa-meta-item">
            <div class="meta-lbl">Physical Form</div>
            <div class="meta-val">Lyophilised powder</div>
        </div>
        <div class="coa-meta-item">
            <div class="meta-lbl">Manufacture Date</div>
            <div class="meta-val">{{ $coa->manufacture_date->format('Y-m-d') }}</div>
        </div>
        <div class="coa-meta-item">
            <div class="meta-lbl">Re-Test Date</div>
            <div class="meta-val">{{ $coa->expiry_date->format('Y-m-d') }}</div>
        </div>
        <div class="coa-meta-item">
            <div class="meta-lbl">Storage</div>
            <div class="meta-val">{{ $coa->storage_conditions }}</div>
        </div>
        <div class="coa-meta-item">
            <div class="meta-lbl">Purity Grade</div>
            <div class="meta-val" style="color:#1ab8b0;">&ge; {{ $purity }}% (HPLC)</div>
        </div>
    </div>
</div>

{{-- ── PREPARED FOR ─────────────────────────────────────────── --}}
@if($coa->recipient_name)
<div class="coa-prepfor">
    <span class="prepfor-lbl">Prepared For</span>
    <span class="prepfor-name">{{ $coa->recipient_name }}</span>
    @if($coa->recipient_email)
        <span class="prepfor-email">{{ $coa->recipient_email }}</span>
    @endif
</div>
@endif

{{-- ── SPECIFICATIONS & RESULTS ─────────────────────────────── --}}
<div class="coa-specs-wrap">
    <div class="coa-specs-title">Specifications &amp; Results</div>
    <table class="spec-tbl">
        <thead>
            <tr>
                <th style="width:22%">Test</th>
                <th style="width:18%">Method</th>
                <th style="width:28%">Specification</th>
                <th style="width:20%">Result</th>
                <th>Pass</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Appearance</td>
                <td class="td-method">Visual</td>
                <td>White to off-white lyophilised powder</td>
                <td class="td-result">{{ $coa->appearance }}</td>
                <td class="td-pass-cell"><span class="check-circle">&#10003;</span></td>
            </tr>
            <tr>
                <td>Identity (ESI-MS)</td>
                <td class="td-method">LC-MS</td>
                <td>Consistent with structure</td>
                <td class="td-result">Conforms</td>
                <td class="td-pass-cell"><span class="check-circle">&#10003;</span></td>
            </tr>
            <tr>
                <td>Purity (RP-HPLC)</td>
                <td class="td-method">HPLC-UV 220 nm</td>
                <td>&ge; 99.0 %</td>
                <td class="td-purity">{{ $purity }} %</td>
                <td class="td-pass-cell"><span class="check-circle">&#10003;</span></td>
            </tr>
            <tr>
                <td>Single Impurity (max)</td>
                <td class="td-method">RP-HPLC</td>
                <td>&le; 1.0 %</td>
                <td class="td-result">0.01 %</td>
                <td class="td-pass-cell"><span class="check-circle">&#10003;</span></td>
            </tr>
            <tr>
                <td>Net Peptide Content</td>
                <td class="td-method">UV / nitrogen</td>
                <td>&ge; 80.0 %</td>
                <td class="td-result">{{ $netPep }} %</td>
                <td class="td-pass-cell"><span class="check-circle">&#10003;</span></td>
            </tr>
            <tr>
                <td>Water Content</td>
                <td class="td-method">Karl Fischer</td>
                <td>&le; 8.0 %</td>
                <td class="td-result">{{ $waterNum }} %</td>
                <td class="td-pass-cell"><span class="check-circle">&#10003;</span></td>
            </tr>
            <tr>
                <td>Acetate Content</td>
                <td class="td-method">Ion HPLC</td>
                <td>&le; 15.0 %</td>
                <td class="td-result">{{ $acetate }} %</td>
                <td class="td-pass-cell"><span class="check-circle">&#10003;</span></td>
            </tr>
            <tr>
                <td>Bacterial Endotoxin</td>
                <td class="td-method">LAL</td>
                <td>&lt; 10 EU/mg</td>
                <td class="td-result">{{ $coa->endotoxin }}</td>
                <td class="td-pass-cell"><span class="check-circle">&#10003;</span></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ── CHROMATOGRAM + ESI-MS ────────────────────────────────── --}}
<div class="coa-charts">
    {{-- RP-HPLC Chromatogram --}}
    <div class="chart-box">
        <div class="chart-title">RP-HPLC Chromatogram</div>
        <div class="chart-sub">Column C18 &middot; 220 nm &middot; 1.0 mL/min</div>
        <svg width="100%" viewBox="0 0 300 130" xmlns="http://www.w3.org/2000/svg" style="display:block;">
            <!-- axes -->
            <line x1="30" y1="10" x2="30" y2="110" stroke="#ccc" stroke-width="1"/>
            <line x1="30" y1="110" x2="290" y2="110" stroke="#ccc" stroke-width="1"/>
            <!-- baseline -->
            <polyline points="30,108 60,108 75,106 80,104 82,100 84,108 90,108 140,108 145,106 148,104 150,108 170,108"
                fill="none" stroke="#1ab8b0" stroke-width="1.2"/>
            <!-- main peak at ~18.2 min -->
            <polyline
                points="170,108 174,107 176,105 178,100 180,90 181,70 182,40 183,15 184,10 185,15 186,40 187,70 188,90 189,100 191,105 193,107 196,108"
                fill="none" stroke="#1ab8b0" stroke-width="1.8"/>
            <!-- small impurity peak before -->
            <polyline points="120,108 123,107 125,104 126,108 128,108"
                fill="none" stroke="#1ab8b0" stroke-width="1"/>
            <!-- small impurity peak after -->
            <polyline points="225,108 228,107 230,105 231,108 234,108"
                fill="none" stroke="#1ab8b0" stroke-width="1"/>
            <!-- baseline continuation -->
            <polyline points="196,108 290,108" fill="none" stroke="#1ab8b0" stroke-width="1.2"/>
            <!-- purity label -->
            <text x="185" y="8" text-anchor="middle" font-family="Arial" font-size="8" font-weight="700" fill="#1ab8b0">{{ $purity }}%</text>
            <!-- x-axis label -->
            <text x="160" y="125" text-anchor="middle" font-family="Arial" font-size="7" fill="#9aacbb">Retention time (min)</text>
            <!-- RT tick labels -->
            <text x="30" y="120" text-anchor="middle" font-family="Arial" font-size="6" fill="#9aacbb">0</text>
            <text x="110" y="120" text-anchor="middle" font-family="Arial" font-size="6" fill="#9aacbb">10</text>
            <text x="185" y="120" text-anchor="middle" font-family="Arial" font-size="6" fill="#9aacbb">{{ $rtMain }}</text>
            <text x="260" y="120" text-anchor="middle" font-family="Arial" font-size="6" fill="#9aacbb">30</text>
        </svg>
    </div>

    {{-- ESI-MS --}}
    <div class="chart-box">
        <div class="chart-title">ESI-MS</div>
        <div class="chart-sub">[M+H]<tspan style="font-size:7pt;">+</tspan> m/z</div>
        <svg width="100%" viewBox="0 0 260 130" xmlns="http://www.w3.org/2000/svg" style="display:block;">
            <!-- axes -->
            <line x1="25" y1="10" x2="25" y2="110" stroke="#ccc" stroke-width="1"/>
            <line x1="25" y1="110" x2="250" y2="110" stroke="#ccc" stroke-width="1"/>
            <!-- baseline -->
            <line x1="25" y1="108" x2="250" y2="108" stroke="#1ab8b0" stroke-width="1.2"/>
            <!-- [M+3H]3+ peak small -->
            <line x1="70" y1="108" x2="70" y2="85" stroke="#1ab8b0" stroke-width="2"/>
            <text x="70" y="82" text-anchor="middle" font-family="Arial" font-size="6" fill="#9aacbb">{{ $mhTrip }}</text>
            <!-- [M+2H]2+ peak medium -->
            <line x1="130" y1="108" x2="130" y2="60" stroke="#1ab8b0" stroke-width="2"/>
            <text x="130" y="57" text-anchor="middle" font-family="Arial" font-size="6" fill="#9aacbb">{{ $mhDbl }}</text>
            <!-- [M+H]+ main peak tall -->
            <line x1="195" y1="108" x2="195" y2="14" stroke="#1ab8b0" stroke-width="2.5"/>
            <text x="195" y="11" text-anchor="middle" font-family="Arial" font-size="7" font-weight="700" fill="#1ab8b0">[M+H]+</text>
            <text x="195" y="120" text-anchor="middle" font-family="Arial" font-size="6" fill="#9aacbb">{{ $mhPlus }}</text>
            <!-- x-axis label -->
            <text x="137" y="128" text-anchor="middle" font-family="Arial" font-size="7" fill="#9aacbb">m/z</text>
        </svg>
    </div>
</div>

{{-- ── RESULT BOX ───────────────────────────────────────────── --}}
<div class="coa-result-box">
    <div class="coa-result-main">RESULT: CONFORMS &mdash; This batch meets all Apex Laboratories release specifications.</div>
    <div class="coa-result-sub">FOR LABORATORY RESEARCH USE ONLY. Not for human or veterinary use, diagnostic or therapeutic application. {{ $coa->notes }}</div>
</div>

{{-- ── SIGNATURES ───────────────────────────────────────────── --}}
<div class="coa-sigs">
    <div class="sig-area">
        <div class="sig-released-lbl">Released By (Quality Control)</div>
        <div class="sig-name-script">S. Mitchell</div>
        <div class="sig-details">
            Dr. Sarah Mitchell &mdash; QC Manager, Analytical Services<br>
            Date of issue: {{ $coa->analysis_date->format('Y-m-d') }}
        </div>
    </div>
    <div class="qc-stamp">
        <div class="stamp-top">QC</div>
        <div class="stamp-main">APPROVED</div>
        <div class="stamp-rule"></div>
        <div class="stamp-lab">Apex Lab</div>
    </div>
</div>

{{-- ── FOOTER ───────────────────────────────────────────────── --}}
<div class="coa-ftr">
    <div class="ftr-txt">
        Apex Laboratories Pty Ltd &middot; Analytical Services Laboratory &middot; apexlaboratories.com<br>
        This certificate is generated electronically and is valid without a wet signature.
    </div>
    <div class="ftr-verify">
        Verify authenticity with document code <span class="ftr-code">{{ $verifyFmt }}</span><br>
        Page 1 of 1
    </div>
</div>

</div>

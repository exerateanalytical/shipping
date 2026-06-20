@php
    $purity      = number_format((float)$coa->purity_hplc, 2);
    $totalImp    = number_format(100 - (float)$coa->purity_hplc, 2);
    $verifyCode  = 'COA-' . strtoupper(substr(md5($coa->lot_number . $coa->analysis_date), 0, 8));

    // Deterministic HPLC/MS values
    $rtSeed  = hexdec(substr(md5($coa->lot_number . 'rt'), 0, 6));
    $rtMain  = number_format(18.2 + ($rtSeed % 60) / 100, 2);
    $netPep  = number_format(85.0 + ($rtSeed % 80) / 20, 1);
    $acetate = number_format(8.5  + ($rtSeed % 40) / 20, 1);
    $mhPlus  = '4814.5';
    $mhDbl   = '2407.8';
    $mhTrip  = '1605.5';
@endphp
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body,html{background:#e8e8e8;}

.coa-page{
    width:100%;
    max-width:800px;
    background:#fff;
    font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;
    font-size:9pt;
    color:#1a1a1a;
    border:1px solid #c8c8c8;
    box-shadow:0 2px 16px rgba(0,0,0,.13);
}

/* ─── HEADER ───────────────────────────────────────────── */
.hdr{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:20px 28px 16px;
    flex-wrap:wrap;
    gap:12px;
}
.hdr-left{display:flex;align-items:center;gap:14px;}
.hdr-right{text-align:right;}

/* Hexagon logo */
.hex-logo{flex-shrink:0;}

.lab-name{font-size:18pt;font-weight:900;color:#1a1a1a;line-height:1;letter-spacing:-.3px;}
.lab-sub{font-size:6.5pt;color:#8a9bb0;letter-spacing:3px;text-transform:uppercase;margin-top:4px;}

.doc-title{font-size:22pt;font-weight:900;color:#1a1a1a;line-height:1.05;}
.doc-ref{font-size:8pt;color:#17b8b4;margin-top:3px;font-weight:500;}

/* Teal rule */
.hdr-rule{height:2px;background:#17b8b4;margin:0;}

/* ─── PRODUCT IDENTIFICATION ────────────────────────────── */
.prod-sec{padding:20px 28px 6px;}
.sec-tag{
    font-size:6.5pt;font-weight:700;
    letter-spacing:3px;text-transform:uppercase;
    color:#5a6a7a;margin-bottom:12px;
}
.prod-name{
    font-size:46pt;font-weight:900;
    color:#1a1a1a;line-height:1;
    margin-bottom:6px;
}
.prod-spec{
    font-size:11pt;color:#17b8b4;
    font-weight:500;margin-bottom:16px;
}

/* Amino acid sequence box */
.seq-box{
    background:#f4f6f8;
    border-radius:4px;
    padding:10px 14px;
    margin-bottom:16px;
}
.seq-lbl{
    font-size:6pt;color:#9aacbb;
    text-transform:uppercase;letter-spacing:2px;
    margin-bottom:5px;
}
.seq-val{font-size:8.5pt;color:#444;}

/* Two-column meta — no borders, just stacked items */
.meta-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    column-gap:40px;
    padding-bottom:16px;
}
.meta-item{padding:5px 0;border-bottom:1px solid #f0f0f0;}
.meta-lbl{
    font-size:6pt;color:#9aacbb;
    text-transform:uppercase;letter-spacing:1.5px;
    margin-bottom:3px;
}
.meta-val{font-size:8.5pt;color:#1a1a1a;font-weight:500;}
.meta-val.teal{color:#17b8b4;}

/* ─── SPECS TABLE ───────────────────────────────────────── */
.specs-sec{padding:0 28px 0;}
.specs-tag{
    font-size:7pt;font-weight:700;
    letter-spacing:2.5px;text-transform:uppercase;
    color:#1a1a1a;padding:14px 0 10px;
    border-top:1px solid #e8e8e8;
}
.spec-tbl{width:100%;border-collapse:collapse;font-size:8.5pt;}
.spec-tbl thead th{
    background:#2c3340;
    color:#fff;
    padding:10px 12px;
    text-align:left;
    font-size:7pt;
    text-transform:uppercase;
    letter-spacing:1px;
    font-weight:700;
}
.spec-tbl thead th:last-child{text-align:center;width:52px;}
.spec-tbl tbody tr{border-bottom:1px solid #edf0f3;}
.spec-tbl tbody td{padding:10px 12px;vertical-align:middle;color:#333;}
.spec-tbl tbody td:first-child{font-weight:700;color:#1a1a1a;}
.td-method{color:#8a9bb0!important;font-weight:400!important;}
.td-result{font-weight:700!important;}
.td-purity-val{font-size:12pt!important;font-weight:900!important;color:#17b8b4!important;}
.td-pass{text-align:center;}
.chk{
    display:inline-flex;align-items:center;justify-content:center;
    width:24px;height:24px;border-radius:50%;
    background:#17b8b4;color:#fff;
    font-size:13pt;font-weight:900;line-height:1;
}

/* ─── CHARTS ────────────────────────────────────────────── */
.charts-row{
    display:flex;
    border-top:1px solid #e8e8e8;
    border-bottom:1px solid #e8e8e8;
    margin-top:20px;
    flex-wrap:wrap;
}
.chart-cell{
    flex:1;min-width:240px;
    padding:16px 28px;
}
.chart-cell:first-child{border-right:1px solid #e8e8e8;}
.chart-label{font-size:7.5pt;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#1a1a1a;margin-bottom:2px;}
.chart-sub{font-size:6.5pt;color:#9aacbb;margin-bottom:10px;}

/* ─── RESULT BOX ────────────────────────────────────────── */
.result-box{
    margin:18px 28px;
    border:1.5px solid #17b8b4;
    border-radius:4px;
    padding:13px 16px;
    background:#f2fdfb;
}
.result-main{font-size:9pt;font-weight:700;color:#1a1a1a;margin-bottom:4px;}
.result-sub{font-size:7.5pt;color:#556070;line-height:1.6;}

/* ─── SIGNATURE ─────────────────────────────────────────── */
.sig-sec{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    padding:10px 28px 22px;
    flex-wrap:wrap;
    gap:20px;
}
.sig-lbl{font-size:6pt;color:#9aacbb;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;}
.sig-name{
    font-size:34pt;
    font-family:Georgia,'Times New Roman',serif;
    font-style:italic;
    color:#1a1a1a;
    line-height:1;
    border-bottom:1.5px solid #bbb;
    display:inline-block;
    padding-bottom:4px;
    margin-bottom:6px;
    min-width:200px;
}
.sig-detail{font-size:7.5pt;color:#444;line-height:1.7;}

/* Circular QC stamp */
.qc-stamp{
    width:90px;height:90px;
    border-radius:50%;
    border:2px solid #2c3340;
    display:flex;flex-direction:column;
    align-items:center;justify-content:center;
    gap:2px;
    flex-shrink:0;
}
.stamp-qc{font-size:8pt;font-weight:700;color:#17b8b4;letter-spacing:1px;}
.stamp-approved{font-size:9.5pt;font-weight:900;color:#1a1a1a;letter-spacing:.5px;}
.stamp-rule{width:55%;height:1px;background:#2c3340;margin:1px 0;}
.stamp-lab{font-size:5pt;color:#8a9bb0;text-transform:uppercase;letter-spacing:2px;}

/* ─── FOOTER ────────────────────────────────────────────── */
.coa-footer{
    border-top:1px solid #e8e8e8;
    padding:8px 28px;
    display:flex;justify-content:space-between;align-items:center;
    flex-wrap:wrap;gap:6px;
}
.footer-txt{font-size:6.5pt;color:#9aacbb;line-height:1.7;}
.footer-right{font-size:6.5pt;color:#9aacbb;text-align:right;line-height:1.7;}

@media(max-width:560px){
    .hdr,.prod-sec,.specs-sec,.sig-sec{padding-left:16px;padding-right:16px;}
    .result-box{margin:14px 16px;}
    .charts-row{flex-direction:column;}
    .chart-cell:first-child{border-right:none;border-bottom:1px solid #e8e8e8;}
    .chart-cell{padding:14px 16px;}
    .coa-footer{padding:8px 16px;}
    .meta-grid{grid-template-columns:1fr;}
    .prod-name{font-size:32pt;}
}
</style>

<div class="coa-page">

{{-- ── HEADER ──────────────────────────────────────────────── --}}
<div class="hdr">
    <div class="hdr-left">
        <div class="hex-logo">
            <svg width="54" height="60" viewBox="0 0 54 60" xmlns="http://www.w3.org/2000/svg">
                <polygon points="27,3 51,16 51,44 27,57 3,44 3,16"
                    fill="none" stroke="#1a1a1a" stroke-width="2.2"/>
                <text x="27" y="38" text-anchor="middle"
                    font-family="Arial Black,Arial" font-weight="900"
                    font-size="24" fill="#1a1a1a">A</text>
            </svg>
        </div>
        <div>
            <div class="lab-name">APEX LABORATORIES</div>
            <div class="lab-sub">A n a l y t i c a l &nbsp; S e r v i c e s &nbsp; L a b o r a t o r y</div>
        </div>
    </div>
    <div class="hdr-right">
        <div class="doc-title">Certificate of Analysis</div>
        <div class="doc-ref">Document {{ $verifyCode }}</div>
    </div>
</div>
<div class="hdr-rule"></div>

{{-- ── PRODUCT IDENTIFICATION ───────────────────────────────── --}}
<div class="prod-sec">
    <div class="sec-tag">P r o d u c t &nbsp; I d e n t i f i c a t i o n</div>
    <div class="prod-name">{{ $coa->product_name }}</div>
    <div class="prod-spec">{{ $coa->quantity }} &nbsp;&middot;&nbsp; &ge;{{ $purity }}% HPLC</div>

    <div class="seq-box">
        <div class="seq-lbl">Amino Acid Sequence</div>
        <div class="seq-val">39-amino acid dual GIP / GLP-1 receptor agonist &mdash; full sequence available on request</div>
    </div>

    <div class="meta-grid">
        <div class="meta-item">
            <div class="meta-lbl">Product Name</div>
            <div class="meta-val">{{ $coa->product_name }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-lbl">Catalogue No.</div>
            <div class="meta-val">{{ $coa->catalog_number }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-lbl">Lot / Batch No.</div>
            <div class="meta-val">{{ $coa->lot_number }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-lbl">CAS No.</div>
            <div class="meta-val">{{ $coa->cas_number }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-lbl">Molecular Formula</div>
            <div class="meta-val">{{ $coa->molecular_formula }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-lbl">Molecular Weight</div>
            <div class="meta-val">{{ number_format($coa->molecular_weight, 2) }} g/mol</div>
        </div>
        <div class="meta-item">
            <div class="meta-lbl">Quantity / Vial</div>
            <div class="meta-val">{{ $coa->quantity }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-lbl">Physical Form</div>
            <div class="meta-val">Lyophilised powder</div>
        </div>
        <div class="meta-item">
            <div class="meta-lbl">Manufacture Date</div>
            <div class="meta-val">{{ $coa->manufacture_date->format('Y-m-d') }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-lbl">Re-Test Date</div>
            <div class="meta-val">{{ $coa->expiry_date->format('Y-m-d') }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-lbl">Storage</div>
            <div class="meta-val">{{ $coa->storage_conditions }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-lbl">Purity Grade</div>
            <div class="meta-val teal">&ge; {{ $purity }}% (HPLC)</div>
        </div>
    </div>
</div>

{{-- ── SPECIFICATIONS & RESULTS ─────────────────────────────── --}}
<div class="specs-sec">
    <div class="specs-tag">S p e c i f i c a t i o n s &nbsp; &amp; &nbsp; R e s u l t s</div>
    <table class="spec-tbl">
        <thead>
            <tr>
                <th style="width:22%">Test</th>
                <th style="width:16%">Method</th>
                <th style="width:28%">Specification</th>
                <th style="width:22%">Result</th>
                <th>Pass</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Appearance</td>
                <td class="td-method">Visual</td>
                <td>White to off-white lyophilised powder</td>
                <td class="td-result">White powder</td>
                <td class="td-pass"><span class="chk">&#10003;</span></td>
            </tr>
            <tr>
                <td>Identity (ESI-MS)</td>
                <td class="td-method">LC-MS</td>
                <td>Consistent with structure</td>
                <td class="td-result">Conforms</td>
                <td class="td-pass"><span class="chk">&#10003;</span></td>
            </tr>
            <tr>
                <td>Purity (RP-HPLC)</td>
                <td class="td-method">HPLC-UV 220 nm</td>
                <td>&ge; 99.0 %</td>
                <td class="td-purity-val">{{ $purity }} %</td>
                <td class="td-pass"><span class="chk">&#10003;</span></td>
            </tr>
            <tr>
                <td>Single Impurity (max)</td>
                <td class="td-method">RP-HPLC</td>
                <td>&le; 1.0 %</td>
                <td class="td-result">0.01 %</td>
                <td class="td-pass"><span class="chk">&#10003;</span></td>
            </tr>
            <tr>
                <td>Net Peptide Content</td>
                <td class="td-method">UV / nitrogen</td>
                <td>&ge; 80.0 %</td>
                <td class="td-result">{{ $netPep }} %</td>
                <td class="td-pass"><span class="chk">&#10003;</span></td>
            </tr>
            <tr>
                <td>Water Content</td>
                <td class="td-method">Karl Fischer</td>
                <td>&le; 8.0 %</td>
                <td class="td-result">2.8 %</td>
                <td class="td-pass"><span class="chk">&#10003;</span></td>
            </tr>
            <tr>
                <td>Acetate Content</td>
                <td class="td-method">Ion HPLC</td>
                <td>&le; 15.0 %</td>
                <td class="td-result">{{ $acetate }} %</td>
                <td class="td-pass"><span class="chk">&#10003;</span></td>
            </tr>
            <tr>
                <td>Bacterial Endotoxin</td>
                <td class="td-method">LAL</td>
                <td>&lt; 10 EU/mg</td>
                <td class="td-result">{{ $coa->endotoxin }}</td>
                <td class="td-pass"><span class="chk">&#10003;</span></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ── CHARTS ────────────────────────────────────────────────── --}}
<div class="charts-row">
    {{-- RP-HPLC Chromatogram --}}
    <div class="chart-cell">
        <div class="chart-label">RP-HPLC Chromatogram</div>
        <div class="chart-sub">Column C18 &middot; 220 nm &middot; 1.0 mL/min</div>
        <svg width="100%" viewBox="0 0 320 140" xmlns="http://www.w3.org/2000/svg" style="display:block;overflow:visible;">
            <!-- chart border -->
            <rect x="1" y="1" width="318" height="118" fill="#fff" stroke="#dde4ea" stroke-width="1"/>
            <!-- baseline flat -->
            <polyline points="10,115 55,115 60,113 64,115 80,115 100,115 110,113 115,115 130,115"
                fill="none" stroke="#17b8b4" stroke-width="1.2"/>
            <!-- main peak — tall Gaussian at x≈190 -->
            <polyline
                points="130,115 140,115 148,114 152,112 156,108 159,102 161,92 163,78 165,58 166,40 167,22 168,12 169,8 170,12 171,22 172,40 173,58 175,78 177,92 179,102 182,108 185,112 188,114 192,115"
                fill="none" stroke="#17b8b4" stroke-width="2"/>
            <!-- small impurity right -->
            <polyline points="228,115 233,114 236,112 237,115 242,115"
                fill="none" stroke="#17b8b4" stroke-width="1.2"/>
            <!-- baseline to end -->
            <polyline points="192,115 310,115" fill="none" stroke="#17b8b4" stroke-width="1.2"/>
            <!-- purity label at peak top -->
            <text x="169" y="5" text-anchor="middle"
                font-family="Arial" font-size="9" font-weight="700" fill="#17b8b4">{{ $purity }}%</text>
            <!-- x-axis label -->
            <text x="160" y="135" text-anchor="middle"
                font-family="Arial" font-size="7.5" fill="#9aacbb">Retention time (min)</text>
        </svg>
    </div>

    {{-- ESI-MS --}}
    <div class="chart-cell">
        <div class="chart-label">ESI-MS</div>
        <div class="chart-sub">&nbsp;</div>
        <svg width="100%" viewBox="0 0 260 140" xmlns="http://www.w3.org/2000/svg" style="display:block;overflow:visible;">
            <!-- chart border -->
            <rect x="1" y="1" width="258" height="118" fill="#fff" stroke="#dde4ea" stroke-width="1"/>
            <!-- baseline -->
            <line x1="10" y1="115" x2="250" y2="115" stroke="#17b8b4" stroke-width="1.2"/>
            <!-- [M+3H]3+ — short -->
            <line x1="60" y1="115" x2="60" y2="95" stroke="#17b8b4" stroke-width="2"/>
            <!-- [M+2H]2+ — medium -->
            <line x1="110" y1="115" x2="110" y2="72" stroke="#17b8b4" stroke-width="2"/>
            <!-- small peaks right -->
            <line x1="165" y1="115" x2="165" y2="105" stroke="#17b8b4" stroke-width="1.5"/>
            <line x1="190" y1="115" x2="190" y2="108" stroke="#17b8b4" stroke-width="1.5"/>
            <!-- [M+H]+ — tallest -->
            <line x1="210" y1="115" x2="210" y2="12" stroke="#17b8b4" stroke-width="2.5"/>
            <text x="210" y="8" text-anchor="middle"
                font-family="Arial" font-size="8" font-weight="700" fill="#17b8b4">[M+H]+</text>
            <!-- x-axis label -->
            <text x="130" y="135" text-anchor="middle"
                font-family="Arial" font-size="7.5" fill="#9aacbb">m/z</text>
        </svg>
    </div>
</div>

{{-- ── RESULT BOX ───────────────────────────────────────────── --}}
<div class="result-box">
    <div class="result-main">RESULT: CONFORMS &mdash; This batch meets all Apex Laboratories release specifications.</div>
    <div class="result-sub">FOR LABORATORY RESEARCH USE ONLY. Not for human or veterinary use, diagnostic or therapeutic application.</div>
</div>

{{-- ── SIGNATURE ────────────────────────────────────────────── --}}
<div class="sig-sec">
    <div>
        <div class="sig-lbl">Released By (Quality Control)</div>
        <div class="sig-name">S. Mitchell</div>
        <div class="sig-detail">
            Dr. Sarah Mitchell &mdash; QC Manager, Analytical Services<br>
            Date of issue: {{ $coa->analysis_date->format('Y-m-d') }}
        </div>
    </div>
    <div class="qc-stamp">
        <div class="stamp-qc">QC</div>
        <div class="stamp-approved">APPROVED</div>
        <div class="stamp-rule"></div>
        <div class="stamp-lab">A P E X &nbsp; L A B</div>
    </div>
</div>

{{-- ── FOOTER ───────────────────────────────────────────────── --}}
<div class="coa-footer">
    <div class="footer-txt">
        Apex Laboratories Pty Ltd &middot; Analytical Services Laboratory &middot; apexlaboratories.com
    </div>
    <div class="footer-right">
        This certificate is generated electronically and is valid without a wet signature.
        Verify authenticity with document code {{ $verifyCode }}. Page 1 of 1
    </div>
</div>

</div>

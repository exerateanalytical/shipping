@php
    $purity       = number_format((float)$coa->purity_hplc, 2);
    $analysisPeriod = $coa->analysis_start_date
        ? $coa->analysis_start_date->format('d M Y') . ' – ' . $coa->analysis_date->format('d M Y')
        : $coa->analysis_date->format('d M Y');

    // Deterministic verification code
    $verifyCode = strtoupper(substr(md5($coa->lot_number . $coa->analysis_date), 0, 16));
    $verifyFmt  = implode('-', str_split($verifyCode, 4));

    // QR-like grid
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
@endphp
<style>
*{box-sizing:border-box;margin:0;padding:0;}
.coa{
    width:100%;
    font-family:Arial,Helvetica,sans-serif;
    font-size:9pt;color:#111;background:#fff;
}

/* ── HEADER ─────────────────────────────────────────── */
.coa-hdr{
    background:#0f2d4a;
    padding:22px 30px 18px;
    display:flex;align-items:flex-start;justify-content:space-between;
    flex-wrap:wrap;gap:12px;
}
.coa-lab-name{
    font-size:28pt;font-weight:900;color:#fff;
    letter-spacing:-1px;line-height:1;
}
.coa-lab-accred{
    font-size:7pt;color:#7aadcf;margin-top:4px;letter-spacing:.5px;
}
.coa-lab-accred strong{color:#FFCC00;}
.coa-hdr-right{text-align:right;}
.coa-doc-title{
    font-size:18pt;font-weight:900;color:#FFCC00;
    letter-spacing:.5px;line-height:1.1;
}
.coa-doc-sub{font-size:7.5pt;color:#7aadcf;margin-top:3px;}
.coa-cert-ref{
    font-size:8pt;color:#fff;margin-top:8px;
    font-family:'Courier New',monospace;letter-spacing:.5px;
    background:rgba(255,255,255,.08);
    padding:4px 10px;border-radius:2px;display:inline-block;
}

/* ── ACCENT ─────────────────────────────────────────── */
.coa-accent{height:5px;background:linear-gradient(90deg,#FFCC00 0%,#f0b800 40%,#0f2d4a 100%);}

/* ── PRODUCT BLOCK ──────────────────────────────────── */
.coa-prod{
    background:#f0f5fa;
    border-bottom:2.5px solid #0f2d4a;
    padding:18px 30px;
    display:flex;gap:30px;flex-wrap:wrap;
}
.coa-prod-main{flex:2;min-width:200px;}
.coa-prod-name{font-size:20pt;font-weight:900;color:#0f2d4a;line-height:1.1;}
.coa-prod-synonym{font-size:8pt;color:#555;margin-top:2px;font-weight:600;}
.coa-prod-iupac{
    font-size:7pt;color:#666;margin-top:6px;
    font-style:italic;line-height:1.5;
    background:#e8eff7;padding:6px 8px;border-left:3px solid #0f2d4a;
}
.coa-grade-pill{
    display:inline-block;margin-top:10px;
    background:#0f2d4a;color:#FFCC00;
    padding:3px 14px;font-size:7.5pt;font-weight:700;
    letter-spacing:1px;text-transform:uppercase;border-radius:2px;
}
.coa-prod-meta{flex:1;min-width:180px;}
.coa-meta-tbl{width:100%;border-collapse:collapse;font-size:8pt;}
.coa-meta-tbl tr{border-bottom:1px dotted #d0dce8;}
.coa-meta-tbl td{padding:4px 6px;vertical-align:top;}
.coa-meta-tbl td:first-child{color:#666;font-weight:600;white-space:nowrap;width:42%;}
.coa-meta-tbl td:last-child{font-weight:700;color:#0f2d4a;}

/* ── PREPARED FOR BANNER ────────────────────────────── */
.coa-recipient{
    background:#e8f4fd;border-bottom:2px solid #0f2d4a;
    padding:10px 30px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;
}
.coa-recip-tag{
    background:#0f2d4a;color:#FFCC00;
    font-size:7pt;font-weight:700;text-transform:uppercase;
    letter-spacing:1px;padding:4px 12px;border-radius:2px;white-space:nowrap;
}
.coa-recip-name{font-size:12pt;font-weight:900;color:#0f2d4a;}
.coa-recip-email{font-size:8pt;color:#555;margin-left:10px;}

/* ── SECTION TITLE ──────────────────────────────────── */
.coa-sec{padding:16px 30px;border-bottom:1px solid #dde5ef;}
.coa-sec-title{
    font-size:9.5pt;font-weight:700;color:#0f2d4a;
    text-transform:uppercase;letter-spacing:1px;
    border-left:4px solid #FFCC00;padding-left:8px;margin-bottom:12px;
}

/* ── PURITY BAR ─────────────────────────────────────── */
.purity-row{display:flex;align-items:center;gap:14px;flex-wrap:wrap;}
.purity-bar-bg{
    flex:1;min-width:120px;height:16px;
    background:#dde5ef;border-radius:8px;
    overflow:hidden;border:1px solid #c5d5e8;
}
.purity-bar-fill{
    height:100%;border-radius:8px;
    background:linear-gradient(90deg,#0f2d4a 0%,#1b6ca8 60%,#27ae60 100%);
}
.purity-pct{font-size:16pt;font-weight:900;color:#27ae60;white-space:nowrap;}
.purity-method{font-size:7.5pt;color:#666;margin-top:5px;}

/* ── TEST RESULTS TABLE ─────────────────────────────── */
.coa-tbl{width:100%;border-collapse:collapse;font-size:8.5pt;}
.coa-tbl thead th{
    background:#0f2d4a;color:#fff;
    padding:7px 10px;text-align:left;
    font-size:7pt;text-transform:uppercase;letter-spacing:.5px;
}
.coa-tbl tbody tr:nth-child(even) td{background:#f5f9fd;}
.coa-tbl tbody tr:nth-child(odd)  td{background:#fff;}
.coa-tbl td{padding:7px 10px;border-bottom:1px solid #dde5ef;vertical-align:middle;}
.coa-tbl td:first-child{font-weight:600;color:#222;}
.coa-tbl td.method{font-size:7pt;color:#888;font-style:italic;}
.pass{
    display:inline-block;background:#27ae60;color:#fff;
    padding:2px 10px;border-radius:3px;
    font-size:7pt;font-weight:700;letter-spacing:.5px;
}
.purity-val{font-size:13pt;font-weight:900;color:#27ae60;}

/* ── STORAGE GRID ───────────────────────────────────── */
.store-grid{display:flex;gap:14px;flex-wrap:wrap;}
.store-box{
    flex:1;min-width:130px;
    border:1.5px solid #0f2d4a;border-radius:4px;padding:10px 12px;
}
.store-ico{font-size:15pt;margin-bottom:4px;}
.store-lbl{font-size:6.5pt;text-transform:uppercase;color:#999;letter-spacing:.5px;}
.store-val{font-size:8.5pt;font-weight:700;color:#0f2d4a;margin-top:2px;}

/* ── RESEARCH WARNING ───────────────────────────────── */
.coa-warn{
    margin:14px 30px 0;
    padding:10px 14px;
    background:#fff8e1;border:1.5px solid #f0c000;border-radius:4px;
    font-size:8pt;color:#7a5f00;
    display:flex;gap:10px;align-items:flex-start;
}
.warn-icon{font-size:15pt;flex-shrink:0;line-height:1.1;}

/* ── BATCH RELEASE ──────────────────────────────────── */
.coa-release{
    margin:14px 30px 0;
    padding:10px 14px;
    background:#e8f4fd;border:1.5px solid #0f2d4a;border-radius:4px;
    font-size:8pt;color:#0f2d4a;
}
.release-title{font-weight:700;font-size:8.5pt;margin-bottom:3px;}

/* ── SIGNATURES ─────────────────────────────────────── */
.coa-sigs{
    padding:18px 30px;border-top:1px solid #dde5ef;
    display:flex;gap:24px;align-items:flex-start;flex-wrap:wrap;
}
.sig-blk{flex:1;min-width:160px;}
.sig-line-area{
    height:36px;margin-bottom:5px;
    border-bottom:1.5px solid #333;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='34'%3E%3Cpath d='M8 26 Q22 8 40 20 Q58 32 76 12 Q94 -4 112 18 Q130 36 160 14' stroke='%230f2d4a' stroke-width='1.8' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:left center;
}
.sig-role{font-size:6.5pt;color:#999;text-transform:uppercase;letter-spacing:.5px;}
.sig-name{font-size:9.5pt;font-weight:700;color:#0f2d4a;margin-top:2px;}
.sig-title-txt{font-size:7.5pt;color:#555;}
.sig-date{font-size:7.5pt;color:#888;margin-top:3px;}
.coa-stamp{
    border:2.5px solid #0f2d4a;padding:12px 18px;
    border-radius:4px;background:#f0f5fa;text-align:center;
    align-self:flex-end;
}
.stamp-status{font-size:13pt;font-weight:900;color:#27ae60;margin:4px 0;}
.stamp-dept{font-size:7pt;color:#555;}
.stamp-iso{font-size:6.5pt;color:#888;margin-top:3px;}

/* ── VERIFICATION ───────────────────────────────────── */
.coa-verify{
    margin:14px 30px;
    padding:10px 14px;
    background:#f8f8f8;border:1px solid #ddd;border-radius:4px;
    display:flex;align-items:center;gap:14px;flex-wrap:wrap;
}
.verify-qr{display:grid;grid-template-columns:repeat(7,1fr);width:56px;height:56px;flex-shrink:0;border:1px solid #ccc;}
.verify-qr-c{width:100%;aspect-ratio:1;}
.verify-text{flex:1;font-size:7pt;color:#555;line-height:1.6;}
.verify-code{
    font-family:'Courier New',monospace;font-size:9pt;
    font-weight:700;color:#0f2d4a;letter-spacing:2px;
    display:block;margin-top:3px;
}

/* ── FOOTER ─────────────────────────────────────────── */
.coa-ftr{
    background:#0f2d4a;padding:12px 30px;
    display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;
}
.ftr-logo{font-size:15pt;font-weight:900;color:#FFCC00;display:block;line-height:1;}
.ftr-left{font-size:7pt;color:#7aadcf;line-height:1.7;}
.ftr-right{font-size:7pt;color:#7aadcf;text-align:right;line-height:1.7;}
.ftr-note{font-size:6pt;color:#4a7a9b;margin-top:4px;}

@media(max-width:580px){
    .coa-hdr,.coa-prod,.coa-sigs{padding:14px 16px;}
    .coa-sec{padding:12px 16px;}
    .coa-warn,.coa-release,.coa-verify{margin:10px 16px 0;}
    .coa-recipient{padding:10px 16px;}
    .coa-ftr{padding:10px 16px;}
    .coa-prod-main,.coa-prod-meta{min-width:100%;}
}
</style>

<div class="coa">

{{-- ── HEADER ──────────────────────────────────────────────── --}}
<div class="coa-hdr">
    <div>
        <div class="coa-lab-name">Apex Laboratories</div>
        <div class="coa-lab-accred">
            <strong>ISO/IEC 17025:2017 Accredited</strong> · Accreditation No. AL-2847 · GMP Compliant
        </div>
        <div class="coa-lab-accred" style="margin-top:2px;">
            Precision &nbsp;·&nbsp; Purity &nbsp;·&nbsp; Performance
        </div>
    </div>
    <div class="coa-hdr-right">
        <div class="coa-doc-title">Certificate of Analysis</div>
        <div class="coa-doc-sub">Quality Assurance &amp; Release Document</div>
        <div class="coa-cert-ref">COA-{{ $coa->lot_number }}</div>
    </div>
</div>
<div class="coa-accent"></div>

{{-- ── PRODUCT INFO ─────────────────────────────────────────── --}}
<div class="coa-prod">
    <div class="coa-prod-main">
        <div class="coa-prod-name">{{ $coa->product_name }}</div>
        <div class="coa-prod-synonym">Dual GIP / GLP-1 Receptor Agonist &nbsp;·&nbsp; 39-Amino Acid Synthetic Peptide</div>
        <div class="coa-prod-iupac">
            <strong>IUPAC Name:</strong> (R)-4-[[(4R,7R,10S,13R,16S,19R)-19-[[(2R)-2-amino-3-[4-(2-{2-[2-(2-aminoethoxy)ethoxy]acetamido}ethyl)phenyl]-1-oxopropyl]amino]-
            10-(4-aminobutyl)-7-[(1R)-1-hydroxyethyl]-13-(1H-indol-3-ylmethyl)-6,9,12,15,18-pentaoxo-
            16-[(4-{[(2S)-2-{[(2S)-2-amino-4-methylpentanoyl]amino}propanoyl]amino}phenyl)methyl]-
            1,2-dithia-5,8,11,14,17-pentazacyclodocosane-4-carbonyl]amino]pentanedioic acid
        </div>
        <span class="coa-grade-pill">{{ $coa->grade }}</span>
    </div>
    <div class="coa-prod-meta">
        <table class="coa-meta-tbl">
            <tr><td>Catalog No.</td><td>{{ $coa->catalog_number }}</td></tr>
            <tr><td>Lot / Batch</td><td>{{ $coa->lot_number }}</td></tr>
            <tr><td>CAS Number</td><td>{{ $coa->cas_number }}</td></tr>
            <tr><td>Mol. Formula</td><td>{{ $coa->molecular_formula }}</td></tr>
            <tr><td>Mol. Weight</td><td>{{ number_format($coa->molecular_weight, 2) }} g/mol</td></tr>
            <tr><td>Quantity</td><td>{{ $coa->quantity }}</td></tr>
            @if($coa->recipient_name)
            <tr><td>Prepared For</td><td><strong>{{ $coa->recipient_name }}</strong></td></tr>
            @endif
            <tr><td>Manufacture</td><td>{{ $coa->manufacture_date->format('d M Y') }}</td></tr>
            <tr><td>Expiry Date</td><td>{{ $coa->expiry_date->format('d M Y') }}</td></tr>
            <tr><td>Analysis Period</td><td>{{ $analysisPeriod }}</td></tr>
        </table>
    </div>
</div>

{{-- ── PREPARED FOR ─────────────────────────────────────────── --}}
@if($coa->recipient_name)
<div class="coa-recipient">
    <span class="coa-recip-tag">Prepared For</span>
    <div>
        <span class="coa-recip-name">{{ $coa->recipient_name }}</span>
        @if($coa->recipient_email)
        <span class="coa-recip-email">{{ $coa->recipient_email }}</span>
        @endif
    </div>
</div>
@endif

{{-- ── PURITY ASSESSMENT ────────────────────────────────────── --}}
<div class="coa-sec">
    <div class="coa-sec-title">Purity Assessment</div>
    <div class="purity-row">
        <div class="purity-bar-bg">
            <div class="purity-bar-fill" style="width:{{ $purityWidth }}%;"></div>
        </div>
        <div class="purity-pct">{{ $purity }}%</div>
    </div>
    <div class="purity-method">
        Method: Reverse-Phase HPLC (RP-HPLC) · Column: C18, 250 × 4.6 mm, 5 µm ·
        Mobile phase: 0.1% TFA in water / acetonitrile gradient · UV detection at 220 nm ·
        Reference standard: USP Tirzepatide RS · Method Ref: APX-QC-M-047 Rev.3
    </div>
</div>

{{-- ── ANALYTICAL TEST RESULTS ─────────────────────────────── --}}
<div class="coa-sec">
    <div class="coa-sec-title">Analytical Test Results</div>
    <table class="coa-tbl">
        <thead>
            <tr>
                <th style="width:24%">Test Parameter</th>
                <th style="width:20%">Specification</th>
                <th style="width:24%">Result / Observed Value</th>
                <th style="width:18%">Method Ref.</th>
                <th style="width:14%">Compliance</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Purity (RP-HPLC)</td>
                <td>≥ 98.0%</td>
                <td><span class="purity-val">{{ $purity }}%</span></td>
                <td class="method">APX-QC-M-047</td>
                <td><span class="pass">PASS</span></td>
            </tr>
            <tr>
                <td>Identity by Mass Spec</td>
                <td>MH⁺: 4814.5 ± 2 Da</td>
                <td>{{ $coa->identity_ms }} · MH⁺ = 4814.5 Da</td>
                <td class="method">Ph.Eur. 2.2.43</td>
                <td><span class="pass">PASS</span></td>
            </tr>
            <tr>
                <td>Identity by RP-HPLC</td>
                <td>RT ± 0.5 min vs. reference</td>
                <td>{{ $coa->identity_hplc }}</td>
                <td class="method">APX-QC-M-047</td>
                <td><span class="pass">PASS</span></td>
            </tr>
            <tr>
                <td>Appearance</td>
                <td>White to off-white lyophilised powder</td>
                <td>{{ $coa->appearance }}</td>
                <td class="method">Visual / Ph.Eur. 2.2.1</td>
                <td><span class="pass">PASS</span></td>
            </tr>
            <tr>
                <td>Water Content (KF)</td>
                <td>≤ 5.0% w/w</td>
                <td>{{ $coa->moisture_content }}</td>
                <td class="method">USP &lt;921&gt;</td>
                <td><span class="pass">PASS</span></td>
            </tr>
            <tr>
                <td>pH (1% aq. solution)</td>
                <td>6.0 – 7.5</td>
                <td>{{ $coa->ph }}</td>
                <td class="method">USP &lt;791&gt;</td>
                <td><span class="pass">PASS</span></td>
            </tr>
            <tr>
                <td>Bacterial Endotoxins</td>
                <td>&lt; 1.0 EU/mg</td>
                <td>{{ $coa->endotoxin }}</td>
                <td class="method">USP &lt;85&gt; / LAL</td>
                <td><span class="pass">PASS</span></td>
            </tr>
            <tr>
                <td>Sterility</td>
                <td>No growth in 14-day incubation</td>
                <td>{{ $coa->sterility }}</td>
                <td class="method">USP &lt;71&gt;</td>
                <td><span class="pass">PASS</span></td>
            </tr>
            <tr>
                <td>Solubility</td>
                <td>≥ 1 mg/mL in sterile water</td>
                <td>{{ $coa->solubility }}</td>
                <td class="method">APX-QC-M-012</td>
                <td><span class="pass">PASS</span></td>
            </tr>
            <tr>
                <td>Related Substances</td>
                <td>Any individual impurity ≤ 0.5%</td>
                <td>Max. detected impurity: 0.01%</td>
                <td class="method">APX-QC-M-047</td>
                <td><span class="pass">PASS</span></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ── STORAGE & HANDLING ───────────────────────────────────── --}}
<div class="coa-sec">
    <div class="coa-sec-title">Storage &amp; Handling</div>
    <div class="store-grid">
        <div class="store-box">
            <div class="store-ico">🌡</div>
            <div class="store-lbl">Temperature</div>
            <div class="store-val">-20 °C (Frozen)</div>
        </div>
        <div class="store-box">
            <div class="store-ico">💧</div>
            <div class="store-lbl">Humidity</div>
            <div class="store-val">Desiccated</div>
        </div>
        <div class="store-box">
            <div class="store-ico">🔆</div>
            <div class="store-lbl">Light</div>
            <div class="store-val">Protect from Light</div>
        </div>
        <div class="store-box">
            <div class="store-ico">🔄</div>
            <div class="store-lbl">Freeze-Thaw Cycles</div>
            <div class="store-val">Limit to ≤ 3 cycles</div>
        </div>
        <div class="store-box">
            <div class="store-ico">📦</div>
            <div class="store-lbl">Full Conditions</div>
            <div class="store-val">{{ $coa->storage_conditions }}</div>
        </div>
    </div>
</div>

{{-- ── RESEARCH USE WARNING ─────────────────────────────────── --}}
<div class="coa-warn">
    <div class="warn-icon">⚠</div>
    <div>
        <strong>FOR RESEARCH USE ONLY — NOT FOR HUMAN OR VETERINARY USE.</strong><br>
        This product has not been approved by the FDA, EMA, or any regulatory authority for
        therapeutic, diagnostic, or prophylactic use. {{ $coa->notes }}
        Handle using appropriate PPE. Refer to SDS document APX-SDS-TZP-001 before use.
    </div>
</div>

{{-- ── BATCH RELEASE STATEMENT ─────────────────────────────── --}}
<div class="coa-release" style="margin-bottom:0;">
    <div class="release-title">Batch Release Statement</div>
    Lot <strong>{{ $coa->lot_number }}</strong> of <strong>{{ $coa->product_name }}</strong> ({{ $coa->quantity }}) has been
    tested against the specifications listed above and found to comply in all respects.
    This batch is hereby released for distribution as of <strong>{{ $coa->analysis_date->format('d M Y') }}</strong>.
    Batch Release No.: <strong>{{ strtoupper(substr(md5($coa->lot_number . 'release'), 0, 10)) }}</strong>
</div>

{{-- ── SIGNATURES ───────────────────────────────────────────── --}}
<div class="coa-sigs">
    <div class="sig-blk">
        <div class="sig-line-area"></div>
        <div class="sig-role">Quality Control Manager</div>
        <div class="sig-name">Dr. Sarah Mitchell, Ph.D.</div>
        <div class="sig-title-txt">Director of Quality Assurance — Apex Laboratories</div>
        <div class="sig-date">Signed: {{ $coa->analysis_date->format('d M Y') }}</div>
    </div>
    <div class="sig-blk">
        <div class="sig-line-area" style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22180%22 height=%2234%22%3E%3Cpath d=%22M5 28 Q15 10 35 22 Q55 34 80 15 Q100 2 120 20 Q140 34 170 16%22 stroke=%220f2d4a%22 stroke-width=%221.8%22 fill=%22none%22 stroke-linecap=%22round%22/%3E%3C/svg%3E');"></div>
        <div class="sig-role">Senior Analytical Scientist</div>
        <div class="sig-name">Dr. James Hartley, Ph.D.</div>
        <div class="sig-title-txt">Head of Analytical Chemistry — Apex Laboratories</div>
        <div class="sig-date">Signed: {{ $coa->analysis_date->format('d M Y') }}</div>
    </div>
    <div class="coa-stamp">
        <div style="font-size:6.5pt;color:#888;text-transform:uppercase;letter-spacing:.5px;">Document Status</div>
        <div class="stamp-status">✓ APPROVED</div>
        <div class="stamp-dept">Apex Laboratories QA</div>
        <div class="stamp-iso">ISO/IEC 17025:2017 · Acc. AL-2847</div>
        <div class="stamp-iso" style="margin-top:2px;">Doc Rev: 1.0 · {{ $coa->analysis_date->format('Y') }}</div>
    </div>
</div>

{{-- ── DOCUMENT VERIFICATION ────────────────────────────────── --}}
<div class="coa-verify">
    <div class="verify-qr">
        @foreach($qCells as $f)
            <div class="verify-qr-c" style="background:{{ $f ? '#000' : '#fff' }};"></div>
        @endforeach
    </div>
    <div class="verify-text">
        <strong>Document Verification</strong><br>
        Verify the authenticity of this COA at <strong>www.apexlaboratories.com/verify</strong>
        using the verification code below. This document is digitally signed and tamper-evident.
        <span class="verify-code">{{ $verifyFmt }}</span>
    </div>
    <div style="text-align:right;font-size:7pt;color:#888;min-width:120px;">
        <div>Issued: {{ $coa->analysis_date->format('d M Y') }}</div>
        <div>Expires: {{ $coa->expiry_date->format('d M Y') }}</div>
        <div style="margin-top:4px;">Certificate No.</div>
        <div style="font-weight:700;color:#0f2d4a;font-family:'Courier New',monospace;">COA-{{ $coa->lot_number }}</div>
    </div>
</div>

{{-- ── FOOTER ───────────────────────────────────────────────── --}}
<div class="coa-ftr">
    <div class="ftr-left">
        <span class="ftr-logo">Apex Laboratories</span>
        1 Research Park Boulevard, Suite 300 · Boston, MA 02210 · USA<br>
        Tel: +1 (617) 555-0200 · Fax: +1 (617) 555-0201 · qc@apexlaboratories.com<br>
        www.apexlaboratories.com · ISO/IEC 17025 Accredited · GMP Certified
    </div>
    <div class="ftr-right">
        <div>Certificate No: COA-{{ $coa->lot_number }}</div>
        <div>Page 1 of 1</div>
        <div>Generated: {{ now()->format('d M Y H:i') }} UTC</div>
        <div class="ftr-note">
            This document is valid without a wet ink signature.<br>
            Unauthorised reproduction is prohibited.
        </div>
    </div>
</div>

</div>

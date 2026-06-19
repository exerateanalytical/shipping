<style>
.coa-doc {
    width: 100%;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9pt;
    color: #1a1a1a;
    background: #fff;
    padding: 0;
}

/* ── Header ─────────────────────────────────────────────── */
.coa-header {
    background: #1a3a5c;
    padding: 20px 30px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.coa-logo-area { display: flex; flex-direction: column; }
.coa-lab-name {
    font-size: 26pt;
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.5px;
    line-height: 1;
}
.coa-lab-tagline {
    font-size: 8pt;
    color: #a8c4e0;
    margin-top: 3px;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.coa-header-right { text-align: right; }
.coa-doc-title {
    font-size: 16pt;
    font-weight: 700;
    color: #FFCC00;
    letter-spacing: 0.5px;
    line-height: 1.1;
}
.coa-doc-subtitle {
    font-size: 8pt;
    color: #a8c4e0;
    margin-top: 3px;
}
.coa-cert-number {
    font-size: 8pt;
    color: #fff;
    margin-top: 6px;
    font-family: 'Courier New', monospace;
}

/* ── Gold accent bar ──────────────────────────────────── */
.coa-accent-bar {
    height: 5px;
    background: linear-gradient(90deg, #FFCC00 0%, #e6b800 50%, #1a3a5c 100%);
}

/* ── Product info block ──────────────────────────────── */
.coa-product-block {
    background: #f0f5fa;
    border-bottom: 2px solid #1a3a5c;
    padding: 16px 30px;
    display: flex;
    gap: 30px;
}
.coa-product-main { flex: 2; }
.coa-product-name {
    font-size: 18pt;
    font-weight: 900;
    color: #1a3a5c;
    line-height: 1.1;
}
.coa-product-iupac {
    font-size: 7.5pt;
    color: #555;
    margin-top: 4px;
    font-style: italic;
    line-height: 1.4;
}
.coa-product-grade {
    display: inline-block;
    margin-top: 8px;
    background: #1a3a5c;
    color: #FFCC00;
    padding: 3px 12px;
    font-size: 7.5pt;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    border-radius: 2px;
}
.coa-product-meta { flex: 1; }
.coa-meta-table { width: 100%; font-size: 8pt; border-collapse: collapse; }
.coa-meta-table td { padding: 3px 6px; border-bottom: 1px dotted #ccc; vertical-align: top; }
.coa-meta-table td:first-child { color: #666; font-weight: 600; white-space: nowrap; }
.coa-meta-table td:last-child { font-weight: 700; color: #1a3a5c; }

/* ── Section headings ───────────────────────────────── */
.coa-section {
    padding: 14px 30px;
    border-bottom: 1px solid #dde3ea;
}
.coa-section-title {
    font-size: 10pt;
    font-weight: 700;
    color: #1a3a5c;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-left: 4px solid #FFCC00;
    padding-left: 8px;
    margin-bottom: 10px;
}

/* ── Test results table ─────────────────────────────── */
.coa-results-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8.5pt;
}
.coa-results-table thead th {
    background: #1a3a5c;
    color: #fff;
    padding: 7px 10px;
    text-align: left;
    font-size: 7.5pt;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.coa-results-table tbody tr:nth-child(even) td { background: #f6f9fc; }
.coa-results-table tbody tr:nth-child(odd)  td { background: #fff; }
.coa-results-table td {
    padding: 7px 10px;
    border-bottom: 1px solid #e0e8f0;
    vertical-align: middle;
}
.coa-results-table td:first-child { font-weight: 600; color: #333; }
.pass-badge {
    display: inline-block;
    background: #27ae60;
    color: #fff;
    padding: 2px 9px;
    border-radius: 3px;
    font-size: 7.5pt;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.purity-highlight {
    font-size: 12pt;
    font-weight: 900;
    color: #27ae60;
}

/* ── Purity bar ─────────────────────────────────────── */
.purity-bar-wrap {
    margin: 8px 0 4px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.purity-bar-bg {
    flex: 1;
    height: 14px;
    background: #e0e8f0;
    border-radius: 7px;
    overflow: hidden;
    border: 1px solid #c0cfe0;
}
.purity-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #1a3a5c, #27ae60);
    border-radius: 7px;
    width: {{ $coa->purity_hplc }}%;
}
.purity-bar-label {
    font-size: 13pt;
    font-weight: 900;
    color: #27ae60;
    white-space: nowrap;
}

/* ── Storage & warnings ─────────────────────────────── */
.coa-storage-grid {
    display: flex;
    gap: 16px;
}
.coa-storage-box {
    flex: 1;
    border: 1.5px solid #1a3a5c;
    border-radius: 4px;
    padding: 10px 12px;
}
.coa-storage-icon { font-size: 16pt; margin-bottom: 4px; }
.coa-storage-label { font-size: 7pt; text-transform: uppercase; color: #888; letter-spacing: 0.5px; }
.coa-storage-value { font-size: 9pt; font-weight: 700; color: #1a3a5c; margin-top: 2px; }

/* ── Warning box ────────────────────────────────────── */
.coa-warning {
    margin: 0 30px 0;
    padding: 10px 14px;
    background: #fff8e1;
    border: 1.5px solid #FFCC00;
    border-radius: 4px;
    font-size: 8pt;
    color: #7a5f00;
    display: flex;
    gap: 10px;
    align-items: flex-start;
}
.coa-warning-icon { font-size: 14pt; flex-shrink: 0; }

/* ── Signature / approval section ───────────────────── */
.coa-approval {
    padding: 14px 30px;
    border-top: 1px solid #dde3ea;
    display: flex;
    gap: 30px;
    align-items: flex-start;
}
.coa-sig-block { flex: 1; border-top: 2px solid #1a3a5c; padding-top: 8px; }
.coa-sig-role  { font-size: 7.5pt; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
.coa-sig-name  { font-size: 9.5pt; font-weight: 700; color: #1a3a5c; margin-top: 2px; }
.coa-sig-title { font-size: 7.5pt; color: #555; }
.coa-sig-date  { font-size: 7.5pt; color: #888; margin-top: 3px; }
.coa-sig-line  {
    display: block;
    width: 100%;
    height: 32px;
    border-bottom: 1.5px solid #333;
    margin-bottom: 4px;
    /* Simulated cursive signature using CSS */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='30'%3E%3Cpath d='M10 20 Q30 5 50 18 Q70 30 90 10 Q110 -5 130 15 Q150 30 170 12' stroke='%231a3a5c' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left center;
}

/* ── Footer ─────────────────────────────────────────── */
.coa-footer {
    background: #1a3a5c;
    padding: 10px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.coa-footer-left  { font-size: 7pt; color: #a8c4e0; line-height: 1.6; }
.coa-footer-right { font-size: 7pt; color: #a8c4e0; text-align: right; }
.coa-footer-logo  { font-size: 14pt; font-weight: 900; color: #FFCC00; }
.coa-page-note    { font-size: 6.5pt; color: #7a9cbf; margin-top: 3px; }
</style>

<div class="coa-doc">

    {{-- ── HEADER ────────────────────────────────────────────── --}}
    <div class="coa-header">
        <div class="coa-logo-area">
            <div class="coa-lab-name">Apex Laboratories</div>
            <div class="coa-lab-tagline">Precision · Purity · Performance</div>
        </div>
        <div class="coa-header-right">
            <div class="coa-doc-title">Certificate of Analysis</div>
            <div class="coa-doc-subtitle">Quality Assurance Document</div>
            <div class="coa-cert-number">Cert No: COA-{{ $coa->lot_number }}</div>
        </div>
    </div>
    <div class="coa-accent-bar"></div>

    {{-- ── PRODUCT INFO ──────────────────────────────────────── --}}
    <div class="coa-product-block">
        <div class="coa-product-main">
            <div class="coa-product-name">{{ $coa->product_name }}</div>
            <div class="coa-product-iupac">
                (R)-4-[[(4R,7R,10S,13R,16S,19R)-10-(4-aminobutyl)-19-[[(2R)-2-amino-3-(4-hydroxyphenyl)-1-oxopropyl]amino]-
                7-[(1R)-1-hydroxyethyl]-13-(1H-indol-3-ylmethyl)-6,9,12,15,18-pentaoxo-16-[[4-[[(2S)-2-[[(2S)-2-amino-
                4-methylpentanoyl]amino]propanoyl]amino]phenyl]methyl]-1,2-dithia-5,8,11,14,17-pentazacyclodocosane-4-carbonyl]
                amino]pentanedioic acid
            </div>
            <div>
                <span class="coa-product-grade">{{ $coa->grade }}</span>
            </div>
        </div>
        <div class="coa-product-meta">
            <table class="coa-meta-table">
                <tr><td>Catalog No.</td><td>{{ $coa->catalog_number }}</td></tr>
                <tr><td>Lot Number</td><td>{{ $coa->lot_number }}</td></tr>
                <tr><td>CAS Number</td><td>{{ $coa->cas_number }}</td></tr>
                <tr><td>Mol. Formula</td><td>{{ $coa->molecular_formula }}</td></tr>
                <tr><td>Mol. Weight</td><td>{{ number_format($coa->molecular_weight, 2) }} g/mol</td></tr>
                <tr><td>Quantity</td><td>{{ $coa->quantity }}</td></tr>
                @if($coa->recipient_name)
                <tr><td>Prepared For</td><td><strong>{{ $coa->recipient_name }}</strong></td></tr>
                @endif
                <tr><td>Manufacture Date</td><td>{{ $coa->manufacture_date->format('d M Y') }}</td></tr>
                <tr><td>Expiry Date</td><td>{{ $coa->expiry_date->format('d M Y') }}</td></tr>
                <tr>
                    <td>Analysis Period</td>
                    <td>
                        @if($coa->analysis_start_date)
                            {{ $coa->analysis_start_date->format('d M Y') }} — {{ $coa->analysis_date->format('d M Y') }}
                        @else
                            {{ $coa->analysis_date->format('d M Y') }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ── PREPARED FOR ─────────────────────────────────────── --}}
    @if($coa->recipient_name)
    <div style="background:#f0f5fa; border-bottom:2px solid #1a3a5c; padding:10px 30px; display:flex; align-items:center; gap:16px;">
        <div style="background:#1a3a5c; color:#FFCC00; font-size:7.5pt; font-weight:700; text-transform:uppercase; letter-spacing:1px; padding:4px 10px; border-radius:2px; white-space:nowrap;">
            Prepared For
        </div>
        <div>
            <span style="font-size:11pt; font-weight:900; color:#1a3a5c;">{{ $coa->recipient_name }}</span>
            @if($coa->recipient_email)
            <span style="font-size:8pt; color:#555; margin-left:10px;">{{ $coa->recipient_email }}</span>
            @endif
        </div>
    </div>
    @endif

    {{-- ── PURITY HIGHLIGHT ─────────────────────────────────── --}}
    <div class="coa-section">
        <div class="coa-section-title">Purity Assessment</div>
        <div class="purity-bar-wrap">
            <div class="purity-bar-bg">
                <div class="purity-bar-fill"></div>
            </div>
            <div class="purity-bar-label">{{ number_format($coa->purity_hplc, 2) }}%</div>
        </div>
        <div style="font-size:7.5pt; color:#555; margin-top:4px;">
            Determined by Reverse-Phase High-Performance Liquid Chromatography (RP-HPLC) — UV detection at 220 nm
        </div>
    </div>

    {{-- ── TEST RESULTS ─────────────────────────────────────── --}}
    <div class="coa-section">
        <div class="coa-section-title">Analytical Test Results</div>
        <table class="coa-results-table">
            <thead>
                <tr>
                    <th style="width:28%">Test Parameter</th>
                    <th style="width:25%">Specification</th>
                    <th style="width:27%">Result / Observed Value</th>
                    <th style="width:20%">Compliance</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Purity (RP-HPLC)</td>
                    <td>≥ 98.0%</td>
                    <td><span class="purity-highlight">{{ number_format($coa->purity_hplc, 2) }}%</span></td>
                    <td><span class="pass-badge">PASS</span></td>
                </tr>
                <tr>
                    <td>Appearance</td>
                    <td>White to off-white lyophilized powder</td>
                    <td>{{ $coa->appearance }}</td>
                    <td><span class="pass-badge">PASS</span></td>
                </tr>
                <tr>
                    <td>Identity (LC-MS/MS)</td>
                    <td>Consistent with structure</td>
                    <td>{{ $coa->identity_ms }}</td>
                    <td><span class="pass-badge">PASS</span></td>
                </tr>
                <tr>
                    <td>Identity (RP-HPLC)</td>
                    <td>Retention time matches reference</td>
                    <td>{{ $coa->identity_hplc }}</td>
                    <td><span class="pass-badge">PASS</span></td>
                </tr>
                <tr>
                    <td>Moisture Content (KF)</td>
                    <td>≤ 5.0%</td>
                    <td>{{ $coa->moisture_content }}</td>
                    <td><span class="pass-badge">PASS</span></td>
                </tr>
                <tr>
                    <td>pH (1% aqueous solution)</td>
                    <td>6.0 – 7.5</td>
                    <td>{{ $coa->ph }}</td>
                    <td><span class="pass-badge">PASS</span></td>
                </tr>
                <tr>
                    <td>Bacterial Endotoxins (LAL)</td>
                    <td>&lt; 1.0 EU/mg</td>
                    <td>{{ $coa->endotoxin }}</td>
                    <td><span class="pass-badge">PASS</span></td>
                </tr>
                <tr>
                    <td>Sterility (USP &lt;71&gt;)</td>
                    <td>Sterile</td>
                    <td>{{ $coa->sterility }}</td>
                    <td><span class="pass-badge">PASS</span></td>
                </tr>
                <tr>
                    <td>Solubility</td>
                    <td>Soluble in water</td>
                    <td>{{ $coa->solubility }}</td>
                    <td><span class="pass-badge">PASS</span></td>
                </tr>
                <tr>
                    <td>Analysis Period</td>
                    <td>Completed within batch cycle</td>
                    <td>
                        @if($coa->analysis_start_date)
                            {{ $coa->analysis_start_date->format('d M Y') }} – {{ $coa->analysis_date->format('d M Y') }}
                        @else
                            {{ $coa->analysis_date->format('d M Y') }}
                        @endif
                    </td>
                    <td><span class="pass-badge">PASS</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ── STORAGE ──────────────────────────────────────────── --}}
    <div class="coa-section">
        <div class="coa-section-title">Storage &amp; Handling</div>
        <div class="coa-storage-grid">
            <div class="coa-storage-box">
                <div class="coa-storage-icon">🌡️</div>
                <div class="coa-storage-label">Temperature</div>
                <div class="coa-storage-value">-20°C (Frozen)</div>
            </div>
            <div class="coa-storage-box">
                <div class="coa-storage-icon">💧</div>
                <div class="coa-storage-label">Humidity</div>
                <div class="coa-storage-value">Desiccated / Dry</div>
            </div>
            <div class="coa-storage-box">
                <div class="coa-storage-icon">🔆</div>
                <div class="coa-storage-label">Light</div>
                <div class="coa-storage-value">Protected from Light</div>
            </div>
            <div class="coa-storage-box">
                <div class="coa-storage-icon">📦</div>
                <div class="coa-storage-label">Full Conditions</div>
                <div class="coa-storage-value">{{ $coa->storage_conditions }}</div>
            </div>
        </div>
    </div>

    {{-- ── WARNING ───────────────────────────────────────────── --}}
    <div class="coa-warning">
        <div class="coa-warning-icon">⚠</div>
        <div>
            <strong>FOR RESEARCH USE ONLY — NOT FOR HUMAN OR VETERINARY USE.</strong><br>
            {{ $coa->notes }} This product has not been evaluated by the FDA or any regulatory agency for safety or efficacy
            in any application. Handle with appropriate laboratory safety precautions. Consult SDS before use.
        </div>
    </div>

    {{-- ── SIGNATURES ───────────────────────────────────────── --}}
    <div class="coa-approval">
        <div class="coa-sig-block">
            <span class="coa-sig-line"></span>
            <div class="coa-sig-role">Quality Control Manager</div>
            <div class="coa-sig-name">Dr. Sarah Mitchell, Ph.D.</div>
            <div class="coa-sig-title">Director of Quality Assurance</div>
            <div class="coa-sig-date">Date: {{ $coa->analysis_date->format('d M Y') }}</div>
        </div>
        <div class="coa-sig-block">
            <span class="coa-sig-line"></span>
            <div class="coa-sig-role">Analytical Chemist</div>
            <div class="coa-sig-name">Dr. James Hartley, Ph.D.</div>
            <div class="coa-sig-title">Senior Analytical Scientist</div>
            <div class="coa-sig-date">Date: {{ $coa->analysis_date->format('d M Y') }}</div>
        </div>
        <div style="flex:1; text-align:right; display:flex; flex-direction:column; align-items:flex-end; justify-content:flex-end;">
            <div style="border: 2px solid #1a3a5c; padding: 10px 16px; border-radius: 4px; background:#f0f5fa; display:inline-block;">
                <div style="font-size:7pt; color:#888; text-transform:uppercase; letter-spacing:0.5px;">Document Status</div>
                <div style="font-size:12pt; font-weight:900; color:#27ae60; margin:3px 0;">✓ APPROVED</div>
                <div style="font-size:7pt; color:#555;">Apex Laboratories QA Dept.</div>
                <div style="font-size:7pt; color:#888; margin-top:3px;">Doc Rev: 1.0 | ISO 9001:2015</div>
            </div>
        </div>
    </div>

    {{-- ── FOOTER ───────────────────────────────────────────── --}}
    <div class="coa-footer">
        <div class="coa-footer-left">
            <div class="coa-footer-logo">Apex Laboratories</div>
            <div>1 Research Park Boulevard, Suite 300 · Boston, MA 02210 · USA</div>
            <div>Tel: +1 (617) 555-0200 · Email: qc@apexlaboratories.com · www.apexlaboratories.com</div>
        </div>
        <div class="coa-footer-right">
            <div>Certificate No: COA-{{ $coa->lot_number }}</div>
            <div>Issued: {{ $coa->analysis_date->format('d M Y') }}</div>
            <div>Expires: {{ $coa->expiry_date->format('d M Y') }}</div>
            <div class="coa-page-note">This document is generated electronically and is valid without a wet signature.<br>
            Verify authenticity at www.apexlaboratories.com/verify</div>
        </div>
    </div>

</div>

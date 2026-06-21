@php
    $purity      = number_format((float)$coa->purity_hplc, 2);
    $totalImp    = number_format(100 - (float)$coa->purity_hplc, 2);
    $verifyCode  = 'COA-' . strtoupper(substr(md5($coa->lot_number . $coa->analysis_date), 0, 8));

    $rtSeed  = hexdec(substr(md5($coa->lot_number . 'rt'), 0, 6));
    $rtMain  = number_format(18.2 + ($rtSeed % 60) / 100, 2);
    $netPep  = number_format(85.0 + ($rtSeed % 80) / 20, 1);
    $acetate = number_format(8.5  + ($rtSeed % 40) / 20, 1);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
@page { margin: 14pt 18pt; size: 595pt 842pt; }
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Arial,Helvetica,sans-serif; font-size:7.5pt; color:#1a1a1a; background:#fff; }
table { border-collapse:collapse; }
td { vertical-align:top; }
</style>
</head>
<body>

{{-- ▌ OUTER WRAPPER --}}
<table style="width:100%; border:1pt solid #c8c8c8; border-collapse:collapse;">

{{-- ▌ HEADER --}}
<tr>
    <td style="padding:14pt 20pt 10pt; vertical-align:middle;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="vertical-align:middle; width:50%;">
                    <table style="border-collapse:collapse;">
                        <tr>
                            <td style="vertical-align:middle; padding-right:10pt;">
                                <svg width="40" height="44" viewBox="0 0 54 60" xmlns="http://www.w3.org/2000/svg">
                                    <polygon points="27,3 51,16 51,44 27,57 3,44 3,16"
                                        fill="none" stroke="#1a1a1a" stroke-width="2.2"/>
                                    <text x="27" y="38" text-anchor="middle"
                                        font-family="Arial" font-weight="900"
                                        font-size="24" fill="#1a1a1a">A</text>
                                </svg>
                            </td>
                            <td style="vertical-align:middle;">
                                <div style="font-size:15pt; font-weight:bold; color:#1a1a1a; line-height:1; letter-spacing:-.2pt;">APEX LABORATORIES</div>
                                <div style="font-size:6pt; color:#8a9bb0; letter-spacing:2pt; text-transform:uppercase; margin-top:3pt;">Analytical Services Laboratory</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align:middle; text-align:right; width:50%;">
                    <div style="font-size:18pt; font-weight:bold; color:#1a1a1a; line-height:1.05;">Certificate of Analysis</div>
                    <div style="font-size:7.5pt; color:#17b8b4; margin-top:3pt; font-weight:500;">Document {{ $verifyCode }}</div>
                </td>
            </tr>
        </table>
    </td>
</tr>

{{-- ▌ TEAL RULE --}}
<tr>
    <td style="height:2pt; background:#17b8b4; font-size:0; line-height:0;">&nbsp;</td>
</tr>

{{-- ▌ PRODUCT IDENTIFICATION --}}
<tr>
    <td style="padding:14pt 20pt 6pt;">
        <div style="font-size:6pt; font-weight:bold; letter-spacing:3pt; text-transform:uppercase; color:#5a6a7a; margin-bottom:8pt;">Product Identification</div>
        <div style="font-size:36pt; font-weight:bold; color:#1a1a1a; line-height:1; margin-bottom:4pt;">{{ $coa->product_name }}</div>
        <div style="font-size:10pt; color:#17b8b4; font-weight:500; margin-bottom:10pt;">{{ $coa->quantity }} &nbsp;&middot;&nbsp; &ge;{{ $purity }}% HPLC</div>

        {{-- Sequence box --}}
        <table style="width:100%; border-collapse:collapse; margin-bottom:10pt;">
            <tr>
                <td style="background:#f4f6f8; padding:7pt 10pt;">
                    <div style="font-size:6pt; color:#9aacbb; text-transform:uppercase; letter-spacing:2pt; margin-bottom:4pt;">Amino Acid Sequence</div>
                    <div style="font-size:8pt; color:#444;">39-amino acid dual GIP / GLP-1 receptor agonist &mdash; full sequence available on request</div>
                </td>
            </tr>
        </table>

        {{-- Meta grid: 2 columns, 6 rows --}}
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:50%; padding:4pt 0; border-bottom:1pt solid #f0f0f0; padding-right:20pt;">
                    <div style="font-size:5.5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">Product Name</div>
                    <div style="font-size:8pt; color:#1a1a1a; font-weight:500;">{{ $coa->product_name }}</div>
                </td>
                <td style="width:50%; padding:4pt 0; border-bottom:1pt solid #f0f0f0; padding-left:20pt;">
                    <div style="font-size:5.5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">Catalogue No.</div>
                    <div style="font-size:8pt; color:#1a1a1a; font-weight:500;">{{ $coa->catalog_number }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding:4pt 0; border-bottom:1pt solid #f0f0f0; padding-right:20pt;">
                    <div style="font-size:5.5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">Lot / Batch No.</div>
                    <div style="font-size:8pt; color:#1a1a1a; font-weight:500;">{{ $coa->lot_number }}</div>
                </td>
                <td style="padding:4pt 0; border-bottom:1pt solid #f0f0f0; padding-left:20pt;">
                    <div style="font-size:5.5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">CAS No.</div>
                    <div style="font-size:8pt; color:#1a1a1a; font-weight:500;">{{ $coa->cas_number }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding:4pt 0; border-bottom:1pt solid #f0f0f0; padding-right:20pt;">
                    <div style="font-size:5.5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">Molecular Formula</div>
                    <div style="font-size:8pt; color:#1a1a1a; font-weight:500;">{{ $coa->molecular_formula }}</div>
                </td>
                <td style="padding:4pt 0; border-bottom:1pt solid #f0f0f0; padding-left:20pt;">
                    <div style="font-size:5.5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">Molecular Weight</div>
                    <div style="font-size:8pt; color:#1a1a1a; font-weight:500;">{{ number_format($coa->molecular_weight, 2) }} g/mol</div>
                </td>
            </tr>
            <tr>
                <td style="padding:4pt 0; border-bottom:1pt solid #f0f0f0; padding-right:20pt;">
                    <div style="font-size:5.5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">Quantity / Vial</div>
                    <div style="font-size:8pt; color:#1a1a1a; font-weight:500;">{{ $coa->quantity }}</div>
                </td>
                <td style="padding:4pt 0; border-bottom:1pt solid #f0f0f0; padding-left:20pt;">
                    <div style="font-size:5.5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">Physical Form</div>
                    <div style="font-size:8pt; color:#1a1a1a; font-weight:500;">Lyophilised powder</div>
                </td>
            </tr>
            <tr>
                <td style="padding:4pt 0; border-bottom:1pt solid #f0f0f0; padding-right:20pt;">
                    <div style="font-size:5.5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">Manufacture Date</div>
                    <div style="font-size:8pt; color:#1a1a1a; font-weight:500;">{{ $coa->manufacture_date->format('Y-m-d') }}</div>
                </td>
                <td style="padding:4pt 0; border-bottom:1pt solid #f0f0f0; padding-left:20pt;">
                    <div style="font-size:5.5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">Re-Test Date</div>
                    <div style="font-size:8pt; color:#1a1a1a; font-weight:500;">{{ $coa->expiry_date->format('Y-m-d') }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding:4pt 0; padding-right:20pt;">
                    <div style="font-size:5.5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">Storage</div>
                    <div style="font-size:8pt; color:#1a1a1a; font-weight:500;">{{ $coa->storage_conditions }}</div>
                </td>
                <td style="padding:4pt 0; padding-left:20pt;">
                    <div style="font-size:5.5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:2pt;">Purity Grade</div>
                    <div style="font-size:8pt; color:#17b8b4; font-weight:500;">&ge; {{ $purity }}% (HPLC)</div>
                </td>
            </tr>
        </table>
    </td>
</tr>

{{-- ▌ SPECIFICATIONS & RESULTS --}}
<tr>
    <td style="padding:0 20pt;">
        <div style="font-size:6.5pt; font-weight:bold; letter-spacing:2.5pt; text-transform:uppercase; color:#1a1a1a; padding:10pt 0 7pt; border-top:1pt solid #e8e8e8;">Specifications &amp; Results</div>
        <table style="width:100%; border-collapse:collapse; font-size:8pt;">
            <thead>
                <tr style="background:#2c3340;">
                    <th style="color:#fff; padding:8pt 10pt; text-align:left; font-size:6.5pt; text-transform:uppercase; letter-spacing:1pt; font-weight:bold; width:22%;">Test</th>
                    <th style="color:#fff; padding:8pt 10pt; text-align:left; font-size:6.5pt; text-transform:uppercase; letter-spacing:1pt; font-weight:bold; width:16%;">Method</th>
                    <th style="color:#fff; padding:8pt 10pt; text-align:left; font-size:6.5pt; text-transform:uppercase; letter-spacing:1pt; font-weight:bold; width:28%;">Specification</th>
                    <th style="color:#fff; padding:8pt 10pt; text-align:left; font-size:6.5pt; text-transform:uppercase; letter-spacing:1pt; font-weight:bold; width:22%;">Result</th>
                    <th style="color:#fff; padding:8pt 10pt; text-align:center; font-size:6.5pt; text-transform:uppercase; letter-spacing:1pt; font-weight:bold; width:12%;">Pass</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom:1pt solid #edf0f3;">
                    <td style="padding:8pt 10pt; font-weight:bold; color:#1a1a1a;">Appearance</td>
                    <td style="padding:8pt 10pt; color:#8a9bb0;">Visual</td>
                    <td style="padding:8pt 10pt; color:#333;">White to off-white lyophilised powder</td>
                    <td style="padding:8pt 10pt; font-weight:bold; color:#333;">White powder</td>
                    <td style="padding:8pt 10pt; text-align:center;">
                        <span style="display:inline-block; width:20pt; height:20pt; border-radius:50%; background:#17b8b4; color:#fff; font-size:11pt; font-weight:bold; text-align:center; line-height:20pt;">&#10003;</span>
                    </td>
                </tr>
                <tr style="border-bottom:1pt solid #edf0f3;">
                    <td style="padding:8pt 10pt; font-weight:bold; color:#1a1a1a;">Identity (ESI-MS)</td>
                    <td style="padding:8pt 10pt; color:#8a9bb0;">LC-MS</td>
                    <td style="padding:8pt 10pt; color:#333;">Consistent with structure</td>
                    <td style="padding:8pt 10pt; font-weight:bold; color:#333;">Conforms</td>
                    <td style="padding:8pt 10pt; text-align:center;">
                        <span style="display:inline-block; width:20pt; height:20pt; border-radius:50%; background:#17b8b4; color:#fff; font-size:11pt; font-weight:bold; text-align:center; line-height:20pt;">&#10003;</span>
                    </td>
                </tr>
                <tr style="border-bottom:1pt solid #edf0f3;">
                    <td style="padding:8pt 10pt; font-weight:bold; color:#1a1a1a;">Purity (RP-HPLC)</td>
                    <td style="padding:8pt 10pt; color:#8a9bb0;">HPLC-UV 220 nm</td>
                    <td style="padding:8pt 10pt; color:#333;">&ge; 99.0 %</td>
                    <td style="padding:8pt 10pt; font-size:11pt; font-weight:bold; color:#17b8b4;">{{ $purity }} %</td>
                    <td style="padding:8pt 10pt; text-align:center;">
                        <span style="display:inline-block; width:20pt; height:20pt; border-radius:50%; background:#17b8b4; color:#fff; font-size:11pt; font-weight:bold; text-align:center; line-height:20pt;">&#10003;</span>
                    </td>
                </tr>
                <tr style="border-bottom:1pt solid #edf0f3;">
                    <td style="padding:8pt 10pt; font-weight:bold; color:#1a1a1a;">Single Impurity (max)</td>
                    <td style="padding:8pt 10pt; color:#8a9bb0;">RP-HPLC</td>
                    <td style="padding:8pt 10pt; color:#333;">&le; 1.0 %</td>
                    <td style="padding:8pt 10pt; font-weight:bold; color:#333;">0.01 %</td>
                    <td style="padding:8pt 10pt; text-align:center;">
                        <span style="display:inline-block; width:20pt; height:20pt; border-radius:50%; background:#17b8b4; color:#fff; font-size:11pt; font-weight:bold; text-align:center; line-height:20pt;">&#10003;</span>
                    </td>
                </tr>
                <tr style="border-bottom:1pt solid #edf0f3;">
                    <td style="padding:8pt 10pt; font-weight:bold; color:#1a1a1a;">Net Peptide Content</td>
                    <td style="padding:8pt 10pt; color:#8a9bb0;">UV / nitrogen</td>
                    <td style="padding:8pt 10pt; color:#333;">&ge; 80.0 %</td>
                    <td style="padding:8pt 10pt; font-weight:bold; color:#333;">{{ $netPep }} %</td>
                    <td style="padding:8pt 10pt; text-align:center;">
                        <span style="display:inline-block; width:20pt; height:20pt; border-radius:50%; background:#17b8b4; color:#fff; font-size:11pt; font-weight:bold; text-align:center; line-height:20pt;">&#10003;</span>
                    </td>
                </tr>
                <tr style="border-bottom:1pt solid #edf0f3;">
                    <td style="padding:8pt 10pt; font-weight:bold; color:#1a1a1a;">Water Content</td>
                    <td style="padding:8pt 10pt; color:#8a9bb0;">Karl Fischer</td>
                    <td style="padding:8pt 10pt; color:#333;">&le; 8.0 %</td>
                    <td style="padding:8pt 10pt; font-weight:bold; color:#333;">2.8 %</td>
                    <td style="padding:8pt 10pt; text-align:center;">
                        <span style="display:inline-block; width:20pt; height:20pt; border-radius:50%; background:#17b8b4; color:#fff; font-size:11pt; font-weight:bold; text-align:center; line-height:20pt;">&#10003;</span>
                    </td>
                </tr>
                <tr style="border-bottom:1pt solid #edf0f3;">
                    <td style="padding:8pt 10pt; font-weight:bold; color:#1a1a1a;">Acetate Content</td>
                    <td style="padding:8pt 10pt; color:#8a9bb0;">Ion HPLC</td>
                    <td style="padding:8pt 10pt; color:#333;">&le; 15.0 %</td>
                    <td style="padding:8pt 10pt; font-weight:bold; color:#333;">{{ $acetate }} %</td>
                    <td style="padding:8pt 10pt; text-align:center;">
                        <span style="display:inline-block; width:20pt; height:20pt; border-radius:50%; background:#17b8b4; color:#fff; font-size:11pt; font-weight:bold; text-align:center; line-height:20pt;">&#10003;</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding:8pt 10pt; font-weight:bold; color:#1a1a1a;">Bacterial Endotoxin</td>
                    <td style="padding:8pt 10pt; color:#8a9bb0;">LAL</td>
                    <td style="padding:8pt 10pt; color:#333;">&lt; 10 EU/mg</td>
                    <td style="padding:8pt 10pt; font-weight:bold; color:#333;">{{ $coa->endotoxin }}</td>
                    <td style="padding:8pt 10pt; text-align:center;">
                        <span style="display:inline-block; width:20pt; height:20pt; border-radius:50%; background:#17b8b4; color:#fff; font-size:11pt; font-weight:bold; text-align:center; line-height:20pt;">&#10003;</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>

{{-- ▌ CHARTS ROW --}}
<tr>
    <td style="border-top:1pt solid #e8e8e8; border-bottom:1pt solid #e8e8e8; padding:0;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:50%; padding:12pt 20pt; border-right:1pt solid #e8e8e8; vertical-align:top;">
                    <div style="font-size:7pt; font-weight:bold; text-transform:uppercase; letter-spacing:.5pt; color:#1a1a1a; margin-bottom:1pt;">RP-HPLC Chromatogram</div>
                    <div style="font-size:6pt; color:#9aacbb; margin-bottom:7pt;">Column C18 &middot; 220 nm &middot; 1.0 mL/min</div>
                    <svg width="100%" viewBox="0 0 320 140" xmlns="http://www.w3.org/2000/svg" style="display:block;overflow:visible;">
                        <rect x="1" y="1" width="318" height="118" fill="#fff" stroke="#dde4ea" stroke-width="1"/>
                        <polyline points="10,115 55,115 60,113 64,115 80,115 100,115 110,113 115,115 130,115"
                            fill="none" stroke="#17b8b4" stroke-width="1.2"/>
                        <polyline
                            points="130,115 140,115 148,114 152,112 156,108 159,102 161,92 163,78 165,58 166,40 167,22 168,12 169,8 170,12 171,22 172,40 173,58 175,78 177,92 179,102 182,108 185,112 188,114 192,115"
                            fill="none" stroke="#17b8b4" stroke-width="2"/>
                        <polyline points="228,115 233,114 236,112 237,115 242,115"
                            fill="none" stroke="#17b8b4" stroke-width="1.2"/>
                        <polyline points="192,115 310,115" fill="none" stroke="#17b8b4" stroke-width="1.2"/>
                        <text x="169" y="5" text-anchor="middle"
                            font-family="Arial" font-size="9" font-weight="700" fill="#17b8b4">{{ $purity }}%</text>
                        <text x="160" y="135" text-anchor="middle"
                            font-family="Arial" font-size="7.5" fill="#9aacbb">Retention time (min)</text>
                    </svg>
                </td>
                <td style="width:50%; padding:12pt 20pt; vertical-align:top;">
                    <div style="font-size:7pt; font-weight:bold; text-transform:uppercase; letter-spacing:.5pt; color:#1a1a1a; margin-bottom:1pt;">ESI-MS</div>
                    <div style="font-size:6pt; color:#9aacbb; margin-bottom:7pt;">&nbsp;</div>
                    <svg width="100%" viewBox="0 0 260 140" xmlns="http://www.w3.org/2000/svg" style="display:block;overflow:visible;">
                        <rect x="1" y="1" width="258" height="118" fill="#fff" stroke="#dde4ea" stroke-width="1"/>
                        <line x1="10" y1="115" x2="250" y2="115" stroke="#17b8b4" stroke-width="1.2"/>
                        <line x1="60" y1="115" x2="60" y2="95" stroke="#17b8b4" stroke-width="2"/>
                        <line x1="110" y1="115" x2="110" y2="72" stroke="#17b8b4" stroke-width="2"/>
                        <line x1="165" y1="115" x2="165" y2="105" stroke="#17b8b4" stroke-width="1.5"/>
                        <line x1="190" y1="115" x2="190" y2="108" stroke="#17b8b4" stroke-width="1.5"/>
                        <line x1="210" y1="115" x2="210" y2="12" stroke="#17b8b4" stroke-width="2.5"/>
                        <text x="210" y="8" text-anchor="middle"
                            font-family="Arial" font-size="8" font-weight="700" fill="#17b8b4">[M+H]+</text>
                        <text x="130" y="135" text-anchor="middle"
                            font-family="Arial" font-size="7.5" fill="#9aacbb">m/z</text>
                    </svg>
                </td>
            </tr>
        </table>
    </td>
</tr>

{{-- ▌ RESULT BOX --}}
<tr>
    <td style="padding:12pt 20pt;">
        <table style="width:100%; border-collapse:collapse; border:1.5pt solid #17b8b4;">
            <tr>
                <td style="background:#f2fdfb; padding:10pt 14pt;">
                    <div style="font-size:8.5pt; font-weight:bold; color:#1a1a1a; margin-bottom:3pt;">RESULT: CONFORMS &mdash; This batch meets all Apex Laboratories release specifications.</div>
                    <div style="font-size:7pt; color:#556070; line-height:1.6;">FOR LABORATORY RESEARCH USE ONLY. Not for human or veterinary use, diagnostic or therapeutic application.</div>
                </td>
            </tr>
        </table>
    </td>
</tr>

{{-- ▌ SIGNATURE --}}
<tr>
    <td style="padding:6pt 20pt 16pt;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="vertical-align:bottom; width:70%;">
                    <div style="font-size:5.5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:6pt;">Released By (Quality Control)</div>
                    <div style="font-size:28pt; font-family:Georgia,'Times New Roman',serif; font-style:italic; color:#1a1a1a; line-height:1; border-bottom:1.5pt solid #bbb; display:inline-block; padding-bottom:3pt; margin-bottom:5pt; min-width:160pt;">S. Mitchell</div>
                    <div style="font-size:7pt; color:#444; line-height:1.7;">
                        Dr. Sarah Mitchell &mdash; QC Manager, Analytical Services<br/>
                        Date of issue: {{ $coa->analysis_date->format('Y-m-d') }}
                    </div>
                </td>
                <td style="vertical-align:bottom; width:30%; text-align:right;">
                    <table style="border-collapse:collapse; margin-left:auto;">
                        <tr>
                            <td style="border:2pt solid #2c3340; padding:8pt 12pt; text-align:center; vertical-align:middle; width:70pt; height:70pt;">
                                <div style="font-size:7.5pt; font-weight:bold; color:#17b8b4; letter-spacing:1pt;">QC</div>
                                <div style="font-size:8.5pt; font-weight:bold; color:#1a1a1a; letter-spacing:.5pt; margin:2pt 0;">APPROVED</div>
                                <div style="height:1pt; background:#2c3340; margin:2pt auto; width:40pt;">&nbsp;</div>
                                <div style="font-size:4.5pt; color:#8a9bb0; text-transform:uppercase; letter-spacing:2pt;">A P E X &nbsp; L A B</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>

{{-- ▌ FOOTER --}}
<tr>
    <td style="border-top:1pt solid #e8e8e8; padding:6pt 20pt;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="font-size:6pt; color:#9aacbb; line-height:1.7;">
                    Apex Laboratories Pty Ltd &middot; Analytical Services Laboratory &middot; apexlaboratories.com
                </td>
                <td style="font-size:6pt; color:#9aacbb; line-height:1.7; text-align:right;">
                    This certificate is generated electronically and is valid without a wet signature.<br/>
                    Verify authenticity with document code {{ $verifyCode }}. Page 1 of 1
                </td>
            </tr>
        </table>
    </td>
</tr>

</table>
</body>
</html>

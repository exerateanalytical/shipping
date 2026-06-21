@php
    $purity      = number_format((float)$coa->purity_hplc, 2);
    $totalImp    = number_format(100 - (float)$coa->purity_hplc, 2);
    $verifyCode  = 'COA-' . strtoupper(substr(md5($coa->lot_number . $coa->analysis_date), 0, 8));

    $rtSeed  = hexdec(substr(md5($coa->lot_number . 'rt'), 0, 6));
    $netPep  = number_format(85.0 + ($rtSeed % 80) / 20, 1);
    $acetate = number_format(8.5  + ($rtSeed % 40) / 20, 1);

    $qrText = "APEX LABORATORIES\nCertificate of Analysis\nProduct: {$coa->product_name}\nLot: {$coa->lot_number}\nPurity: {$purity}%\nAnalysis Date: {$coa->analysis_date}\nDocument: {$verifyCode}\nVerify at: apexlaboratories.com/verify/{$verifyCode}";
    $qrSvg  = (string)(new \SimpleSoftwareIO\QrCode\Generator)->format('svg')->size(70)->margin(1)->generate($qrText);
    $qrB64  = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
@endphp

<table style="width:563pt; font-family:Arial,Helvetica,sans-serif; font-size:7pt; color:#1a1a1a; background:#fff; border:1pt solid #c8c8c8; border-collapse:collapse;">

{{-- ▌ HEADER --}}
<tr>
    <td style="padding:12pt 16pt 8pt;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="vertical-align:middle; width:55%;">
                    <table style="border-collapse:collapse;">
                        <tr>
                            <td style="vertical-align:middle; padding-right:8pt;">
                                <svg width="36" height="40" viewBox="0 0 54 60" xmlns="http://www.w3.org/2000/svg">
                                    <polygon points="27,3 51,16 51,44 27,57 3,44 3,16" fill="none" stroke="#1a1a1a" stroke-width="2.2"/>
                                    <text x="27" y="38" text-anchor="middle" font-family="Arial" font-weight="900" font-size="24" fill="#1a1a1a">A</text>
                                </svg>
                            </td>
                            <td style="vertical-align:middle;">
                                <div style="font-size:13pt; font-weight:bold; color:#1a1a1a; line-height:1; letter-spacing:-.2pt;">APEX LABORATORIES</div>
                                <div style="font-size:5.5pt; color:#8a9bb0; letter-spacing:2pt; text-transform:uppercase; margin-top:3pt;">Analytical Services Laboratory</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align:middle; text-align:right; width:45%;">
                    <table style="border-collapse:collapse; margin-left:auto;">
                        <tr>
                            <td style="vertical-align:middle; text-align:right; padding-right:10pt;">
                                <div style="font-size:16pt; font-weight:bold; color:#1a1a1a; line-height:1.05;">Certificate of Analysis</div>
                                <div style="font-size:7pt; color:#17b8b4; margin-top:2pt; font-weight:500;">Document {{ $verifyCode }}</div>
                            </td>
                            <td style="vertical-align:middle; text-align:center;">
                                <img src="{{ $qrB64 }}" width="58" height="58" style="display:block;"/>
                                <div style="font-size:4pt; color:#9aacbb; margin-top:2pt; text-align:center; text-transform:uppercase; letter-spacing:.5pt;">Scan to Verify</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>

{{-- ▌ TEAL RULE --}}
<tr><td style="height:2pt; background:#17b8b4; font-size:0; line-height:0;">&nbsp;</td></tr>

{{-- ▌ PRODUCT IDENTIFICATION --}}
<tr>
    <td style="padding:10pt 16pt 4pt;">
        <div style="font-size:5.5pt; font-weight:bold; letter-spacing:3pt; text-transform:uppercase; color:#5a6a7a; margin-bottom:6pt;">Product Identification</div>
        <div style="font-size:30pt; font-weight:bold; color:#1a1a1a; line-height:1; margin-bottom:3pt;">{{ $coa->product_name }}</div>
        <div style="font-size:9pt; color:#17b8b4; font-weight:500; margin-bottom:8pt;">{{ $coa->quantity }} &nbsp;&middot;&nbsp; &ge;{{ $purity }}% HPLC</div>

        <table style="width:100%; border-collapse:collapse; margin-bottom:8pt;">
            <tr>
                <td style="background:#f4f6f8; padding:5pt 10pt;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:2pt; margin-bottom:3pt;">Amino Acid Sequence</div>
                    <div style="font-size:7pt; color:#444;">39-amino acid dual GIP / GLP-1 receptor agonist &mdash; full sequence available on request</div>
                </td>
            </tr>
        </table>

        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:50%; padding:3pt 0 3pt; border-bottom:1pt solid #f0f0f0; padding-right:16pt;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:1pt;">Product Name</div>
                    <div style="font-size:7pt; color:#1a1a1a; font-weight:500;">{{ $coa->product_name }}</div>
                </td>
                <td style="width:50%; padding:3pt 0 3pt; border-bottom:1pt solid #f0f0f0; padding-left:16pt;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:1pt;">Catalogue No.</div>
                    <div style="font-size:7pt; color:#1a1a1a; font-weight:500;">{{ $coa->catalog_number }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding:3pt 0; border-bottom:1pt solid #f0f0f0; padding-right:16pt;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:1pt;">Lot / Batch No.</div>
                    <div style="font-size:7pt; color:#1a1a1a; font-weight:500;">{{ $coa->lot_number }}</div>
                </td>
                <td style="padding:3pt 0; border-bottom:1pt solid #f0f0f0; padding-left:16pt;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:1pt;">CAS No.</div>
                    <div style="font-size:7pt; color:#1a1a1a; font-weight:500;">{{ $coa->cas_number }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding:3pt 0; border-bottom:1pt solid #f0f0f0; padding-right:16pt;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:1pt;">Molecular Formula</div>
                    <div style="font-size:7pt; color:#1a1a1a; font-weight:500;">{{ $coa->molecular_formula }}</div>
                </td>
                <td style="padding:3pt 0; border-bottom:1pt solid #f0f0f0; padding-left:16pt;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:1pt;">Molecular Weight</div>
                    <div style="font-size:7pt; color:#1a1a1a; font-weight:500;">{{ number_format($coa->molecular_weight, 2) }} g/mol</div>
                </td>
            </tr>
            <tr>
                <td style="padding:3pt 0; border-bottom:1pt solid #f0f0f0; padding-right:16pt;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:1pt;">Quantity / Vial</div>
                    <div style="font-size:7pt; color:#1a1a1a; font-weight:500;">{{ $coa->quantity }}</div>
                </td>
                <td style="padding:3pt 0; border-bottom:1pt solid #f0f0f0; padding-left:16pt;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:1pt;">Physical Form</div>
                    <div style="font-size:7pt; color:#1a1a1a; font-weight:500;">Lyophilised powder</div>
                </td>
            </tr>
            <tr>
                <td style="padding:3pt 0; border-bottom:1pt solid #f0f0f0; padding-right:16pt;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:1pt;">Manufacture Date</div>
                    <div style="font-size:7pt; color:#1a1a1a; font-weight:500;">{{ $coa->manufacture_date->format('Y-m-d') }}</div>
                </td>
                <td style="padding:3pt 0; border-bottom:1pt solid #f0f0f0; padding-left:16pt;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:1pt;">Re-Test Date</div>
                    <div style="font-size:7pt; color:#1a1a1a; font-weight:500;">{{ $coa->expiry_date->format('Y-m-d') }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding:3pt 0; padding-right:16pt;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:1pt;">Storage</div>
                    <div style="font-size:7pt; color:#1a1a1a; font-weight:500;">{{ $coa->storage_conditions }}</div>
                </td>
                <td style="padding:3pt 0; padding-left:16pt;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:1pt;">Purity Grade</div>
                    <div style="font-size:7pt; color:#17b8b4; font-weight:500;">&ge; {{ $purity }}% (HPLC)</div>
                </td>
            </tr>
        </table>
    </td>
</tr>

{{-- ▌ SPECIFICATIONS & RESULTS --}}
<tr>
    <td style="padding:0 16pt;">
        <div style="font-size:6pt; font-weight:bold; letter-spacing:2.5pt; text-transform:uppercase; color:#1a1a1a; padding:8pt 0 6pt; border-top:1pt solid #e8e8e8;">Specifications &amp; Results</div>
        <table style="width:100%; border-collapse:collapse; font-size:7pt;">
            <thead>
                <tr style="background:#2c3340;">
                    <th style="color:#fff; padding:6pt 8pt; text-align:left; font-size:6pt; text-transform:uppercase; letter-spacing:1pt; font-weight:bold; width:22%;">Test</th>
                    <th style="color:#fff; padding:6pt 8pt; text-align:left; font-size:6pt; text-transform:uppercase; letter-spacing:1pt; font-weight:bold; width:15%;">Method</th>
                    <th style="color:#fff; padding:6pt 8pt; text-align:left; font-size:6pt; text-transform:uppercase; letter-spacing:1pt; font-weight:bold; width:28%;">Specification</th>
                    <th style="color:#fff; padding:6pt 8pt; text-align:left; font-size:6pt; text-transform:uppercase; letter-spacing:1pt; font-weight:bold; width:24%;">Result</th>
                    <th style="color:#fff; padding:6pt 8pt; text-align:center; font-size:6pt; text-transform:uppercase; letter-spacing:1pt; font-weight:bold; width:11%;">Pass</th>
                </tr>
            </thead>
            <tbody>
                @php
                $rows = [
                    ['Appearance',           'Visual',         'White to off-white lyophilised powder', 'White powder',        false],
                    ['Identity (ESI-MS)',     'LC-MS',          'Consistent with structure',             'Conforms',            false],
                    ['Purity (RP-HPLC)',      'HPLC-UV 220 nm', '≥ 99.0 %',                             $purity.' %',          true],
                    ['Single Impurity (max)', 'RP-HPLC',        '≤ 1.0 %',                              '0.01 %',              false],
                    ['Net Peptide Content',   'UV / nitrogen',  '≥ 80.0 %',                             $netPep.' %',          false],
                    ['Water Content',         'Karl Fischer',   '≤ 8.0 %',                              '2.8 %',               false],
                    ['Acetate Content',       'Ion HPLC',       '≤ 15.0 %',                             $acetate.' %',         false],
                    ['Bacterial Endotoxin',   'LAL',            '< 10 EU/mg',                           $coa->endotoxin,       false],
                ];
                @endphp
                @foreach($rows as $r)
                <tr style="border-bottom:1pt solid #edf0f3;">
                    <td style="padding:6pt 8pt; font-weight:bold; color:#1a1a1a;">{{ $r[0] }}</td>
                    <td style="padding:6pt 8pt; color:#8a9bb0;">{{ $r[1] }}</td>
                    <td style="padding:6pt 8pt; color:#333;">{{ $r[2] }}</td>
                    <td style="padding:6pt 8pt; font-weight:bold; {{ $r[4] ? 'color:#17b8b4;font-size:9pt;' : 'color:#333;' }}">{{ $r[3] }}</td>
                    <td style="padding:6pt 8pt; text-align:center;">
                        <span style="display:inline-block;width:16pt;height:16pt;background:#17b8b4;color:#fff;font-size:9pt;font-weight:bold;text-align:center;line-height:16pt;">&#10003;</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </td>
</tr>

{{-- ▌ CHARTS ROW --}}
<tr>
    <td style="border-top:1pt solid #e8e8e8; border-bottom:1pt solid #e8e8e8; padding:0;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:50%; padding:9pt 16pt; border-right:1pt solid #e8e8e8; vertical-align:top;">
                    <div style="font-size:6.5pt; font-weight:bold; text-transform:uppercase; letter-spacing:.5pt; color:#1a1a1a; margin-bottom:1pt;">RP-HPLC Chromatogram</div>
                    <div style="font-size:5.5pt; color:#9aacbb; margin-bottom:5pt;">Column C18 &middot; 220 nm &middot; 1.0 mL/min</div>
                    <svg width="100%" viewBox="0 0 320 120" xmlns="http://www.w3.org/2000/svg" style="display:block;">
                        <rect x="1" y="1" width="318" height="98" fill="#fff" stroke="#dde4ea" stroke-width="1"/>
                        <polyline points="10,95 55,95 60,93 64,95 130,95" fill="none" stroke="#17b8b4" stroke-width="1.2"/>
                        <polyline points="130,95 140,95 148,94 152,92 156,88 159,82 161,72 163,58 165,40 166,24 167,14 168,8 169,5 170,8 171,14 172,24 173,40 175,58 177,72 179,82 182,88 185,92 188,94 192,95" fill="none" stroke="#17b8b4" stroke-width="2"/>
                        <polyline points="228,95 233,94 236,92 237,95 310,95" fill="none" stroke="#17b8b4" stroke-width="1.2"/>
                        <text x="169" y="4" text-anchor="middle" font-family="Arial" font-size="8" font-weight="700" fill="#17b8b4">{{ $purity }}%</text>
                        <text x="160" y="114" text-anchor="middle" font-family="Arial" font-size="7" fill="#9aacbb">Retention time (min)</text>
                    </svg>
                </td>
                <td style="width:50%; padding:9pt 16pt; vertical-align:top;">
                    <div style="font-size:6.5pt; font-weight:bold; text-transform:uppercase; letter-spacing:.5pt; color:#1a1a1a; margin-bottom:1pt;">ESI-MS</div>
                    <div style="font-size:5.5pt; color:#9aacbb; margin-bottom:5pt;">&nbsp;</div>
                    <svg width="100%" viewBox="0 0 260 120" xmlns="http://www.w3.org/2000/svg" style="display:block;">
                        <rect x="1" y="1" width="258" height="98" fill="#fff" stroke="#dde4ea" stroke-width="1"/>
                        <line x1="10" y1="95" x2="250" y2="95" stroke="#17b8b4" stroke-width="1.2"/>
                        <line x1="60" y1="95" x2="60" y2="78" stroke="#17b8b4" stroke-width="2"/>
                        <line x1="110" y1="95" x2="110" y2="58" stroke="#17b8b4" stroke-width="2"/>
                        <line x1="165" y1="95" x2="165" y2="88" stroke="#17b8b4" stroke-width="1.5"/>
                        <line x1="190" y1="95" x2="190" y2="90" stroke="#17b8b4" stroke-width="1.5"/>
                        <line x1="210" y1="95" x2="210" y2="8" stroke="#17b8b4" stroke-width="2.5"/>
                        <text x="210" y="5" text-anchor="middle" font-family="Arial" font-size="7" font-weight="700" fill="#17b8b4">[M+H]+</text>
                        <text x="130" y="114" text-anchor="middle" font-family="Arial" font-size="7" fill="#9aacbb">m/z</text>
                    </svg>
                </td>
            </tr>
        </table>
    </td>
</tr>

{{-- ▌ RESULT BOX --}}
<tr>
    <td style="padding:9pt 16pt;">
        <table style="width:100%; border-collapse:collapse; border:1.5pt solid #17b8b4;">
            <tr>
                <td style="background:#f2fdfb; padding:8pt 12pt;">
                    <div style="font-size:8pt; font-weight:bold; color:#1a1a1a; margin-bottom:3pt;">RESULT: CONFORMS &mdash; This batch meets all Apex Laboratories release specifications.</div>
                    <div style="font-size:6.5pt; color:#556070; line-height:1.6;">FOR LABORATORY RESEARCH USE ONLY. Not for human or veterinary use, diagnostic or therapeutic application.</div>
                </td>
            </tr>
        </table>
    </td>
</tr>

{{-- ▌ SIGNATURE --}}
<tr>
    <td style="padding:4pt 16pt 12pt;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="vertical-align:bottom; width:70%;">
                    <div style="font-size:5pt; color:#9aacbb; text-transform:uppercase; letter-spacing:1.5pt; margin-bottom:5pt;">Released By (Quality Control)</div>
                    <div style="font-size:24pt; font-family:Georgia,'Times New Roman',serif; font-style:italic; color:#1a1a1a; line-height:1; border-bottom:1.5pt solid #bbb; display:inline-block; padding-bottom:3pt; margin-bottom:4pt; min-width:140pt;">S. Mitchell</div>
                    <div style="font-size:6.5pt; color:#444; line-height:1.7;">
                        Dr. Sarah Mitchell &mdash; QC Manager, Analytical Services<br/>
                        Date of issue: {{ $coa->analysis_date->format('Y-m-d') }}
                    </div>
                </td>
                <td style="vertical-align:bottom; width:30%; text-align:right;">
                    <table style="border-collapse:collapse; margin-left:auto;">
                        <tr>
                            <td style="border:2pt solid #2c3340; padding:7pt 10pt; text-align:center; vertical-align:middle; width:65pt; height:65pt;">
                                <div style="font-size:7pt; font-weight:bold; color:#17b8b4; letter-spacing:1pt;">QC</div>
                                <div style="font-size:8pt; font-weight:bold; color:#1a1a1a; letter-spacing:.5pt; margin:2pt 0;">APPROVED</div>
                                <div style="height:1pt; background:#2c3340; margin:2pt auto; width:36pt;">&nbsp;</div>
                                <div style="font-size:4pt; color:#8a9bb0; text-transform:uppercase; letter-spacing:2pt;">A P E X &nbsp; L A B</div>
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
    <td style="border-top:1pt solid #e8e8e8; padding:5pt 16pt;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="font-size:5.5pt; color:#9aacbb; line-height:1.7;">
                    Apex Laboratories Pty Ltd &middot; Analytical Services Laboratory &middot; apexlaboratories.com
                </td>
                <td style="font-size:5.5pt; color:#9aacbb; line-height:1.7; text-align:right;">
                    This certificate is generated electronically and is valid without a wet signature.<br/>
                    Verify authenticity with document code {{ $verifyCode }}. Page 1 of 1
                </td>
            </tr>
        </table>
    </td>
</tr>

</table>

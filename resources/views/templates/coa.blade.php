<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>COA - {{ $coa->product_name }} - {{ $coa->lot_number }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: #e8e8e8; display: flex; flex-direction: column; align-items: center; padding: 20px; font-family: Arial, Helvetica, sans-serif; }
    .actions { margin-bottom: 16px; }
    .actions a { display: inline-block; padding: 10px 22px; background: #1a3a5c; color: #fff; text-decoration: none; font-weight: bold; border-radius: 4px; margin-right: 10px; }
    .actions a.back { background: #555; }
</style>
</head>
<body>
<div class="actions">
    <a href="{{ route('coa.download', $coa->id) }}">⬇ Download PDF</a>
    <a href="{{ route('coa.index') }}" class="back">← Back</a>
</div>
@include('templates.partials.coa_doc', ['coa' => $coa])
</body>
</html>

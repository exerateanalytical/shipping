<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>DHL Waybill - {{ $shipment->waybill_number }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: #e0e0e0; display: flex; flex-direction: column; align-items: center; padding: 20px; font-family: Arial, Helvetica, sans-serif; }
    .actions { margin-bottom: 16px; }
    .actions a { display: inline-block; padding: 10px 22px; background: #D40511; color: #fff; text-decoration: none; font-weight: bold; border-radius: 4px; margin-right: 10px; }
</style>
</head>
<body>
<div class="actions">
    <a href="{{ route('dhl.download', $shipment->id) }}">⬇ Download PDF</a>
    <a href="{{ route('shipments.index') }}" style="background:#555;">← Back</a>
</div>
@include('templates.partials.dhl_label_doc', ['shipment' => $shipment])
</body>
</html>

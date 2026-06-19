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
    .label-wrap { background: #fff; width: 5.5in; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
</style>
</head>
<body>
<div class="actions">
    <a href="{{ route('dhl.download', $shipment->id) }}">⬇ Download PDF</a>
    <a href="{{ route('shipments.index') }}" style="background:#555;">← Back</a>
</div>
<div class="label-wrap">
    @include('templates.partials.dhl_label_body', ['shipment' => $shipment])
</div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { margin: 0; size: 595pt 842pt; }
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Arial,Helvetica,sans-serif; font-size:7.5pt; color:#000; background:#fff; margin:0; padding:0; }
table { border-collapse:collapse; }
td { vertical-align:top; }
</style>
</head>
<body>
@include('templates.partials.dhl_label_doc', ['shipment' => $shipment])
</body>
</html>

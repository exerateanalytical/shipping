<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { background: #fff; font-family: Arial, Helvetica, sans-serif; }
</style>
</head>
<body>
@include('templates.partials.dhl_label_doc', ['shipment' => $shipment])
</body>
</html>

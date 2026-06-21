<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
@page { margin: 0; size: 595pt 842pt; }
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Arial,Helvetica,sans-serif; font-size:8.5pt; color:#1a1a1a; background:#fff; margin:0; padding:0; }
table { border-collapse:collapse; }
td { vertical-align:top; }
</style>
</head>
<body>
@include('templates.partials.coa_doc', ['coa' => $coa])
</body>
</html>

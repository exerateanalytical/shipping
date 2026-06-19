<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shipping Templates')</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; }
        .navbar { background: #D40511; padding: 12px 24px; display: flex; align-items: center; gap: 20px; }
        .navbar a { color: #fff; text-decoration: none; font-weight: bold; font-size: 14px; }
        .navbar .logo { font-size: 28px; font-weight: 900; letter-spacing: -1px; color: #FFCC00; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .card { background: #fff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 8px 18px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; cursor: pointer; border: none; }
        .btn-red { background: #D40511; color: #fff; }
        .btn-yellow { background: #FFCC00; color: #333; }
        .btn-outline { background: #fff; color: #D40511; border: 2px solid #D40511; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { background: #D40511; color: #fff; padding: 10px 12px; text-align: left; }
        td { padding: 10px 12px; border-bottom: 1px solid #eee; }
        tr:hover td { background: #fafafa; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; background: #FFCC00; color: #333; }
        h1, h2 { margin-bottom: 16px; }
    </style>
</head>
<body>
<nav class="navbar">
    <span class="logo">DHL</span>
    <a href="{{ route('shipments.index') }}">Shipping Labels</a>
    <a href="{{ route('coa.index') }}">COA Documents</a>
</nav>
<div class="container">
    @yield('content')
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الموردين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fb;
        }
    </style>
</head>

<body class="p-8" dir="rtl">

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('/') }}">تجميل</a>
            <div class="float-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="{{ route('items.index') }}">الأصناف</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('suppliers.index') }}">الموردين</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('purchases.index') }}">مشتريات</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('sales.index') }}">مبيعات</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('expenses.index') }}">المصروفات</a></li>
                </ul>
            </div>
        </div>
    </nav>

    {{ $slot }}

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>
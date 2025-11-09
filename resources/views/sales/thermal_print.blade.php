<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <title>إيصال مبيعات - رقم {{ $sale->id }}</title>
    <style>
        /* Add any thermal receipt styling here */
        .receipt-container {
            width: 80mm; /* Standard thermal paper width */
            margin: 0 auto;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .items-list table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-list td {
            padding: 3px 0;
        }
        .total-line {
            border-top: 1px dashed #000;
            margin-top: 10px;
            padding-top: 5px;
        }
    </style>
</head>
<body  onload="printAndRedirect();">
    <div class="receipt-container">
        <header>
            <h1 class="store-name">{{ env('APP_NAME') }}</h1>
            <p>رقم العملية: {{ $sale->id }}</p>
            <p>التاريخ: {{ $sale->created_at->format('Y-m-d H:i') }}</p>
            @if($sale->customer_name)
                <p>العميل: {{ $sale->customer_name }}</p>
            @endif
        </header>

        <section class="items-list">
            <table>
                <thead>
                    <tr>
                        <th>الصنف</th>
                        <th>الكمية</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $saleItem)
                        <tr>
                            <td>{{ $saleItem->item->name }}</td>
                            <td>{{ $saleItem->quantity }}</td>
                            <td>{{ number_format($saleItem->unit_price, 2) }}</td>
                            <td>{{ number_format($saleItem->sub_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="summary">
            <p class="total-line">
                <span>المجموع الكلي:</span>
                <span>{{ number_format($sale->total_amount, 2) }}</span>
            </p>
            @if($sale->discount_amount > 0)
                <p>
                    <span>الخصم:</span>
                    <span>{{ number_format($sale->discount_amount, 2) }}</span>
                </p>
            @endif
            <p class="total-line">
                <span>المبلغ النهائي:</span>
                <span>{{ number_format($sale->final_amount, 2) }}</span>
            </p>
            
        </section>

        @if($sale->notes)
            <section class="notes">
                <p>ملاحظات: {{ $sale->notes }}</p>
            </section>
        @endif

        <footer>
            <p>شكراً لتعاملكم معنا</p>
        </footer>
    </div>
    <script>
        function printAndRedirect() {
            window.print();
            window.onafterprint = function() {
                window.location.href = '/sales'; // Change this to your homepage URL
            };
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bill - {{ $order->bill_no }}</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .bill-container {
            width: 420px;
            margin: auto;
            background: #fff;
            border: 1px solid #ddd;
            padding: 20px;
        }

        .text-center {
            text-align: center;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 8px 4px;
        }

        table thead th {
            border-bottom: 1px solid #000;
        }

        table tbody tr:last-child td {
            border-bottom: 1px dashed #999;
        }

        .right {
            text-align: right;
        }

        .summary td {
            padding: 5px 0;
        }

        .total {
            font-size: 18px;
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 13px;
        }

        .actions {
            margin-top: 20px;
            text-align: center;
        }

        .actions button,
        .actions a {
            padding: 10px 18px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }

        .btn-print {
            background: #198754;
            color: #fff;
        }

        .btn-back {
            background: #6c757d;
            color: #fff;
        }

        @media print {

            body {
                background: #fff;
                padding: 0;
            }

            .bill-container {
                width: 100%;
                border: none;
                box-shadow: none;
            }

            .actions {
                display: none;
            }
        }
    </style>

</head>

<body>

    <div class="bill-container">

        <div class="text-center mb-20">
            <h2>{{ $order->branch?->restaurant?->name ?? $order->restaurant?->name }}</h2>

            <div>{{ $order->branch?->name }}</div>

            <small>
                {{ $order->branch?->address }}
            </small>
        </div>

        <table class="mb-20">

            <tr>
                <td><strong>Bill No</strong></td>
                <td class="right">{{ $order->bill_no }}</td>
            </tr>

            <tr>
                <td><strong>Token</strong></td>
                <td class="right">{{ $order->token_no }}</td>
            </tr>

            <tr>
                <td><strong>Date</strong></td>
                <td class="right">
                    {{ $order->bill_generated_at?->format('d M Y h:i A') }}
                </td>
            </tr>

            <tr>
                <td><strong>Customer</strong></td>
                <td class="right">{{ $order->customer_name }}</td>
            </tr>

            <tr>
                <td><strong>Mobile</strong></td>
                <td class="right">{{ $order->mobile_number }}</td>
            </tr>

            <tr>
                <td><strong>Table</strong></td>
                <td class="right">{{ $order->table_no }}</td>
            </tr>

        </table>

        <table>

            <thead>
                <tr>
                    <th align="left">Item</th>
                    <th class="right">Qty</th>
                    <th class="right">Rate</th>
                    <th class="right">Amount</th>
                </tr>
            </thead>

            <tbody>

                @foreach($order->items as $item)

                    <tr>

                        <td>
                            {{ $item->menuItem->name }}
                        </td>

                        <td class="right">
                            {{ $item->quantity }}
                        </td>

                        <td class="right">
                            ₹{{ number_format($item->price,2) }}
                        </td>

                        <td class="right">
                            ₹{{ number_format($item->subtotal,2) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

        <br>

        <table class="summary">

            <tr>

                <td>Subtotal</td>

                <td class="right">
                    ₹{{ number_format($order->subtotal,2) }}
                </td>

            </tr>

            <tr>

                <td>Tax</td>

                <td class="right">
                    ₹{{ number_format($order->tax,2) }}
                </td>

            </tr>

            <tr class="total">

                <td>Total</td>

                <td class="right">
                    ₹{{ number_format($order->total,2) }}
                </td>

            </tr>

        </table>

        <div class="footer">

            <p>
                Thank You For Visiting!
            </p>

            <small>
                Please Visit Again.
            </small>

        </div>

        <div class="actions">

            <button onclick="window.print()" class="btn-print">
                Print Bill
            </button>

            <a href="{{ url()->previous() }}" class="btn-back">
                Back
            </a>

        </div>

    </div>

</body>

</html>

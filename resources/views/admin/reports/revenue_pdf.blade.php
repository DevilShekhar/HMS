<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Report</title>

    <style>
        /* Base Reset & Simple Universally Supported Font */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
           font-family: DejaVu Sans, sans-serif;
            background-color: #0f1115;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        /* Invoice Container Structure */
        .invoice-card {
            width: 100%;
            max-width: 800px;
            background: #1a1d24;
            border: 1px solid rgba(255, 138, 0, 0.25);
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.6);
            color: #ffffff;
            overflow: hidden;
            border-radius: 8px;
        }

        /* Top Header Block with your exact brand gradient */
        .invoice-header-bg {
            background: #ff8a00;
            background: linear-gradient(135deg, #ff8a00, #ff5f00);
            padding: 45px 40px;
            text-align: center;
            position: relative;
            border-bottom: 3px solid #ff5f00;
        }

        /* Main Title Accent */
        .invoice-header-bg h2 {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #ffffff;
            margin-bottom: 15px;
        }

        /* Luxury Branding Elements */
        .brand-logo-wrapper {
            margin-bottom: 5px;
        }

        .hotel-text {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            letter-spacing: 5px;
            text-transform: uppercase;
            font-weight: 600;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 6px 16px;
            display: inline-block;
            border-radius: 2px;
            background: rgba(0, 0, 0, 0.1);
        }

        /* Table Content Container */
        .invoice-body {
            padding: 40px;
            background: #11141a;
        }

        /* Premium Financial Data Grid */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 16px 12px;
            border-top: 2px solid rgba(255, 138, 0, 0.4);
            border-bottom: 2px solid rgba(255, 138, 0, 0.4);
            color: #ff8a00;
        }

        /* Sleek column dividers */
        th:not(:last-child),
        td:not(:last-child) {
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        td {
            font-size: 14px;
            padding: 18px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            color: #e5e7eb;
        }

        /* Exact Layout Alignment Rules */
        th:first-child,
        td:first-child {
            text-align: left;
            font-weight: bold;
            color: #ffffff;
        }

        th:not(:first-child),
        td:not(:first-child) {
            text-align: right;
        }

        /* Total Column Highlight styling */
        td:last-child {
            color: #ff8a00;
            font-weight: bold;
        }

        /* Hover Interaction Effects */
        tbody tr:hover td {
            background: rgba(255, 138, 0, 0.04);
        }

        /* Base Premium Accent Line Strip */
        .invoice-bottom-bar {
            height: 12px;
            background: linear-gradient(135deg, #ff5f00, #ff8a00);
        }
    </style>
</head>

<body>

    <div class="invoice-card">

        <div class="invoice-header-bg">
            <h2>Revenue Report</h2>

            <div class="brand-logo-wrapper">
                <div class="hotel-text">RESTAURANT
                    MANAGEMENT SYSTEM </div>
            </div>
        </div>

        <div class="invoice-body">
            <table>
                <thead>
                    <tr>
                        <th>Branch</th>
                        <th>Today</th>
                        <th>This Month</th>
                        <th>This Year</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td>{{ $report['branch_name'] }}</td>
                            <td>{{ $currencySymbol ?? '₹' }}{{ number_format($report['today'], 2) }}</td>
                            <td>{{ $currencySymbol ?? '₹' }}{{ number_format($report['monthly'], 2) }}</td>
                            <td>{{ $currencySymbol ?? '₹' }}{{ number_format($report['yearly'], 2) }}</td>
                            <td>{{ $currencySymbol ?? '₹' }}{{ number_format($report['total'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="invoice-bottom-bar"></div>

    </div>

</body>

</html>

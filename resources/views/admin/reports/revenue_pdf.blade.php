<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        h2 {
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>Revenue Report</h2>

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

                    <td>₹{{ number_format($report['today'], 2) }}</td>

                    <td>₹{{ number_format($report['monthly'], 2) }}</td>

                    <td>₹{{ number_format($report['yearly'], 2) }}</td>

                    <td>₹{{ number_format($report['total'], 2) }}</td>

                </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>

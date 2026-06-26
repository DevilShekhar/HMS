<!DOCTYPE html>
<html>

<head>
    <title>Top Selling Items Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1,
        h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .branch-header {
            background-color: #f8f9fa;
            padding: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Top Selling Menu Items Report</h1>
        <p>{{ now()->format('d M, Y') }}</p>
    </div>

    @foreach($reports as $report)
        <div class="branch-header">
            <h2>{{ $report['branch'] }}</h2>
        </div>

        @if($report['items']->isEmpty())
            <p>No data available for this branch.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Menu Item</th>
                        <th>Quantity Sold</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['items'] as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->menuItem->name ?? 'Unknown' }}</td>
                            <td>{{ number_format($item->total_quantity) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach
</body>

</html>

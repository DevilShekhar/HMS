<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Selling Items Report</title>

    <style>
        /* Base Reset & Simple Universally Supported Font */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #0f1115;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        /* Invoice Container Structure */
        .report-card {
            width: 100%;
            max-width: 800px;
            background: #1a1d24;
            border: 1px solid rgba(255, 138, 0, 0.25);
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.6);
            color: #ffffff;
            overflow: hidden;
            border-radius: 8px;
        }

        /* Top Header Block with exact brand gradient */
        .report-header-bg {
            background: #ff8a00;
            background: linear-gradient(135deg, #ff8a00, #ff5f00);
            padding: 45px 40px;
            text-align: center;
            position: relative;
            border-bottom: 3px solid #ff5f00;
        }

        /* Main Title Accent */
        .report-header-bg h1 {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #ffffff;
            margin-bottom: 8px;
        }

        /* Clean, Simple Date Subtitle Style */
        .report-date {
            font-size: 14px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: normal;
            color: rgba(255, 255, 255, 0.85);
        }

        /* Main Body Area */
        .report-body {
            padding: 40px;
            background: #11141a;
        }

        /* Premium Section Subheaders for Branches */
        .branch-section {
            margin-bottom: 40px;
        }

        .branch-title {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #ffffff;
            margin-bottom: 15px;
            padding-left: 10px;
            border-left: 3px solid #ff8a00;
            /* Subtle orange brand tag indicator */
        }

        .no-data-msg {
            font-size: 14px;
            color: #9ca3af;
            padding: 15px 10px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 4px;
            border: 1px dashed rgba(255, 255, 255, 0.1);
        }

        /* Premium Financial Data Grid */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
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
        th:nth-child(1),
        td:nth-child(1) {
            text-align: center;
            width: 15%;
            font-weight: bold;
            color: #9ca3af;
        }

        th:nth-child(2),
        td:nth-child(2) {
            text-align: left;
            font-weight: bold;
            color: #ffffff;
        }

        th:nth-child(3),
        td:nth-child(3) {
            text-align: right;
            width: 30%;
            color: #ff8a00;
            font-weight: bold;
        }

        /* Hover Interaction Effects */
        tbody tr:hover td {
            background: rgba(255, 138, 0, 0.04);
        }

        /* Base Premium Accent Line Strip */
        .report-bottom-bar {
            height: 12px;
            background: linear-gradient(135deg, #ff5f00, #ff8a00);
        }
    </style>
</head>

<body>

    <div class="report-card">

        <div class="report-header-bg">
            <h1>Top Selling Items</h1>
            <div class="report-date">{{ now()->format('d M, Y') }}</div>
        </div>

        <div class="report-body">
            @foreach($reports as $report)
                <div class="branch-section">
                    <h2 class="branch-title">{{ $report['branch'] }}</h2>

                    @if($report['items']->isEmpty())
                        <p class="no-data-msg">No data available for this branch.</p>
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
                                        <td>#{{ $loop->iteration }}</td>
                                        <td>{{ $item->menuItem->name ?? 'Unknown' }}</td>
                                        <td>{{ number_format($item->total_quantity) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="report-bottom-bar"></div>

    </div>

</body>

</html>
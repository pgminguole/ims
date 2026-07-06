<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ ucfirst($reportType) }} Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-item {
            display: table-cell;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            text-align: center;
        }
        .summary-item h3 {
            margin: 0;
            color: #007bff;
        }
        .summary-item p {
            margin: 5px 0 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-primary { background: #007bff; color: white; }
        .badge-warning { background: #ffc107; color: black; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-info { background: #17a2b8; color: white; }
        .badge-secondary { background: #6c757d; color: white; }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucfirst($reportType) }} Audit Report</h1>
        <p>Judicial Service of Ghana - ICT Assets Management System</p>
        <p>Generated on {{ $generated_at->format('F j, Y \a\t g:i A') }}</p>
        @if(isset($filters['start_date']) && isset($filters['end_date']))
            <p>Period: {{ \Carbon\Carbon::parse($filters['start_date'])->format('M j, Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('M j, Y') }}</p>
        @endif
    </div>

    @if($reportType === 'assets')
    <!-- Assets Summary -->
    <div class="summary">
        <div class="summary-item">
            <h3>{{ $summary['total_assets'] }}</h3>
            <p>Total Assets</p>
        </div>
        @foreach($summary['by_status'] as $status => $count)
        <div class="summary-item">
            <h3>{{ $count }}</h3>
            <p>{{ ucfirst($status) }}</p>
        </div>
        @endforeach
    </div>

    <!-- Assets Table -->
    <table>
        <thead>
            <tr>
                <th>Asset Name</th>
                <th>Category</th>
                <th>Region</th>
                <th>Court</th>
                <th>Status</th>
                <th>Condition</th>
                <th>Assigned To</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $asset)
            <tr>
                <td>{{ $asset->asset_name }}</td>
                <td>{{ $asset->category->name ?? 'N/A' }}</td>
                <td>{{ $asset->region->name ?? 'N/A' }}</td>
                <td>{{ $asset->court->name ?? 'N/A' }}</td>
                <td>
                    <span class="badge badge-{{ $asset->status === 'available' ? 'success' : ($asset->status === 'assigned' ? 'primary' : 'warning') }}">
                        {{ ucfirst($asset->status) }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ $asset->condition === 'excellent' ? 'success' : ($asset->condition === 'good' ? 'info' : ($asset->condition === 'fair' ? 'warning' : 'danger')) }}">
                        {{ ucfirst($asset->condition) }}
                    </span>
                </td>
                <td>
                    @if($asset->assigned_type === 'user' && $asset->assignedUser)
                        {{ $asset->assignedUser->name }}
                    @elseif($asset->office)
                        {{ $asset->office->name }}
                    @elseif($asset->court)
                        {{ $asset->court->name }}
                    @else
                        Not Assigned
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($reportType === 'dts')
    <!-- DTS Summary -->
    <div class="summary">
        <div class="summary-item">
            <h3>{{ $summary['total_dts'] }}</h3>
            <p>Total DTS Systems</p>
        </div>
        <div class="summary-item">
            <h3>{{ $summary['available_dts'] }}</h3>
            <p>Available</p>
        </div>
        <div class="summary-item">
            <h3>{{ $summary['complete_systems'] }}</h3>
            <p>Complete Systems</p>
        </div>
        <div class="summary-item">
            <h3>{{ $summary['by_court']->count() }}</h3>
            <p>Courts</p>
        </div>
    </div>

    <!-- DTS Table -->
    <table>
        <thead>
            <tr>
                <th>DTS Name</th>
                <th>Court</th>
                <th>Region</th>
                <th>Monitors</th>
                <th>Splitters</th>
                <th>HDMI</th>
                <th>Ext Boards</th>
                <th>Trucking</th>
                <th>Recorders</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dtsSystems as $dts)
            <tr>
                <td><strong>{{ $dts->name }}</strong></td>
                <td>{{ $dts->court->name }}</td>
                <td>{{ $dts->court->region->name ?? 'N/A' }}</td>
                <td>{{ $dts->monitors_count }}</td>
                <td>{{ $dts->splitters_count }}</td>
                <td>{{ $dts->hdmi_short_cables_count }}(5M) & {{ $dts->hdmi_long_cables_count }}(20M)</td>
                <td>{{ $dts->extension_boards_count }}</td>
                <td>{{ $dts->trucking_count }}</td>
                <td>{{ $dts->sony_recorders_count }}</td>
                <td>
                    <span class="badge badge-{{ $dts->is_available ? 'success' : 'secondary' }}">
                        {{ $dts->is_available ? 'Available' : 'Unavailable' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>This report was generated by the ICT Assets Management System on behalf of the Judicial Service of Ghana.</p>
        <p>For any queries, please contact the ICT Department.</p>
    </div>
</body>
</html>
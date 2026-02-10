<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            background: #009966;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            opacity: 0.9;
        }

        .meta-info {
            background: #f9fafb;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        .meta-info p {
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: #f3f4f6;
            color: #1f2937;
            font-weight: bold;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #d1d5db;
            font-size: 9px;
        }

        td {
            padding: 6px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-primary { background: #e0f2fe; color: #075985; }
        .badge-dark { background: #e5e7eb; color: #1f2937; }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Sistem Informasi dan Komunikasi Mangrove DKI Jakarta (SIKOMANG)</p>
    </div>

    <div class="meta-info">
        <p><strong>Tanggal Export:</strong> {{ $generated_at }}</p>
        <p><strong>Total Laporan:</strong> {{ $total }} laporan</p>
        <p><strong>Dicetak oleh:</strong> {{ auth()->user()->name ?? 'Administrator' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">No. Laporan</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 15%;">Lokasi</th>
                <th style="width: 10%;">Jenis</th>
                <th style="width: 8%;">Urgensi</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 15%;">Pelapor</th>
                <th style="width: 15%;">Kontak</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $index => $report)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $report->report_number }}</strong></td>
                <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    {{ $report->location->name ?? 'N/A' }}
                    @if($report->location->region)
                        <br><small style="color: #6b7280;">{{ $report->location->region }}</small>
                    @endif
                </td>
                <td>
                    <span class="badge badge-{{ ['kerusakan' => 'danger', 'pencemaran' => 'warning', 'penebangan_liar' => 'dark', 'kondisi_baik' => 'success', 'lainnya' => 'info'][$report->report_type] ?? 'info' }}">
                        {{ $report->report_type_label }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ ['rendah' => 'info', 'sedang' => 'warning', 'tinggi' => 'danger', 'darurat' => 'dark'][$report->urgency_level] ?? 'info' }}">
                        {{ $report->urgency_label }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ $report->status_color }}">
                        {{ $report->status_label }}
                    </span>
                </td>
                <td>{{ $report->reporter_name }}</td>
                <td>
                    {{ $report->reporter_email }}<br>
                    {{ $report->reporter_phone }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh SIKOMANG</p>
        <p>© {{ date('Y') }} Dinas Lingkungan Hidup DKI Jakarta</p>
    </div>
</body>
</html>

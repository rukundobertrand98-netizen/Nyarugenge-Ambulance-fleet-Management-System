<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nyarugenge Ambulance Weekly Report</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #1f2937;
            background: #f8fafc;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            background: #f8fafc;
            color: #1f2937;
        }
        .report-shell {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            padding: 32px;
        }
        .hero {
            display: grid;
            gap: 16px;
            grid-template-columns: 1.3fr 0.7fr;
            align-items: end;
            margin-bottom: 32px;
        }
        .hero h1 {
            margin: 0;
            font-size: 2.25rem;
            line-height: 1.1;
            color: #0f172a;
        }
        .hero p {
            margin: 12px 0 0;
            color: #475569;
            font-size: 1rem;
            line-height: 1.75;
        }
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #ecfeff;
            border: 1px solid #67e8f9;
            color: #0f766e;
            padding: 10px 14px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin: 48px 0 18px;
        }
        .section-title h2 {
            margin: 0;
            font-size: 1.25rem;
            letter-spacing: -0.02em;
            color: #111827;
        }
        .section-title small {
            color: #64748b;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }
        .metric-card {
            border-radius: 20px;
            padding: 20px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid #e2e8f0;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        }
        .metric-card strong {
            display: block;
            margin-top: 12px;
            font-size: 2rem;
            color: #0f172a;
        }
        .metric-card span {
            font-size: 0.95rem;
            color: #475569;
        }
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
        }
        .status-pill-card {
            padding: 18px;
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
        }
        .status-pill-card[data-status="pending"] { background: #fffbeb; border-color: #fcd34d; }
        .status-pill-card[data-status="assigned"] { background: #eff6ff; border-color: #3b82f6; }
        .status-pill-card[data-status="on_route"] { background: #eef2ff; border-color: #6366f1; }
        .status-pill-card[data-status="completed"] { background: #ecfdf5; border-color: #22c55e; }
        .status-pill-card[data-status="cancelled"] { background: #fef2f2; border-color: #ef4444; }
        .status-pill-card h3 {
            margin: 0;
            font-size: 1rem;
            color: #334155;
        }
        .status-pill-card strong {
            display: block;
            margin-top: 10px;
            font-size: 1.85rem;
        }
        .table-wrapper {
            overflow-x: auto;
            margin-top: 16px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 860px;
        }
        th, td {
            text-align: left;
            padding: 14px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.95rem;
            vertical-align: top;
        }
        thead th {
            color: #0f172a;
            font-weight: 700;
            background: #f8fafc;
        }
        tbody tr:hover {
            background: #f8fafc;
        }
        .small-label {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 0.82rem;
            letter-spacing: 0.01em;
            font-weight: 700;
        }
        .status-chip {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #1e293b;
            text-transform: capitalize;
        }
        .status-chip.pending { background: #fef3c7; color: #92400e; }
        .status-chip.assigned { background: #dbeafe; color: #1e40af; }
        .status-chip.on_route { background: #e0e7ff; color: #4338ca; }
        .status-chip.completed { background: #d1fae5; color: #166534; }
        .status-chip.cancelled { background: #fee2e2; color: #b91c1c; }
        .fleet-card {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-top: 18px;
        }
        .fleet-card .card {
            padding: 18px;
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
        }
        .fleet-card .card p {
            margin: 10px 0 0;
            color: #475569;
        }
        .footer-note {
            margin-top: 40px;
            padding: 18px 20px;
            border-radius: 18px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #334155;
            font-size: 0.95rem;
            line-height: 1.7;
        }
    </style>
</head>
<body>
    <div class="report-shell">
        <div class="hero">
            <div>
                <span class="badge-pill">Weekly Operational Summary</span>
                <h1>Nyarugenge Ambulance Service</h1>
                <p>Seven-day operational review with clear mission metrics, ambulance status, and detailed request activity for better planning and faster decision-making.</p>
            </div>
            <div style="text-align:right;">
                <p style="margin:0; color:#475569; font-size:0.95rem;">Reporting period</p>
                <p style="margin:.35rem 0 0; font-size:1.1rem; font-weight:700; color:#0f172a;">{{ $startOfWeek->format('M d, Y') }} – {{ $endOfWeek->format('M d, Y') }}</p>
                <p style="margin:.75rem 0 0; color:#64748b;">Generated: {{ $generatedAt->format('F j, Y \a\t H:i') }}</p>
            </div>
        </div>

        <div class="section-title">
            <h2>Weekly Highlights</h2>
            <small>At a glance summary for this week&apos;s activity</small>
        </div>
        <div class="summary-grid">
            <div class="metric-card">
                <span>Total emergency requests</span>
                <strong>{{ $weeklyTotals['requests'] }}</strong>
            </div>
            <div class="metric-card">
                <span>Completed missions</span>
                <strong>{{ $weeklyTotals['completed'] }}</strong>
            </div>
            <div class="metric-card">
                <span>Pending requests</span>
                <strong>{{ $weeklyTotals['pending'] }}</strong>
            </div>
            <div class="metric-card">
                <span>Assigned / On-route</span>
                <strong>{{ $weeklyTotals['assigned'] + $weeklyTotals['on_route'] }}</strong>
            </div>
        </div>

        <div class="section-title" style="margin-top:40px;">
            <h2>Request Status Breakdown</h2>
            <small>How calls progressed across the week</small>
        </div>
        <div class="status-grid">
            @foreach(['pending','assigned','on_route','completed','cancelled'] as $status)
                <div class="status-pill-card" data-status="{{ $status }}">
                    <h3>{{ str_replace('_', ' ', ucfirst($status)) }}</h3>
                    <strong>{{ $weeklyTotals[$status] ?? 0 }}</strong>
                </div>
            @endforeach
        </div>

        <div class="section-title" style="margin-top:40px;">
            <h2>Ambulance Fleet Status</h2>
            <small>Active fleet condition and deployment snapshot</small>
        </div>
        <div class="fleet-card">
            @foreach($weeklyAmbulanceStatuses as $status => $count)
                <div class="card">
                    <p style="margin:0 0 10px; font-weight:700; color:#0f172a; text-transform:capitalize;">{{ str_replace('_', ' ', $status) }}</p>
                    <p style="margin:0; font-size:2.1rem; color:#0f172a;">{{ $count }}</p>
                </div>
            @endforeach
        </div>

        <div class="section-title" style="margin-top:40px;">
            <h2>Staff & User Counts</h2>
            <small>Headcount by role in the system</small>
        </div>
        <div class="summary-grid">
            @foreach($weeklyUserCounts as $role => $count)
                <div class="metric-card">
                    <span>{{ ucfirst($role) }}</span>
                    <strong>{{ $count }}</strong>
                </div>
            @endforeach
        </div>

        <div class="section-title" style="margin-top:40px;">
            <h2>Emergency Request Details</h2>
            <small>Complete request log for the last 7 days</small>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Pickup</th>
                        <th>Ambulance</th>
                        <th>Dispatcher</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($weeklyRequests as $request)
                        <tr>
                            <td>{{ $request->id }}</td>
                            <td>{{ $request->patient->name ?? 'Patient #'.$request->patient_id }}</td>
                            <td>{{ $request->pickup_location }}</td>
                            <td>{{ $request->ambulance->ambulance_number ?? 'Unassigned' }}</td>
                            <td>{{ $request->dispatcher->name ?? 'Not assigned' }}</td>
                            <td><span class="status-chip {{ str_replace(' ', '_', strtolower($request->status)) }}">{{ str_replace('_', ' ', ucfirst($request->status)) }}</span></td>
                            <td>{{ optional($request->created_at)->format('M d, H:i') }}</td>
                            <td>{{ optional($request->updated_at)->format('M d, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:24px; text-align:center; color:#64748b;">No emergency requests were logged during this week.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="section-title" style="margin-top:40px;">
            <h2>Fleet Summary</h2>
            <small>Ambulance readiness and assigned driver overview</small>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Unit</th>
                        <th>Plate</th>
                        <th>Driver</th>
                        <th>Status</th>
                        <th>Fuel</th>
                        <th>Battery</th>
                        <th>Condition</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ambulances as $ambulance)
                        <tr>
                            <td>{{ $ambulance->ambulance_number }}</td>
                            <td>{{ $ambulance->plate_number }}</td>
                            <td>{{ $ambulance->driver->name ?? 'Unassigned' }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($ambulance->status)) }}</td>
                            <td>{{ $ambulance->fuel_level ?? 'N/A' }}%</td>
                            <td>{{ $ambulance->battery_level ?? 'N/A' }}%</td>
                            <td>{{ $ambulance->overall_condition ?? 'Unknown' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer-note">
            This document is the official weekly operational report for the Nyarugenge Ambulance System. It summarizes mission activity, fleet readiness, and staff allocation to help leadership review performance and coordinate upcoming dispatch planning.
        </div>
    </div>
</body>
</html>

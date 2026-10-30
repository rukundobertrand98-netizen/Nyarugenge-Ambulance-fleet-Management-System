<x-layouts.app>

@php
    $dispatcher = auth()->user();
    $statusBadge = [
        'pending'               => 'badge-yellow',
        'assigned'              => 'badge-blue',
        'on_route'              => 'badge-indigo',
        'completed'             => 'badge-green',
        'cancelled'             => 'badge-red',
        'available'             => 'badge-green',
        'busy'                  => 'badge-blue',
        'maintenance'           => 'badge-yellow',
        'technical_control'     => 'badge-indigo',
        'scheduled_maintenance' => 'badge-purple',
        'out_of_service'        => 'badge-red',
    ];
@endphp

<style>
/* ── Single-column dispatcher layout ── */
.dsp-shell {
    display: flex;
    min-height: 100vh;
    background: #f1f5f9;
    font-family: 'Inter', sans-serif;
}

/* ── Sidebar ── */
.dsp-sidebar {
    width: 230px;
    min-width: 230px;
    background: #0f172a;
    display: flex;
    flex-direction: column;
    padding: 0;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
}
.dsp-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 22px 20px 18px;
    border-bottom: 1px solid #1e293b;
}
.dsp-brand__icon {
    width: 40px; height: 40px;
    background: linear-gradient(135deg,#dc2626,#991b1b);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 18px; flex-shrink: 0;
}
.dsp-brand strong { color: #f8fafc; font-size: 14px; display: block; }
.dsp-brand small  { color: #94a3b8; font-size: 11px; }
</style>

<style>
.dsp-nav { padding: 16px 12px; flex: 1; display: flex; flex-direction: column; gap: 4px; }
.dsp-nav a {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 12px; border-radius: 8px;
    color: #94a3b8; font-size: 13px; font-weight: 500;
    text-decoration: none; transition: all .15s;
}
.dsp-nav a i { width: 16px; text-align: center; }
.dsp-nav a:hover, .dsp-nav a.active {
    background: #1e293b; color: #f8fafc;
}
.dsp-nav a.active { color: #f87171; }

.dsp-logout { padding: 16px 12px; border-top: 1px solid #1e293b; }
.dsp-logout button {
    width: 100%; padding: 9px 12px; border-radius: 8px;
    background: #7f1d1d; color: #fca5a5; border: none;
    font-size: 13px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    transition: background .15s;
}
.dsp-logout button:hover { background: #991b1b; }

/* ── Main content ── */
.dsp-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0;
    overflow-y: auto;
}

/* ── Top bar with dispatcher name ── */
.dsp-topbar {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    padding: 14px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 50;
}
.dsp-topbar__left { display: flex; align-items: center; gap: 12px; }
.dsp-topbar__avatar {
    width: 38px; height: 38px;
    background: linear-gradient(135deg,#dc2626,#991b1b);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 15px; font-weight: 700;
}
.dsp-topbar__name strong { font-size: 14px; color: #0f172a; display: block; }
.dsp-topbar__name small  { font-size: 11px; color: #64748b; }
.dsp-topbar__right { display: flex; align-items: center; gap: 10px; }
.live-dot {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; color: #16a34a; font-weight: 600;
}
.live-dot span {
    width: 8px; height: 8px; border-radius: 50%;
    background: #16a34a;
    animation: livePulse 1.4s infinite;
}
@keyframes livePulse {
    0%,100% { opacity: 1; transform: scale(1); }
    50%      { opacity: .5; transform: scale(1.4); }
}
</style>

<style>
/* ── Section wrapper ── */
.dsp-content { padding: 24px 28px; display: flex; flex-direction: column; gap: 28px; }

/* ── Section heading ── */
.dsp-section-title {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 14px;
}
.dsp-section-title .dsp-section-icon {
    width: 34px; height: 34px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; color: #fff; flex-shrink: 0;
}
.dsp-section-title h2 { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; }
.dsp-section-title small { font-size: 11px; color: #64748b; display: block; margin-top: 1px; }

/* ── Metric cards ── */
.dsp-metrics {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
}
.dsp-metric {
    background: #fff;
    border-radius: 12px;
    padding: 18px 20px;
    border: 1px solid #e2e8f0;
    display: flex; align-items: center; gap: 14px;
}
.dsp-metric__icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.dsp-metric__icon--red    { background: #fef2f2; color: #dc2626; }
.dsp-metric__icon--blue   { background: #eff6ff; color: #2563eb; }
.dsp-metric__icon--green  { background: #f0fdf4; color: #16a34a; }
.dsp-metric__icon--purple { background: #faf5ff; color: #7c3aed; }
.dsp-metric p  { font-size: 12px; color: #64748b; margin: 0 0 2px; }
.dsp-metric strong { font-size: 26px; font-weight: 700; color: #0f172a; }

/* ── Panel card ── */
.dsp-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}
.dsp-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.dsp-card-header h3 { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; }
.dsp-card-header small { font-size: 11px; color: #94a3b8; }
.dsp-card-body { padding: 0; }

/* ── Map ── */
.dsp-map {
    height: 480px;
    width: 100%;
    border-radius: 0 0 14px 14px;
}
.dsp-map-toolbar {
    padding: 10px 16px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
}
.dsp-map-legend {
    display: flex; align-items: center; gap: 16px;
    font-size: 12px; color: #475569;
}
.dsp-map-legend span { display: flex; align-items: center; gap: 5px; }
.legend-dot {
    width: 10px; height: 10px; border-radius: 50%; display: inline-block;
}
</style>

<style>
/* ── Table ── */
.dsp-table-wrap { overflow-x: auto; }
.dsp-table {
    width: 100%; border-collapse: collapse; font-size: 13px;
}
.dsp-table th {
    background: #f8fafc; padding: 10px 14px;
    text-align: left; font-size: 11px; font-weight: 700;
    color: #64748b; text-transform: uppercase; letter-spacing: .04em;
    border-bottom: 1px solid #e2e8f0;
}
.dsp-table td {
    padding: 12px 14px; border-bottom: 1px solid #f1f5f9;
    vertical-align: top;
}
.dsp-table tr:last-child td { border-bottom: none; }
.dsp-table td strong { display: block; color: #0f172a; font-weight: 600; }
.dsp-table td small  { display: block; color: #64748b; font-size: 11px; margin-top: 2px; }
.dsp-table .empty-row td {
    text-align: center; color: #94a3b8; padding: 28px; font-size: 13px;
}

/* ── Mission cards ── */
.dsp-mission-list { display: flex; flex-direction: column; gap: 0; }
.dsp-mission-card {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-bottom: 1px solid #f1f5f9;
    gap: 12px; flex-wrap: wrap;
}
.dsp-mission-card:last-child { border-bottom: none; }
.dsp-mission-card__info strong { font-size: 14px; color: #0f172a; font-weight: 600; }
.dsp-mission-card__info small  { font-size: 12px; color: #64748b; display: block; margin-top: 2px; }
.dsp-mission-card__actions { display: flex; gap: 8px; flex-wrap: wrap; }

/* ── Fleet cards ── */
.dsp-fleet-list { display: flex; flex-direction: column; gap: 0; }
.dsp-fleet-card {
    padding: 14px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex; flex-direction: column; gap: 8px;
}
.dsp-fleet-card:last-child { border-bottom: none; }
.dsp-fleet-card__top {
    display: flex; align-items: center;
    justify-content: space-between; gap: 10px;
}
.dsp-fleet-card__top strong { font-size: 14px; color: #0f172a; font-weight: 600; }
.dsp-fleet-card__top small  { font-size: 12px; color: #64748b; display: block; }
.dsp-fleet-stats {
    display: grid; grid-template-columns: repeat(3,1fr); gap: 8px;
}
.dsp-fleet-stat {
    background: #f8fafc; border-radius: 6px; padding: 6px 10px; font-size: 12px;
}
.dsp-fleet-stat span  { color: #64748b; display: block; }
.dsp-fleet-stat strong { color: #0f172a; }
.dsp-alert-tag {
    border-radius: 6px; padding: 5px 10px; font-size: 12px; font-weight: 500;
}
.dsp-alert-tag--yellow { background:#fef3c7; color:#92400e; }
.dsp-alert-tag--purple { background:#ede9fe; color:#5b21b6; }
.dsp-alert-tag--green  { background:#f0fdf4; color:#166534; }

/* ── Inline form ── */
.dsp-inline-form { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
.dsp-inline-form select, .dsp-inline-form input {
    padding: 6px 10px; border: 1.5px solid #d1d5db; border-radius: 6px;
    font-size: 12px; color: #374151; outline: none;
}
.dsp-inline-form select:focus, .dsp-inline-form input:focus { border-color: #dc2626; }

/* ── Alerts ── */
.dsp-alert {
    margin: 0 28px;
    padding: 12px 16px; border-radius: 10px;
    display: flex; align-items: flex-start; gap: 10px; font-size: 13px;
}
.dsp-alert--success { background:#f0fdf4; border:1px solid #86efac; color:#166534; }
.dsp-alert--error   { background:#fef2f2; border:1px solid #fca5a5; color:#dc2626; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .dsp-sidebar { display: none; }
    .dsp-metrics { grid-template-columns: repeat(2,1fr); }
    .dsp-content { padding: 16px; }
}
@media (max-width: 560px) {
    .dsp-metrics { grid-template-columns: 1fr 1fr; }
}
</style>

{{-- ═══════════════════════════════════════════════════════
     SHELL
═══════════════════════════════════════════════════════ --}}
<div class="dsp-shell">

    {{-- ── Sidebar ── --}}
    <aside class="dsp-sidebar">
        <div class="dsp-brand">
            <div class="dsp-brand__icon"><i class="fa-solid fa-headset"></i></div>
            <div>
                <strong>Dispatch Center</strong>
                <small>Nyarugenge EMS</small>
            </div>
        </div>

        <nav class="dsp-nav">
            <a href="#overview"  class="active"><i class="fa-solid fa-chart-line"></i> Overview</a>
            <a href="#requests">  <i class="fa-solid fa-bell-medical"></i> Patient Requests</a>
            <a href="#active">    <i class="fa-solid fa-route"></i> Active Missions</a>
            <a href="#fleet">     <i class="fa-solid fa-truck-medical"></i> Ambulances</a>
            <a href="#tracking">  <i class="fa-solid fa-location-crosshairs"></i> Live Tracking</a>
            <a href="#history">   <i class="fa-solid fa-clock-rotate-left"></i> History</a>
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="dsp-logout">
            @csrf
            <button type="submit">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </form>
    </aside>

    {{-- ── Main ── --}}
    <div class="dsp-main">

        {{-- ── Top bar with dispatcher name ── --}}
        <div class="dsp-topbar">
            <div class="dsp-topbar__left">
                <div class="dsp-topbar__avatar">
                    {{ strtoupper(substr($dispatcher->name, 0, 1)) }}
                </div>
                <div class="dsp-topbar__name">
                    <strong>{{ $dispatcher->name }}</strong>
                    <small>Dispatcher · {{ $dispatcher->email }}</small>
                </div>
            </div>
            <div class="dsp-topbar__right">
                <div class="live-dot"><span></span> Live tracking active</div>
            </div>
        </div>

        {{-- ── Alerts ── --}}
        @if (session('success'))
            <div class="dsp-alert dsp-alert--success" style="margin-top:16px;">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="dsp-alert dsp-alert--error" style="margin-top:16px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>@foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
            </div>
        @endif

        {{-- ════════════════════════════════════════
             CONTENT — single column flow
        ════════════════════════════════════════ --}}
        <div class="dsp-content">

            {{-- ── 1. OVERVIEW METRICS ── --}}
            <div id="overview">
                <div class="dsp-section-title">
                    <div class="dsp-section-icon" style="background:#1e293b;">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div>
                        <h2>Dispatcher Dashboard</h2>
                        <small>Emergency operations overview</small>
                    </div>
                </div>

                <div class="dsp-metrics">
                    <div class="dsp-metric">
                        <div class="dsp-metric__icon dsp-metric__icon--red">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <div>
                            <p>Pending Requests</p>
                            <strong>{{ $stats['newRequestsCount'] }}</strong>
                        </div>
                    </div>
                    <div class="dsp-metric">
                        <div class="dsp-metric__icon dsp-metric__icon--blue">
                            <i class="fa-solid fa-route"></i>
                        </div>
                        <div>
                            <p>Active Missions</p>
                            <strong>{{ $stats['activeTripsCount'] }}</strong>
                        </div>
                    </div>
                    <div class="dsp-metric">
                        <div class="dsp-metric__icon dsp-metric__icon--green">
                            <i class="fa-solid fa-truck-medical"></i>
                        </div>
                        <div>
                            <p>Available Ambulances</p>
                            <strong>{{ $stats['availableAmbulances'] }}</strong>
                        </div>
                    </div>
                    <div class="dsp-metric">
                        <div class="dsp-metric__icon dsp-metric__icon--purple">
                            <i class="fa-solid fa-flag-checkered"></i>
                        </div>
                        <div>
                            <p>Completed Today</p>
                            <strong>{{ $stats['completedToday'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── 2. PATIENT EMERGENCY REQUESTS ── --}}
            <div id="requests">
                <div class="dsp-section-title">
                    <div class="dsp-section-icon" style="background:#dc2626;">
                        <i class="fa-solid fa-bell-medical"></i>
                    </div>
                    <div>
                        <h2>Patient Emergency Requests</h2>
                        <small>Approval queue — assign only available ambulances with drivers</small>
                    </div>
                </div>

                <div class="dsp-card">
                    <div class="dsp-table-wrap">
                        <table class="dsp-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Pickup & Location</th>
                                    <th>Emergency Type</th>
                                    <th>Approve & Assign</th>
                                    <th>Reject</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($newRequests as $req)
                                    <tr>
                                        <td>
                                            <strong>{{ $req->patient->name ?? 'Patient #'.$req->patient_id }}</strong>
                                            <small><i class="fa-solid fa-phone" style="color:#6b7280;"></i>
                                                {{ $req->patient_phone ?? $req->patient->phone ?? 'No phone' }}
                                            </small>
                                            @if($req->additional_notes)
                                                <small><i class="fa-solid fa-note-sticky" style="color:#6b7280;"></i>
                                                    {{ $req->additional_notes }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $req->pickup_location }}</strong>
                                            @if($req->pickup_lat && $req->pickup_lng)
                                                <small>
                                                    <i class="fa-solid fa-location-dot" style="color:#dc2626;"></i>
                                                    {{ number_format($req->pickup_lat,6) }},
                                                    {{ number_format($req->pickup_lng,6) }}
                                                </small>
                                                <small>
                                                    <a href="https://www.google.com/maps?q={{ $req->pickup_lat }},{{ $req->pickup_lng }}"
                                                       target="_blank" style="color:#2563eb;">
                                                        <i class="fa-solid fa-map"></i> View on map
                                                    </a>
                                                </small>
                                            @endif
                                            <small style="color:#9ca3af;">{{ $req->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>{{ $req->emergency_type ?? 'Medical emergency' }}</td>
                                        <td>
                                            <form method="POST"
                                                  action="{{ route('dispatcher.requests.assign', $req) }}"
                                                  class="dsp-inline-form">
                                                @csrf
                                                <select name="ambulance_id" required>
                                                    <option value="">Choose ambulance</option>
                                                    @foreach($availableAmbulances as $amb)
                                                        <option value="{{ $amb->id }}">
                                                            {{ $amb->ambulance_number }} – {{ $amb->driver->name ?? 'No driver' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-green compact-btn">
                                                    <i class="fa-solid fa-check"></i> Approve
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <form method="POST"
                                                  action="{{ route('dispatcher.requests.reject', $req) }}"
                                                  class="dsp-inline-form">
                                                @csrf
                                                <input name="reason" placeholder="Reason (optional)">
                                                <button type="submit" class="btn btn-red compact-btn">
                                                    <i class="fa-solid fa-xmark"></i> Reject
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row"><td colspan="5">No pending patient requests.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── 3. ACTIVE MISSIONS ── --}}
            <div id="active">
                <div class="dsp-section-title">
                    <div class="dsp-section-icon" style="background:#2563eb;">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <div>
                        <h2>Active Missions</h2>
                        <small>Assigned and on-route ambulances</small>
                    </div>
                </div>

                <div class="dsp-card">
                    <div class="dsp-mission-list">
                        @forelse($activeRequests as $req)
                            <div class="dsp-mission-card">
                                <div class="dsp-mission-card__info">
                                    <strong>{{ $req->pickup_location }}</strong>
                                    <small>
                                        <i class="fa-solid fa-truck-medical" style="color:#6b7280;"></i>
                                        {{ $req->ambulance->ambulance_number ?? 'No ambulance' }}
                                        &nbsp;·&nbsp;
                                        <i class="fa-solid fa-user" style="color:#6b7280;"></i>
                                        {{ $req->ambulance->driver->name ?? 'No driver' }}
                                    </small>
                                    <small>
                                        <i class="fa-solid fa-user-injured" style="color:#6b7280;"></i>
                                        {{ $req->patient->name ?? 'Patient #'.$req->patient_id }}
                                        &nbsp;·&nbsp;
                                        <i class="fa-solid fa-phone" style="color:#6b7280;"></i>
                                        {{ $req->patient_phone ?? $req->patient->phone ?? 'No phone' }}
                                    </small>
                                </div>

                                <span class="badge {{ $statusBadge[$req->status] ?? 'badge-blue' }}">
                                    {{ str_replace('_',' ',ucfirst($req->status)) }}
                                </span>

                                <div class="dsp-mission-card__actions">
                                    @if($req->status === 'assigned')
                                        <form method="POST" action="{{ route('dispatcher.requests.on-route', $req) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-blue compact-btn">
                                                <i class="fa-solid fa-truck-fast"></i> On Route
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('dispatcher.requests.complete', $req) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-green compact-btn">
                                            <i class="fa-solid fa-flag-checkered"></i> Complete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">
                                No active missions right now.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ── 4. AMBULANCE VEHICLE STATUS ── --}}
            <div id="fleet">
                <div class="dsp-section-title">
                    <div class="dsp-section-icon" style="background:#d97706;">
                        <i class="fa-solid fa-truck-medical"></i>
                    </div>
                    <div>
                        <h2>Ambulance Fleet Status</h2>
                        <small>Driver-reported vehicle health and maintenance</small>
                    </div>
                </div>

                <div class="dsp-card">
                    <div class="dsp-fleet-list">
                        @forelse($ambulances as $amb)
                            <div class="dsp-fleet-card">
                                <div class="dsp-fleet-card__top">
                                    <div>
                                        <strong>{{ $amb->ambulance_number }} · {{ $amb->plate_number }}</strong>
                                        <small>
                                            <i class="fa-solid fa-user" style="color:#6b7280;"></i>
                                            {{ $amb->driver->name ?? 'No driver assigned' }}
                                        </small>
                                    </div>
                                    <span class="badge {{ $statusBadge[$amb->status] ?? 'badge-blue' }}">
                                        {{ str_replace('_',' ',ucfirst($amb->status)) }}
                                    </span>
                                </div>

                                <div class="dsp-fleet-stats">
                                    <div class="dsp-fleet-stat">
                                        <span>Fuel</span>
                                        <strong>{{ $amb->fuel_level }}%</strong>
                                    </div>
                                    <div class="dsp-fleet-stat">
                                        <span>Battery</span>
                                        <strong>{{ $amb->battery_level }}%</strong>
                                    </div>
                                    <div class="dsp-fleet-stat">
                                        <span>Condition</span>
                                        <strong>{{ $amb->overall_condition }}</strong>
                                    </div>
                                </div>

                                @if($amb->maintenance_issue)
                                    <div class="dsp-alert-tag dsp-alert-tag--yellow">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        Issue: {{ $amb->maintenance_issue }}
                                    </div>
                                @endif
                                @if($amb->technical_control_until)
                                    <div class="dsp-alert-tag dsp-alert-tag--purple">
                                        <i class="fa-solid fa-clipboard-check"></i>
                                        Technical Control until: {{ $amb->technical_control_until->format('M d, Y H:i') }}
                                    </div>
                                @endif
                                @if($amb->next_service_due_at)
                                    <div class="dsp-alert-tag dsp-alert-tag--green">
                                        <i class="fa-solid fa-calendar-check"></i>
                                        Next Service: {{ $amb->next_service_due_at->format('M d, Y') }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">
                                No ambulances registered.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ── 5. LIVE TRACKING MAP ── --}}
            <div id="tracking">
                <div class="dsp-section-title">
                    <div class="dsp-section-icon" style="background:#16a34a;">
                        <i class="fa-solid fa-location-crosshairs"></i>
                    </div>
                    <div>
                        <h2>Live Tracking</h2>
                        <small id="mapLastUpdated">Refreshes every 5 seconds</small>
                    </div>
                </div>

                <div class="dsp-card">
                    <div class="dsp-card-header">
                        <h3>Ambulance & Patient Positions</h3>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div class="dsp-map-legend">
                                <span><span class="legend-dot" style="background:#2563eb;"></span> Ambulance</span>
                                <span><span class="legend-dot" style="background:#dc2626;"></span> Patient pickup</span>
                            </div>
                            <div class="live-dot"><span></span> Auto-refresh</div>
                        </div>
                    </div>
                    <div class="dsp-map-toolbar">
                        <button class="btn btn-blue compact-btn" id="centerMapBtn">
                            <i class="fa-solid fa-crosshairs"></i> Center map
                        </button>
                        <span id="mapAmbCount" style="font-size:12px;color:#475569;"></span>
                        <span id="mapReqCount" style="font-size:12px;color:#475569;"></span>
                    </div>
                    <div id="dispatcherMap" class="dsp-map"></div>
                </div>
            </div>

            {{-- ── 6. HISTORY ── --}}
            <div id="history">
                <div class="dsp-section-title">
                    <div class="dsp-section-icon" style="background:#475569;">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <h2>Recently Closed Requests</h2>
                        <small>Completed and cancelled cases</small>
                    </div>
                </div>

                <div class="dsp-card">
                    <div class="dsp-table-wrap">
                        <table class="dsp-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Pickup</th>
                                    <th>Ambulance</th>
                                    <th>Status</th>
                                    <th>Closed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedRequests as $req)
                                    <tr>
                                        <td>
                                            <strong>{{ $req->patient->name ?? 'Patient #'.$req->patient_id }}</strong>
                                            <small>{{ $req->patient_phone ?? $req->patient->phone ?? '' }}</small>
                                        </td>
                                        <td>{{ $req->pickup_location }}</td>
                                        <td>{{ $req->ambulance->ambulance_number ?? 'Not assigned' }}</td>
                                        <td>
                                            <span class="badge {{ $statusBadge[$req->status] ?? 'badge-blue' }}">
                                                {{ ucfirst($req->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $req->updated_at->format('M d, H:i') }}</strong>
                                            <small>{{ $req->updated_at->diffForHumans() }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row"><td colspan="5">No closed requests yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>{{-- end .dsp-content --}}
    </div>{{-- end .dsp-main --}}
</div>{{-- end .dsp-shell --}}

<script>
const DSP_ROUTES = {
    liveTracking: "{{ route('dispatcher.api.live-tracking') }}",
};

/* ── Map state ── */
let dspMap;
const ambulanceMarkers = {};   // keyed by ambulance id — move smoothly
const requestMarkers   = {};   // keyed by request id

/* ── Custom icons ── */
function makeAmbIcon(status) {
    const color = {
        available: '#16a34a', busy: '#2563eb',
        maintenance: '#d97706', technical_control: '#7c3aed',
        scheduled_maintenance: '#7c3aed', out_of_service: '#dc2626',
    }[status] || '#2563eb';

    return L.divIcon({
        className: '',
        html: `<div style="
            width:34px;height:34px;border-radius:50%;
            background:${color};border:3px solid #fff;
            box-shadow:0 2px 8px rgba(0,0,0,.35);
            display:flex;align-items:center;justify-content:center;
            color:#fff;font-size:14px;">
            <i class='fa-solid fa-truck-medical'></i>
        </div>`,
        iconSize: [34, 34],
        iconAnchor: [17, 17],
        popupAnchor: [0, -18],
    });
}

const patientIcon = L.divIcon({
    className: '',
    html: `<div style="
        width:30px;height:30px;border-radius:50%;
        background:#dc2626;border:3px solid #fff;
        box-shadow:0 2px 8px rgba(0,0,0,.35);
        display:flex;align-items:center;justify-content:center;
        color:#fff;font-size:13px;">
        <i class='fa-solid fa-user-injured'></i>
    </div>`,
    iconSize: [30, 30],
    iconAnchor: [15, 15],
    popupAnchor: [0, -16],
});

/* ── Boot map ── */
function bootMap() {
    dspMap = L.map('dispatcherMap', { zoomControl: true })
              .setView([-1.9441, 30.0619], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a>',
    }).addTo(dspMap);
}

/* ── Smooth marker move ── */
function moveMarker(marker, newLat, newLng) {
    const start = marker.getLatLng();
    const end   = L.latLng(newLat, newLng);
    if (start.distanceTo(end) < 0.5) return; // no change
    marker.setLatLng(end);
}

/* ── Refresh map data ── */
async function refreshMap() {
    let data;
    try {
        const res = await fetch(DSP_ROUTES.liveTracking, {
            headers: { Accept: 'application/json' },
            cache: 'no-store',
        });
        data = await res.json();
    } catch (e) {
        console.warn('Live tracking fetch failed:', e);
        return;
    }

    const now = new Date().toLocaleTimeString();
    document.getElementById('mapLastUpdated').textContent = `Last updated: ${now}`;

    /* ── Ambulances ── */
    const seenAmb = new Set();
    (data.ambulances || []).forEach(amb => {
        const loc = amb.latest_location;
        const lat = parseFloat(loc?.latitude  ?? amb.current_lat);
        const lng = parseFloat(loc?.longitude ?? amb.current_lng);
        if (!lat || !lng) return;

        seenAmb.add(amb.id);

        const popup = `
            <div style="min-width:160px;font-size:13px;">
                <strong style="font-size:14px;">${amb.ambulance_number}</strong><br>
                <span style="color:#475569;">Driver:</span> ${amb.driver?.name ?? 'No driver'}<br>
                <span style="color:#475569;">Status:</span>
                <b>${amb.status.replace(/_/g,' ')}</b><br>
                <span style="color:#475569;">Fuel:</span> ${amb.fuel_level ?? '—'}%
                &nbsp;|&nbsp;
                <span style="color:#475569;">Battery:</span> ${amb.battery_level ?? '—'}%<br>
                <span style="color:#6b7280;font-size:11px;">${lat.toFixed(6)}, ${lng.toFixed(6)}</span>
            </div>`;

        if (ambulanceMarkers[amb.id]) {
            moveMarker(ambulanceMarkers[amb.id], lat, lng);
            ambulanceMarkers[amb.id].setIcon(makeAmbIcon(amb.status));
            ambulanceMarkers[amb.id].getPopup().setContent(popup);
        } else {
            ambulanceMarkers[amb.id] = L.marker([lat, lng], { icon: makeAmbIcon(amb.status) })
                .addTo(dspMap)
                .bindPopup(popup);
        }
    });

    // Remove ambulances no longer in response
    Object.keys(ambulanceMarkers).forEach(id => {
        if (!seenAmb.has(parseInt(id))) {
            ambulanceMarkers[id].remove();
            delete ambulanceMarkers[id];
        }
    });

    /* ── Patient pickup requests ── */
    const seenReq = new Set();
    (data.requests || []).forEach(req => {
        const lat = parseFloat(req.pickup_lat);
        const lng = parseFloat(req.pickup_lng);
        if (!lat || !lng) return;

        seenReq.add(req.id);

        const popup = `
            <div style="min-width:160px;font-size:13px;">
                <strong style="font-size:14px;">Patient Pickup #${req.id}</strong><br>
                <span style="color:#475569;">Patient:</span> ${req.patient?.name ?? '—'}<br>
                <span style="color:#475569;">Phone:</span> ${req.patient?.phone ?? '—'}<br>
                <span style="color:#475569;">Location:</span> ${req.pickup_location}<br>
                <span style="color:#475569;">Status:</span> <b>${req.status.replace(/_/g,' ')}</b><br>
                <span style="color:#6b7280;font-size:11px;">${lat.toFixed(6)}, ${lng.toFixed(6)}</span>
            </div>`;

        if (requestMarkers[req.id]) {
            moveMarker(requestMarkers[req.id], lat, lng);
            requestMarkers[req.id].getPopup().setContent(popup);
        } else {
            requestMarkers[req.id] = L.marker([lat, lng], { icon: patientIcon })
                .addTo(dspMap)
                .bindPopup(popup);
        }
    });

    // Remove requests no longer active
    Object.keys(requestMarkers).forEach(id => {
        if (!seenReq.has(parseInt(id))) {
            requestMarkers[id].remove();
            delete requestMarkers[id];
        }
    });

    /* ── Update counters ── */
    document.getElementById('mapAmbCount').textContent =
        `${seenAmb.size} ambulance${seenAmb.size !== 1 ? 's' : ''} on map`;
    document.getElementById('mapReqCount').textContent =
        `· ${seenReq.size} active request${seenReq.size !== 1 ? 's' : ''}`;
}

/* ── Center map on all markers ── */
function centerMap() {
    const all = [
        ...Object.values(ambulanceMarkers),
        ...Object.values(requestMarkers),
    ];
    if (all.length) {
        dspMap.fitBounds(L.featureGroup(all).getBounds().pad(0.25));
    }
}

/* ── Sidebar smooth scroll ── */
document.querySelectorAll('.dsp-nav a').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('.dsp-nav a').forEach(a => a.classList.remove('active'));
        link.classList.add('active');
        const target = document.querySelector(link.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

/* ── Boot ── */
document.addEventListener('DOMContentLoaded', () => {
    bootMap();
    refreshMap();

    // Auto-refresh every 5 seconds for live movement
    setInterval(refreshMap, 5000);

    document.getElementById('centerMapBtn').addEventListener('click', centerMap);
});
</script>

</x-layouts.app>

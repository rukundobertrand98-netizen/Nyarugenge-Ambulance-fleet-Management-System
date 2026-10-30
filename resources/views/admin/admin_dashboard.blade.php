<x-layouts.app>

@php
    $statusBadge = [
        'pending' => 'badge-yellow',
        'assigned' => 'badge-blue',
        'on_route' => 'badge-indigo',
        'completed' => 'badge-green',
        'cancelled' => 'badge-red',
        'available' => 'badge-green',
        'busy' => 'badge-blue',
        'maintenance' => 'badge-yellow',
        'technical_control' => 'badge-indigo',
        'scheduled_maintenance' => 'badge-purple',
        'out_of_service' => 'badge-red',
    ];

    $ambulanceStatusLabels = [
        'available' => 'Available',
        'busy' => 'On mission',
        'maintenance' => 'Maintenance',
        'technical_control' => 'Technical control',
        'scheduled_maintenance' => 'Scheduled maintenance',
        'out_of_service' => 'Out of service',
    ];

    $dispatcherAction = [
        'pending' => ['label' => 'Waiting for dispatcher', 'detail' => 'No action taken yet'],
        'assigned' => ['label' => 'Ambulance assigned', 'detail' => 'Dispatcher selected response unit'],
        'on_route' => ['label' => 'Ambulance dispatched', 'detail' => 'Crew is moving to pickup'],
        'completed' => ['label' => 'Request completed', 'detail' => 'Dispatcher closed the case'],
        'cancelled' => ['label' => 'Request cancelled', 'detail' => 'Dispatcher cancelled response'],
    ];
@endphp

<div class="admin-shell" data-admin-dashboard>
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <span class="admin-brand__mark"><i class="fa-solid fa-truck-medical"></i></span>
            <div>
                <strong>Nyarugenge EMS</strong>
                <small>Admin Command Center</small>
            </div>
        </div>

        <nav class="admin-nav" aria-label="Admin navigation">
            <a href="#overview" class="active"><i class="fa-solid fa-chart-line"></i><span>Overview</span></a>
            <a href="#requests"><i class="fa-solid fa-bell-medical"></i><span>Requests</span></a>
            <a href="#fleet"><i class="fa-solid fa-truck-medical"></i><span>Fleet</span></a>
            <a href="#tracking"><i class="fa-solid fa-location-crosshairs"></i><span>Live Tracking</span></a>
            <a href="#users"><i class="fa-solid fa-users-gear"></i><span>Users</span></a>
            <a href="#reports"><i class="fa-solid fa-file-export"></i><span>Reports</span></a>
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="admin-logout">
            @csrf
            <button type="submit" class="btn btn-red admin-logout__button">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>
    </aside>

    <main class="admin-main">
        <section class="admin-hero" id="overview">
            <div>
                <span class="eyebrow">Hospital ambulance operations</span>
                <h1>Admin Dashboard</h1>
                <p>Manage users, register ambulances, assign drivers, monitor fleet readiness, and view emergency request activity from one control center.</p>
            </div>

            <div class="admin-hero__actions">
                <button type="button" class="btn btn-blue" id="refreshDashboard">
                    <i class="fa-solid fa-rotate"></i>
                    <span>Refresh</span>
                </button>
                <button type="button" class="btn btn-green" id="exportDashboard">
                    <i class="fa-solid fa-file-csv"></i>
                    <span>Export CSV</span>
                </button>
                <span class="live-pill"><span></span>Live API</span>
            </div>
        </section>

        @if (session('success'))
            <div class="admin-alert admin-alert--success">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="admin-alert admin-alert--error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <section class="metric-grid" aria-label="System metrics">
            <article class="metric-card metric-card--urgent">
                <span class="metric-card__icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                <p>Pending Requests</p>
                <strong data-metric="pending_requests">{{ $stats['totals']['pending_requests'] }}</strong>
                <small>Dispatcher action required</small>
            </article>

            <article class="metric-card">
                <span class="metric-card__icon"><i class="fa-solid fa-route"></i></span>
                <p>Active Missions</p>
                <strong data-metric="active_requests">{{ $stats['totals']['active_requests'] }}</strong>
                <small>Assigned or on route</small>
            </article>

            <article class="metric-card">
                <span class="metric-card__icon"><i class="fa-solid fa-truck-medical"></i></span>
                <p>Available Ambulances</p>
                <strong data-metric="available_ambulances">{{ $stats['totals']['available_ambulances'] }}</strong>
                <small>Ready for dispatch</small>
            </article>

            <article class="metric-card">
                <span class="metric-card__icon"><i class="fa-solid fa-circle-check"></i></span>
                <p>Completed Today</p>
                <strong data-metric="completed_today">{{ $stats['totals']['completed_today'] }}</strong>
                <small>Closed emergency cases</small>
            </article>
        </section>

        <section class="admin-grid admin-grid--analytics">
            <article class="panel">
                <div class="panel-header">
                    <div>
                        <span class="eyebrow">7 day activity</span>
                        <h2>Emergency Request Trend</h2>
                    </div>
                </div>
                <div class="chart-box">
                    <canvas id="adminRequestsChart"></canvas>
                </div>
            </article>

            <article class="panel">
                <div class="panel-header">
                    <div>
                        <span class="eyebrow">Fleet readiness</span>
                        <h2>Ambulance Status</h2>
                    </div>
                </div>
                <div class="chart-box chart-box--compact">
                    <canvas id="adminFleetChart"></canvas>
                </div>
                <div class="status-breakdown">
                    <span><b data-status="available">{{ $stats['ambulance_statuses']['available'] }}</b> Available</span>
                    <span><b data-status="busy">{{ $stats['ambulance_statuses']['busy'] }}</b> On mission</span>
                    <span><b data-status="maintenance">{{ $stats['ambulance_statuses']['maintenance'] }}</b> Maintenance</span>
                    <span><b data-status="technical_control">{{ $stats['ambulance_statuses']['technical_control'] }}</b> Technical control</span>
                    <span><b data-status="scheduled_maintenance">{{ $stats['ambulance_statuses']['scheduled_maintenance'] }}</b> Scheduled</span>
                    <span><b data-status="out_of_service">{{ $stats['ambulance_statuses']['out_of_service'] }}</b> Out of service</span>
                </div>
            </article>
        </section>

        <section class="admin-grid admin-grid--single">
            <article class="panel" id="requests">
                <div class="panel-header">
                    <div>
                        <span class="eyebrow">Command queue</span>
                        <h2>Emergency Requests</h2>
                    </div>
                    <input class="table-search" type="search" placeholder="Search requests" data-table-search="requestsTable">
                </div>

                <div class="table-wrap">
                    <table class="admin-table" id="requestsTable">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Pickup & Location</th>
                                <th>Type</th>
                                <th>Ambulance</th>
                                <th>Dispatcher</th>
                                <th>Dispatcher Action</th>
                                <th>Status</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                @php($action = $dispatcherAction[$request->status] ?? ['label' => 'Status updated', 'detail' => 'Dispatcher activity recorded'])
                                <tr>
                                    <td>
                                        <strong>{{ $request->patient->name ?? 'Patient #' . $request->patient_id }}</strong>
                                        <small><i class="fa-solid fa-phone" style="color:#6b7280;"></i> {{ $request->patient_phone ?? $request->patient->phone ?? 'No phone' }}</small>
                                        @if($request->additional_notes)
                                            <small style="color:#374151;"><i class="fa-solid fa-note-sticky" style="color:#6b7280;"></i> {{ $request->additional_notes }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $request->pickup_location }}</strong>
                                        @if($request->pickup_lat && $request->pickup_lng)
                                            <small>
                                                <i class="fa-solid fa-location-dot" style="color:#dc2626;"></i>
                                                {{ number_format($request->pickup_lat, 6) }}, {{ number_format($request->pickup_lng, 6) }}
                                            </small>
                                            <small>
                                                <a href="https://www.google.com/maps?q={{ $request->pickup_lat }},{{ $request->pickup_lng }}"
                                                   target="_blank"
                                                   style="color:#2563eb;font-size:11px;">
                                                    <i class="fa-solid fa-map"></i> View on map
                                                </a>
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $request->emergency_type ?? 'Medical emergency' }}</td>
                                    <td>{{ $request->ambulance->ambulance_number ?? 'Unassigned' }}</td>
                                    <td>
                                        <strong>{{ $request->dispatcher->name ?? 'Not assigned' }}</strong>
                                        <small>{{ $request->dispatcher->phone ?? 'No dispatcher action yet' }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $action['label'] }}</strong>
                                        <small>{{ $action['detail'] }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusBadge[$request->status] ?? 'badge-blue' }}" data-request-badge="{{ $request->id }}">
                                            {{ str_replace('_', ' ', ucfirst($request->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $request->updated_at->format('M d, H:i') }}</strong>
                                        <small>{{ $request->updated_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="empty-cell">No emergency requests found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </section>

        <section class="admin-grid admin-grid--single">
            <article class="panel" id="tracking">
                <div class="panel-header">
                    <div>
                        <span class="eyebrow">Location API</span>
                        <h2>Live Ambulance Tracking</h2>
                    </div>
                    <span class="panel-note" id="lastUpdated">Updated {{ $stats['last_updated'] }}</span>
                </div>
                <div id="adminMap" class="admin-map"></div>
            </article>
        </section>

        <section class="admin-grid admin-grid--management">
            <article class="panel" id="fleet">
                <div class="panel-header">
                    <div>
                        <span class="eyebrow">Fleet management</span>
                        <h2>Ambulance Fleet</h2>
                    </div>
                    <input class="table-search" type="search" placeholder="Search fleet" data-table-search="fleetTable">
                </div>

                <form method="POST" action="{{ route('admin.ambulances.store') }}" class="admin-form admin-form--inline">
                    @csrf
                    <input name="ambulance_number" placeholder="Ambulance number" required>
                    <input name="plate_number" placeholder="Plate number" required>
                    <select name="driver_id">
                        <option value="">No driver</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" required>
                        @foreach($ambulanceStatusLabels as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-blue">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Ambulance</span>
                    </button>
                </form>

                @foreach($ambulances as $ambulance)
                    <form id="ambulance-form-{{ $ambulance->id }}" method="POST" action="{{ route('admin.ambulances.update', $ambulance) }}">
                        @csrf
                        @method('PATCH')
                    </form>

                    <form id="ambulance-delete-{{ $ambulance->id }}" method="POST" action="{{ route('admin.ambulances.destroy', $ambulance) }}" onsubmit="return confirm('Delete this ambulance from the system?')">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach

                <div class="table-wrap">
                    <table class="admin-table compact-admin-table" id="fleetTable">
                        <thead>
                            <tr>
                                <th>Ambulance</th>
                                <th>Plate</th>
                                <th>Driver</th>
                                <th>Status</th>
                                <th>Vehicle Health</th>
                                <th>Maintenance / Tech Control</th>
                                <th>Live Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ambulances as $ambulance)
                                <tr>
                                    <td>
                                        <strong>{{ $ambulance->ambulance_number }}</strong>
                                        <small>API: /api/ambulances/{{ $ambulance->id }}</small>
                                        <input class="compact-input" form="ambulance-form-{{ $ambulance->id }}" name="ambulance_number" value="{{ $ambulance->ambulance_number }}" required>
                                    </td>
                                    <td>
                                        <input class="compact-input" form="ambulance-form-{{ $ambulance->id }}" name="plate_number" value="{{ $ambulance->plate_number }}" required>
                                    </td>
                                    <td>
                                        <select class="compact-input" form="ambulance-form-{{ $ambulance->id }}" name="driver_id">
                                            <option value="">No driver</option>
                                            @foreach($drivers as $driver)
                                                <option value="{{ $driver->id }}" @selected($ambulance->driver_id === $driver->id)>
                                                    {{ $driver->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <span class="fleet-status-chip fleet-status-chip--{{ str_replace('_', '-', $ambulance->status) }}">
                                            {{ $ambulanceStatusLabels[$ambulance->status] ?? ucfirst($ambulance->status) }}
                                        </span>
                                        <select class="compact-input status-select" form="ambulance-form-{{ $ambulance->id }}" name="status" data-ambulance-status="{{ $ambulance->id }}">
                                            @foreach($ambulanceStatusLabels as $status => $label)
                                                <option value="{{ $status }}" @selected($ambulance->status === $status)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <div style="font-size:12px;display:flex;flex-direction:column;gap:3px;">
                                            <span><i class="fa-solid fa-gas-pump" style="color:#6b7280;width:14px;"></i> Fuel: <strong>{{ $ambulance->fuel_level }}%</strong></span>
                                            <span><i class="fa-solid fa-car-battery" style="color:#6b7280;width:14px;"></i> Battery: <strong>{{ $ambulance->battery_level }}%</strong></span>
                                            <span><i class="fa-solid fa-circle-check" style="color:#6b7280;width:14px;"></i> <strong>{{ $ambulance->overall_condition }}</strong></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size:12px;display:flex;flex-direction:column;gap:4px;">
                                            @if($ambulance->maintenance_issue)
                                                <span style="background:#fef3c7;border-radius:4px;padding:2px 6px;color:#92400e;">
                                                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $ambulance->maintenance_issue }}
                                                </span>
                                            @else
                                                <span style="color:#9ca3af;">No issue reported</span>
                                            @endif
                                            @if($ambulance->technical_control_until)
                                                <span style="background:#ede9fe;border-radius:4px;padding:2px 6px;color:#5b21b6;">
                                                    <i class="fa-solid fa-clipboard-check"></i> TC until: {{ $ambulance->technical_control_until->format('M d, Y') }}
                                                </span>
                                            @endif
                                            @if($ambulance->next_service_due_at)
                                                <span style="background:#f0fdf4;border-radius:4px;padding:2px 6px;color:#166534;">
                                                    <i class="fa-solid fa-calendar-check"></i> Service: {{ $ambulance->next_service_due_at->format('M d, Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php($loc = $ambulance->latestLocation)
                                        @php($lat = $loc->latitude ?? $ambulance->current_lat)
                                        @php($lng = $loc->longitude ?? $ambulance->current_lng)
                                        <div style="font-size:12px;display:flex;flex-direction:column;gap:4px;">
                                            @if($lat && $lng)
                                                <span style="color:#374151;">
                                                    <i class="fa-solid fa-location-dot" style="color:#dc2626;"></i>
                                                    {{ number_format($lat,5) }}, {{ number_format($lng,5) }}
                                                </span>
                                                @if(isset($loc->recorded_at))
                                                    <span style="color:#9ca3af;font-size:11px;">
                                                        <i class="fa-solid fa-clock"></i>
                                                        {{ $loc->recorded_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                                <a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}"
                                                   target="_blank" style="color:#2563eb;font-size:11px;">
                                                    <i class="fa-solid fa-map"></i> View on map
                                                </a>
                                            @else
                                                <span style="color:#9ca3af;">
                                                    <i class="fa-solid fa-location-dot"></i>
                                                    No GPS yet — driver must send location
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <button form="ambulance-form-{{ $ambulance->id }}" type="submit" class="btn btn-green compact-btn" title="Save ambulance">
                                                <i class="fa-solid fa-floppy-disk"></i>
                                                <span>Save</span>
                                            </button>
                                            <button form="ambulance-delete-{{ $ambulance->id }}" type="submit" class="btn btn-red compact-btn" title="Delete ambulance">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="empty-cell">No ambulances registered.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="panel" id="users">
                <div class="panel-header">
                    <div>
                        <span class="eyebrow">Access control</span>
                        <h2>Users & Roles</h2>
                    </div>
                </div>

                <div class="role-grid">
                    <div><strong data-role="patients">{{ $stats['role_counts']['patients'] }}</strong><span>Patients</span></div>
                    <div><strong data-role="dispatchers">{{ $stats['role_counts']['dispatchers'] }}</strong><span>Dispatchers</span></div>
                    <div><strong data-role="drivers">{{ $stats['role_counts']['drivers'] }}</strong><span>Drivers</span></div>
                    <div><strong data-role="admins">{{ $stats['role_counts']['admins'] }}</strong><span>Admins</span></div>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}" class="admin-form">
                    @csrf
                    <div class="admin-form-grid">
                        <input name="name" placeholder="Full name" required>
                        <input type="email" name="email" placeholder="Email address" required>
                        <input name="phone" placeholder="Phone number">
                        <select name="role" required>
                            <option value="patient">Patient</option>
                            <option value="dispatcher">Dispatcher</option>
                            <option value="driver">Driver</option>
                            <option value="admin">Admin</option>
                        </select>
                        <input type="password" name="password" placeholder="Password" required>
                        <button type="submit" class="btn btn-blue">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Add User</span>
                        </button>
                    </div>
                </form>

                @foreach($users as $user)
                    <form id="user-form-{{ $user->id }}" method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PATCH')
                    </form>

                    <form id="user-delete-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach

                <div class="table-wrap">
                    <table class="admin-table compact-admin-table" id="usersTable">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Password</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="compact-user-cell">
                                            <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            <input class="compact-input" form="user-form-{{ $user->id }}" name="name" value="{{ $user->name }}" required>
                                        </div>
                                    </td>
                                    <td>
                                        <input class="compact-input" form="user-form-{{ $user->id }}" type="email" name="email" value="{{ $user->email }}" required>
                                    </td>
                                    <td>
                                        <input class="compact-input" form="user-form-{{ $user->id }}" name="phone" value="{{ $user->phone }}" placeholder="Phone">
                                    </td>
                                    <td>
                                        <select class="compact-input" form="user-form-{{ $user->id }}" name="role" required>
                                            @foreach(['patient', 'dispatcher', 'driver', 'admin'] as $role)
                                                <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input class="compact-input" form="user-form-{{ $user->id }}" type="password" name="password" placeholder="Optional">
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <button form="user-form-{{ $user->id }}" type="submit" class="btn btn-green compact-btn" title="Save user">
                                                <i class="fa-solid fa-floppy-disk"></i>
                                                <span>Save</span>
                                            </button>
                                            <button form="user-delete-{{ $user->id }}" type="submit" class="btn btn-red compact-btn" title="Delete user" @disabled(auth()->id() === $user->id)>
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="empty-cell">No users found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </section>

        <section class="panel" id="reports">
            <div class="panel-header">
                <div>
                    <span class="eyebrow">Reporting</span>
                    <h2>Operational Summary</h2>
                </div>
            </div>

            <div class="report-grid">
                <div>
                    <span>Total Requests</span>
                    <strong data-metric="requests">{{ $stats['totals']['requests'] }}</strong>
                </div>
                <div>
                    <span>Total Users</span>
                    <strong data-metric="users">{{ $stats['totals']['users'] }}</strong>
                </div>
                <div>
                    <span>Total Ambulances</span>
                    <strong data-metric="ambulances">{{ $stats['totals']['ambulances'] }}</strong>
                </div>
                <div>
                    <span>Maintenance Queue</span>
                    <strong data-metric="maintenance_ambulances">{{ $stats['totals']['maintenance_ambulances'] }}</strong>
                </div>
                <div>
                    <span>Technical Control</span>
                    <strong data-metric="technical_control_ambulances">{{ $stats['totals']['technical_control_ambulances'] }}</strong>
                </div>
                <div>
                    <span>Scheduled Maintenance</span>
                    <strong data-metric="scheduled_maintenance_ambulances">{{ $stats['totals']['scheduled_maintenance_ambulances'] }}</strong>
                </div>
                <div>
                    <span>Out of Service</span>
                    <strong data-metric="out_of_service_ambulances">{{ $stats['totals']['out_of_service_ambulances'] }}</strong>
                </div>
            </div>

            <div class="report-actions" style="margin-top:1.5rem; display:flex; gap:1rem; flex-wrap:wrap; align-items:center;">
                <form action="{{ route('admin.reports.weekly') }}" method="get" style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center;">
                    <label for="report_type" class="panel-note" style="color:#111827; font-weight:600;">Select report</label>
                    <select id="report_type" name="report_type" class="form-control" style="min-width:220px;">
                        <option value="weekly">Weekly Operational Summary</option>
                        <option value="fleet">Fleet Status Summary</option>
                        <option value="users">User Role Summary</option>
                    </select>
                    <button type="submit" name="format" value="pdf" class="btn btn-red" title="Download the selected PDF report">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Download PDF Report</span>
                    </button>
                </form>
                <span class="panel-note" style="align-self:center; color:#4b5563;">Choose the report you need, then download the generated PDF file.</span>
            </div>
        </section>
    </main>
</div>

<script>
const adminDashboardData = @js($stats);
const adminRoutes = {
    overview: "{{ route('admin.api.dashboard') }}",
    ambulanceStatus: "{{ url('/admin/api/ambulances') }}",
    csrf: "{{ csrf_token() }}",
};

let requestsChart;
let fleetChart;
let adminMap;
let mapMarkers = [];

function titleCaseStatus(status) {
    return status.replace('_', ' ').replace(/\b\w/g, char => char.toUpperCase());
}

function badgeClass(status) {
    return {
        pending: 'badge-yellow',
        assigned: 'badge-blue',
        on_route: 'badge-indigo',
        completed: 'badge-green',
        cancelled: 'badge-red',
        available: 'badge-green',
        busy: 'badge-blue',
        maintenance: 'badge-yellow',
        technical_control: 'badge-indigo',
        scheduled_maintenance: 'badge-purple',
        out_of_service: 'badge-red',
    }[status] || 'badge-blue';
}

function setBadge(element, status) {
    element.className = `badge ${badgeClass(status)}`;
    element.textContent = titleCaseStatus(status);
}

function updateMetrics(data) {
    Object.entries(data.totals).forEach(([key, value]) => {
        document.querySelectorAll(`[data-metric="${key}"]`).forEach(node => node.textContent = value);
    });

    Object.entries(data.ambulance_statuses).forEach(([key, value]) => {
        document.querySelectorAll(`[data-status="${key}"]`).forEach(node => node.textContent = value);
    });

    Object.entries(data.role_counts).forEach(([key, value]) => {
        document.querySelectorAll(`[data-role="${key}"]`).forEach(node => node.textContent = value);
    });

    document.getElementById('lastUpdated').textContent = `Updated ${data.last_updated}`;
}

function renderCharts(data) {
    const requestCtx = document.getElementById('adminRequestsChart');
    const fleetCtx = document.getElementById('adminFleetChart');

    const requestLabels = data.request_trend.map(item => item.label);
    const requestValues = data.request_trend.map(item => item.total);

    if (requestsChart) {
        requestsChart.data.labels = requestLabels;
        requestsChart.data.datasets[0].data = requestValues;
        requestsChart.update();
    } else {
        requestsChart = new Chart(requestCtx, {
            type: 'line',
            data: {
                labels: requestLabels,
                datasets: [{
                    label: 'Emergency requests',
                    data: requestValues,
                    borderColor: '#dc2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.12)',
                    fill: true,
                    tension: 0.38,
                    borderWidth: 3,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    const fleetLabels = ['Available', 'On mission', 'Maintenance', 'Technical control', 'Scheduled', 'Out of service'];
    const fleetValues = [
        data.ambulance_statuses.available,
        data.ambulance_statuses.busy,
        data.ambulance_statuses.maintenance,
        data.ambulance_statuses.technical_control,
        data.ambulance_statuses.scheduled_maintenance,
        data.ambulance_statuses.out_of_service
    ];

    if (fleetChart) {
        fleetChart.data.datasets[0].data = fleetValues;
        fleetChart.update();
    } else {
        fleetChart = new Chart(fleetCtx, {
            type: 'doughnut',
            data: {
                labels: fleetLabels,
                datasets: [{
                    data: fleetValues,
                    backgroundColor: ['#16a34a', '#2563eb', '#f59e0b', '#4f46e5', '#9333ea', '#dc2626'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
}

function renderMap(data) {
    const defaultCenter = [-1.9441, 30.0619];

    if (!adminMap) {
        adminMap = L.map('adminMap').setView(defaultCenter, 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(adminMap);
    }

    mapMarkers.forEach(marker => marker.remove());
    mapMarkers = [];

    const locations = data.latest_locations || [];

    locations.forEach(location => {
        if (!location.latitude || !location.longitude) {
            return;
        }

        const marker = L.marker([location.latitude, location.longitude])
            .addTo(adminMap)
            .bindPopup(`
                <strong>${location.ambulance_number || 'Ambulance'}</strong><br>
                ${location.plate_number || 'No plate'}<br>
                Driver: ${location.driver || 'Unassigned'}<br>
                Status: ${titleCaseStatus(location.status || 'unknown')}
            `);

        mapMarkers.push(marker);
    });

    if (mapMarkers.length) {
        const group = L.featureGroup(mapMarkers);
        adminMap.fitBounds(group.getBounds().pad(0.25));
    }
}

async function fetchDashboard() {
    const response = await fetch(adminRoutes.overview, {
        headers: { 'Accept': 'application/json' },
    });

    if (!response.ok) {
        throw new Error('Dashboard API request failed');
    }

    const data = await response.json();
    updateMetrics(data);
    renderCharts(data);
    renderMap(data);
    return data;
}

async function patchStatus(url, status) {
    const response = await fetch(url, {
        method: 'PATCH',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': adminRoutes.csrf,
        },
        body: JSON.stringify({ status }),
    });

    if (!response.ok) {
        throw new Error('Status update failed');
    }

    return response.json();
}

function setupStatusActions() {
    document.querySelectorAll('[data-ambulance-status]').forEach(select => {
        select.dataset.previousValue = select.value;
        select.addEventListener('change', async event => {
            const id = event.target.dataset.ambulanceStatus;
            const previous = event.target.dataset.previousValue || event.target.value;
            event.target.disabled = true;
            try {
                await patchStatus(`${adminRoutes.ambulanceStatus}/${id}/status`, event.target.value);
                event.target.dataset.previousValue = event.target.value;
                await fetchDashboard();
            } catch (error) {
                event.target.value = previous;
                alert('Unable to update ambulance status. Please try again.');
            } finally {
                event.target.disabled = false;
            }
        });
    });
}

function setupSearch() {
    document.querySelectorAll('[data-table-search]').forEach(input => {
        input.addEventListener('input', event => {
            const target = document.getElementById(event.target.dataset.tableSearch);
            const rows = target.querySelectorAll('tbody tr, .fleet-item');
            const query = event.target.value.toLowerCase();

            rows.forEach(row => {
                row.hidden = !row.textContent.toLowerCase().includes(query);
            });
        });
    });
}

function setupExport() {
    document.getElementById('exportDashboard').addEventListener('click', () => {
        const rows = [
            ['Metric', 'Value'],
            ['Pending Requests', document.querySelector('[data-metric="pending_requests"]').textContent],
            ['Active Missions', document.querySelector('[data-metric="active_requests"]').textContent],
            ['Available Ambulances', document.querySelector('[data-metric="available_ambulances"]').textContent],
            ['Completed Today', document.querySelector('[data-metric="completed_today"]').textContent],
            ['Total Requests', document.querySelector('[data-metric="requests"]').textContent],
            ['Total Users', document.querySelector('[data-metric="users"]').textContent],
            ['Total Ambulances', document.querySelector('[data-metric="ambulances"]').textContent],
            ['Technical Control', document.querySelector('[data-metric="technical_control_ambulances"]').textContent],
            ['Scheduled Maintenance', document.querySelector('[data-metric="scheduled_maintenance_ambulances"]').textContent],
            ['Out of Service', document.querySelector('[data-metric="out_of_service_ambulances"]').textContent],
        ];

        const csv = rows.map(row => row.map(cell => `"${String(cell).replaceAll('"', '""')}"`).join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'nyarugenge-admin-dashboard.csv';
        link.click();
        URL.revokeObjectURL(link.href);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    renderCharts(adminDashboardData);
    renderMap(adminDashboardData);
    setupStatusActions();
    setupSearch();
    setupExport();

    document.getElementById('refreshDashboard').addEventListener('click', () => fetchDashboard().catch(console.error));
    setInterval(() => fetchDashboard().catch(console.error), 30000);
});
</script>

</x-layouts.app>

<x-layouts.app>
@php $patient = auth()->user(); @endphp

<style>
/* ══════════════════════════════════════════
   KEYFRAMES
══════════════════════════════════════════ */
@keyframes gradientShift {
    0%   { background-position: 0% 50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
@keyframes fadeUp {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes slideIn {
    from { opacity:0; transform:translateX(-18px); }
    to   { opacity:1; transform:translateX(0); }
}
@keyframes softPulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.65; transform:scale(1.2); }
}
@keyframes ambientGlow {
    0%,100% { box-shadow: 0 0 0 0 rgba(245,158,11,.45); }
    50%      { box-shadow: 0 0 0 10px rgba(245,158,11,0); }
}
@keyframes blueGlow {
    0%,100% { box-shadow: 0 0 0 0 rgba(37,99,235,.4); }
    50%      { box-shadow: 0 0 0 10px rgba(37,99,235,0); }
}
@keyframes countUp {
    from { opacity:0; transform:scale(.75); }
    to   { opacity:1; transform:scale(1); }
}
@keyframes driverPing {
    0%   { transform:scale(1); opacity:1; }
    70%  { transform:scale(2.2); opacity:0; }
    100% { transform:scale(2.2); opacity:0; }
}

/* ══════════════════════════════════════════
   SHELL
══════════════════════════════════════════ */
.pat-shell {
    display: flex;
    min-height: 100vh;
    background: #f0f4f8;
    font-family: 'Inter', sans-serif;
}

/* ══════════════════════════════════════════
   SIDEBAR  — teal / indigo / slate palette
══════════════════════════════════════════ */
.pat-sidebar {
    width: 240px;
    min-width: 240px;
    background: linear-gradient(180deg, #0d1117 0%, #1a1f2e 45%, #0d1117 100%);
    background-size: 200% 300%;
    animation: gradientShift 10s ease infinite;
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    z-index: 100;
}

/* ── Brand ── */
.pat-brand {
    padding: 22px 18px 18px;
    border-bottom: 1px solid rgba(255,255,255,.08);
    display: flex; align-items: center; gap: 12px;
    animation: slideIn .5s ease;
}
.pat-brand__icon {
    width: 42px; height: 42px;
    background: linear-gradient(135deg, #f59e0b, #ef4444);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 19px; color: #fff; flex-shrink: 0;
    animation: ambientGlow 2.5s infinite;
}
.pat-brand strong { color: #f0fdfa; font-size: 14px; display: block; line-height: 1.3; }
.pat-brand small  { color: #94a3b8; font-size: 11px; }

/* ── Logout at top of sidebar (just below brand) ── */
.pat-sidebar-logout {
    padding: 10px 12px;
    border-bottom: 1px solid rgba(255,255,255,.07);
}
.pat-sidebar-logout button {
    width: 100%; padding: 8px 12px; border-radius: 9px;
    background: rgba(245,158,11,.12);
    color: #fcd34d; border: 1px solid rgba(245,158,11,.25);
    font-size: 12px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    transition: all .2s;
}
.pat-sidebar-logout button:hover {
    background: rgba(245,158,11,.25); color: #fff;
}

/* ── Patient profile ── */
.pat-profile {
    padding: 14px 18px;
    border-bottom: 1px solid rgba(255,255,255,.07);
    display: flex; align-items: center; gap: 10px;
    animation: slideIn .6s ease;
}
.pat-avatar {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, #f59e0b, #2563eb);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 15px; font-weight: 700; flex-shrink: 0;
    animation: ambientGlow 3s infinite;
}
.pat-profile strong { color: #f0fdfa; font-size: 13px; display: block; }
.pat-profile small  { color: #94a3b8; font-size: 11px; }

/* ── Nav ── */
.pat-nav {
    padding: 14px 10px;
    flex: 1;
    display: flex; flex-direction: column; gap: 3px;
}
.pat-nav a {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 12px; border-radius: 9px;
    color: #94a3b8; font-size: 13px; font-weight: 500;
    text-decoration: none; transition: all .2s;
    position: relative; overflow: hidden;
}
.pat-nav a::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(90deg, rgba(245,158,11,.15), rgba(37,99,235,.12));
    opacity: 0; transition: opacity .2s; border-radius: 9px;
}
.pat-nav a:hover::before, .pat-nav a.active::before { opacity: 1; }
.pat-nav a:hover, .pat-nav a.active { color: #f0fdfa; }
.pat-nav a.active {
    background: linear-gradient(90deg, rgba(245,158,11,.2), rgba(37,99,235,.12));
    color: #fbbf24;
    border-left: 3px solid #f59e0b;
}
.pat-nav a i { width: 16px; text-align: center; font-size: 14px; }

/* ── Nav badge ── */
.nav-badge {
    margin-left: auto;
    background: #f59e0b; color: #0d1117;
    font-size: 10px; font-weight: 700;
    padding: 2px 7px; border-radius: 20px;
    animation: softPulse 2s infinite;
}
</style>

<style>
/* ══════════════════════════════════════════
   MAIN CONTENT
══════════════════════════════════════════ */
.pat-main { flex:1; display:flex; flex-direction:column; overflow-y:auto; min-width:0; }

/* ── Top bar ── */
.pat-topbar {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    padding: 13px 26px;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top:0; z-index:50;
    animation: fadeUp .4s ease;
}
.pat-topbar h1 { font-size:17px; font-weight:700; color:#0f172a; margin:0; }
.pat-topbar small { font-size:11px; color:#64748b; display:block; margin-top:2px; }
.pat-topbar__right { display:flex; align-items:center; gap:10px; }

/* ── SOS button — teal instead of red ── */
.sos-btn {
    padding: 9px 18px;
    background: linear-gradient(135deg, #f59e0b, #ef4444);
    background-size: 200% 200%;
    animation: gradientShift 4s ease infinite, ambientGlow 2s infinite;
    color: #fff; border: none; border-radius: 10px;
    font-size: 13px; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    text-decoration: none; transition: transform .15s;
}
.sos-btn:hover { transform: scale(1.04); }

/* ── Content ── */
.pat-content { padding:22px 26px; display:flex; flex-direction:column; gap:26px; }

/* ── Section title ── */
.pat-section-title {
    display:flex; align-items:center; gap:12px;
    margin-bottom:14px; animation:fadeUp .5s ease;
}
.pat-section-icon {
    width:36px; height:36px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:16px; color:#fff; flex-shrink:0;
}
.pat-section-title h2 { font-size:16px; font-weight:700; color:#0f172a; margin:0; }
.pat-section-title small { font-size:12px; color:#64748b; display:block; margin-top:2px; }

/* ── Metric cards — teal / indigo / emerald ── */
.pat-metrics { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
.pat-metric {
    border-radius:14px; padding:20px 18px;
    display:flex; align-items:center; gap:14px;
    position:relative; overflow:hidden;
    animation:fadeUp .5s ease both;
    transition:transform .2s, box-shadow .2s;
}
.pat-metric:hover { transform:translateY(-3px); box-shadow:0 12px 28px rgba(0,0,0,.12); }
.pat-metric::after {
    content:''; position:absolute; top:-28px; right:-28px;
    width:90px; height:90px; border-radius:50%;
    background:rgba(255,255,255,.09);
}
.pat-metric--teal   { background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; }
.pat-metric--indigo { background:linear-gradient(135deg,#2563eb,#1d4ed8); color:#fff; }
.pat-metric--emerald{ background:linear-gradient(135deg,#059669,#065f46); color:#fff; }
.pat-metric__icon {
    width:46px; height:46px; border-radius:12px;
    background:rgba(255,255,255,.2);
    display:flex; align-items:center; justify-content:center;
    font-size:21px; flex-shrink:0;
}
.pat-metric p  { font-size:12px; opacity:.85; margin:0 0 3px; }
.pat-metric strong { font-size:28px; font-weight:800; display:block; animation:countUp .6s ease both; }

/* ── Card ── */
.pat-card {
    background:#fff; border-radius:14px; border:1px solid #e2e8f0;
    overflow:hidden; animation:fadeUp .5s ease both; transition:box-shadow .2s;
}
.pat-card:hover { box-shadow:0 6px 22px rgba(0,0,0,.07); }
.pat-card-header {
    padding:16px 20px; border-bottom:1px solid #f1f5f9;
    display:flex; align-items:center; justify-content:space-between;
}
.pat-card-header h3 { font-size:14px; font-weight:700; color:#0f172a; margin:0; }
.pat-card-header small { font-size:11px; color:#94a3b8; }
.pat-card-body { padding:20px; }

/* ── Form ── */
.pat-form { display:flex; flex-direction:column; gap:14px; }
.pat-form label {
    font-size:11px; font-weight:700; color:#374151;
    text-transform:uppercase; letter-spacing:.04em;
    display:block; margin-bottom:4px;
}
.pat-form input, .pat-form select, .pat-form textarea {
    width:100%; padding:10px 13px;
    border:1.5px solid #d1d5db; border-radius:9px;
    font-size:13px; color:#111827; outline:none;
    transition:border-color .2s, box-shadow .2s;
    background:#fafafa; box-sizing:border-box;
}
.pat-form input:focus, .pat-form select:focus, .pat-form textarea:focus {
    border-color:#f59e0b;
    box-shadow:0 0 0 3px rgba(245,158,11,.12);
    background:#fff;
}
.pat-form textarea { resize:vertical; min-height:88px; }

/* ── Detect button ── */
.loc-btn {
    padding:10px 13px; border-radius:9px;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff; border:none; cursor:pointer;
    font-size:13px; white-space:nowrap;
    transition:transform .15s, opacity .15s; flex-shrink:0;
}
.loc-btn:hover { transform:scale(1.05); opacity:.9; }

/* ── Submit button ── */
.pat-submit {
    padding:12px 20px; border-radius:11px;
    background:linear-gradient(135deg,#f59e0b,#ef4444);
    background-size:200% 200%;
    animation:gradientShift 4s ease infinite;
    color:#fff; border:none; cursor:pointer;
    font-size:14px; font-weight:700;
    display:flex; align-items:center; justify-content:center; gap:8px;
    transition:transform .15s, box-shadow .15s; width:100%;
}
.pat-submit:hover { transform:translateY(-2px); box-shadow:0 8px 22px rgba(245,158,11,.4); }

/* ── Map ── */
.pat-map { height:270px; border-radius:11px; overflow:hidden; border:1.5px solid #e2e8f0; }

/* ── Request history ── */
.req-list { display:flex; flex-direction:column; }
.req-item {
    padding:14px 20px; border-bottom:1px solid #f1f5f9;
    display:flex; align-items:flex-start; justify-content:space-between; gap:12px;
    transition:background .15s; animation:fadeUp .4s ease both;
}
.req-item:last-child { border-bottom:none; }
.req-item:hover { background:#f8fafc; }
.req-item__info strong { font-size:13px; color:#0f172a; font-weight:600; display:block; }
.req-item__info small  { font-size:11px; color:#64748b; display:block; margin-top:3px; }
.req-item__meta { text-align:right; flex-shrink:0; }
.req-item__meta small { font-size:11px; color:#94a3b8; display:block; margin-top:4px; }

/* ── Badges ── */
.badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-pending  { background:#fef3c7; color:#92400e; border:1px solid #fcd34d; }
.badge-assigned { background:#eef2ff; color:#4338ca; border:1px solid #c7d2fe; }
.badge-on-route { background:#ede9fe; color:#6d28d9; border:1px solid #ddd6fe; }
.badge-completed{ background:#dcfce7; color:#166534; border:1px solid #86efac; }
.badge-cancelled{ background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; }

/* ── Live tracking banner ── */
.tracking-banner {
    background:linear-gradient(135deg,#f59e0b,#ef4444);
    background-size:200% 200%;
    animation:gradientShift 5s ease infinite;
    border-radius:12px; padding:14px 18px;
    display:flex; align-items:center; gap:12px;
    color:#fff; margin-bottom:14px;
}
.tracking-banner .ping-dot {
    width:12px; height:12px; border-radius:50%;
    background:#fff; position:relative; flex-shrink:0;
}
.tracking-banner .ping-dot::after {
    content:''; position:absolute; inset:0;
    border-radius:50%; background:#fff;
    animation:driverPing 1.4s infinite;
}

/* ── Alert ── */
.pat-alert {
    padding:13px 16px; border-radius:11px;
    display:flex; align-items:flex-start; gap:10px;
    font-size:13px; animation:fadeUp .4s ease;
}
.pat-alert--success { background:#f0fdf4; border:1px solid #86efac; color:#166534; }
.pat-alert--error   { background:#fef2f2; border:1px solid #fca5a5; color:#dc2626; }

/* ── Responsive ── */
@media (max-width:960px) {
    .pat-sidebar { display:none; }
    .pat-metrics { grid-template-columns:repeat(2,1fr); }
    .pat-content { padding:14px; }
}
@media (max-width:560px) {
    .pat-metrics { grid-template-columns:1fr 1fr; }
}
</style>

<div class="pat-shell">

    {{-- ══════════ SIDEBAR ══════════ --}}
    <aside class="pat-sidebar">

        {{-- Brand --}}
        <div class="pat-brand">
            <div class="pat-brand__icon"><i class="fa-solid fa-truck-medical"></i></div>
            <div>
                <strong>Nyarugenge EMS</strong>
                <small>Patient Portal</small>
            </div>
        </div>

        {{-- Logout — top of sidebar --}}
        <div class="pat-sidebar-logout">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>

        {{-- Patient profile --}}
        <div class="pat-profile">
            <div class="pat-avatar">{{ strtoupper(substr($patient->name,0,1)) }}</div>
            <div>
                <strong>{{ $patient->name }}</strong>
                <small>{{ $patient->email }}</small>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="pat-nav">
            <a href="#overview" class="active">
                <i class="fa-solid fa-chart-line"></i> Overview
            </a>
            <a href="#request">
                <i class="fa-solid fa-ambulance"></i> Request Ambulance
                @if($pendingRequests > 0)
                    <span class="nav-badge">{{ $pendingRequests }}</span>
                @endif
            </a>
            <a href="#history">
                <i class="fa-solid fa-clock-rotate-left"></i> My Requests
            </a>
            <a href="#tracking">
                <i class="fa-solid fa-location-crosshairs"></i> Live Tracking
                @if($activeRequest)
                    <span class="nav-badge" style="background:#f59e0b;color:#0d1117;">Live</span>
                @endif
            </a>
            <a href="#info">
                <i class="fa-solid fa-circle-info"></i> Hospital Info
            </a>
        </nav>

    </aside>

    {{-- ══════════ MAIN ══════════ --}}
    <div class="pat-main">

        {{-- Top bar --}}
        <div class="pat-topbar">
            <div>
                <h1><i class="fa-solid fa-hospital" style="color:#f59e0b;"></i> Patient Dashboard</h1>
                <small>Welcome, {{ $patient->name }} — Nyarugenge Hospital Ambulance System</small>
            </div>
            <div class="pat-topbar__right">
                <a href="#request" class="sos-btn">
                    <i class="fa-solid fa-bell-medical"></i> Request Ambulance
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div style="padding:14px 26px 0;">
                <div class="pat-alert pat-alert--success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if($errors->any())
            <div style="padding:14px 26px 0;">
                <div class="pat-alert pat-alert--error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>@foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach</div>
                </div>
            </div>
        @endif

        <div class="pat-content">

            {{-- ── 1. OVERVIEW ── --}}
            <div id="overview">
                <div class="pat-section-title">
                    <div class="pat-section-icon" style="background:linear-gradient(135deg,#f59e0b,#ef4444);">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div><h2>Overview</h2><small>Your emergency request summary</small></div>
                </div>
                <div class="pat-metrics">
                    <div class="pat-metric pat-metric--teal" style="animation-delay:.05s">
                        <div class="pat-metric__icon"><i class="fa-solid fa-file-medical"></i></div>
                        <div><p>Total Requests</p><strong>{{ $totalRequests }}</strong></div>
                    </div>
                    <div class="pat-metric pat-metric--indigo" style="animation-delay:.1s">
                        <div class="pat-metric__icon"><i class="fa-solid fa-hourglass-half"></i></div>
                        <div><p>Active / Pending</p><strong>{{ $pendingRequests }}</strong></div>
                    </div>
                    <div class="pat-metric pat-metric--emerald" style="animation-delay:.15s">
                        <div class="pat-metric__icon"><i class="fa-solid fa-circle-check"></i></div>
                        <div><p>Completed</p><strong>{{ $completedRequests }}</strong></div>
                    </div>
                </div>
            </div>

            {{-- ── 2. REQUEST FORM ── --}}
            <div id="request">
                <div class="pat-section-title">
                    <div class="pat-section-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        <i class="fa-solid fa-ambulance"></i>
                    </div>
                    <div><h2>Request Ambulance</h2><small>Fill in your details — we dispatch immediately</small></div>
                </div>
                <div class="pat-card">
                    <div class="pat-card-header">
                        <h3><i class="fa-solid fa-bell-medical" style="color:#f59e0b;"></i> Emergency Request Form</h3>
                        <small>Location detected automatically</small>
                    </div>
                    <div class="pat-card-body">
                        <form method="POST" action="{{ route('patient.requests.store') }}" class="pat-form">
                            @csrf
                            <div>
                                <label>Phone Number</label>
                                <input type="tel" name="phone"
                                       value="{{ old('phone', $patient->phone) }}"
                                       placeholder="e.g. +250 788 000 000" required>
                            </div>
                            <div>
                                <label>Pickup Location</label>
                                <div style="display:flex;gap:10px;">
                                    <input type="text" id="pickup_location" name="pickup_location"
                                           placeholder="Detecting your location…"
                                           value="{{ old('pickup_location') }}"
                                           required readonly style="flex:1;">
                                    <button type="button" class="loc-btn" id="detectBtn" onclick="getCurrentLocation()">
                                        <i class="fa-solid fa-location-crosshairs"></i> Detect
                                    </button>
                                </div>
                                <input type="hidden" name="pickup_lat" id="pickup_lat">
                                <input type="hidden" name="pickup_lng" id="pickup_lng">
                            </div>
                            <div>
                                <label>Your Location on Map</label>
                                <div id="patientMap" class="pat-map"></div>
                            </div>
                            <div>
                                <label>Emergency Type</label>
                                <select name="emergency_type" required>
                                    <option value="">Select emergency type</option>
                                    @foreach(['Accident','Medical Emergency','Heart Attack','Pregnancy','Breathing Problem','Stroke','Trauma','Other'] as $t)
                                        <option value="{{ $t }}" @selected(old('emergency_type')===$t)>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label>Patient Condition</label>
                                <select name="patient_condition" required>
                                    <option value="">Select condition</option>
                                    @foreach(['Critical','Serious','Stable'] as $c)
                                        <option value="{{ $c }}" @selected(old('patient_condition')===$c)>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label>Additional Notes</label>
                                <textarea name="notes"
                                    placeholder="Describe the emergency — symptoms, number of people, special needs…">{{ old('notes') }}</textarea>
                            </div>
                            <button type="submit" class="pat-submit">
                                <i class="fa-solid fa-paper-plane"></i> Send Emergency Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ── 3. MY REQUESTS (no dispatcher name) ── --}}
            <div id="history">
                <div class="pat-section-title">
                    <div class="pat-section-icon" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div><h2>My Requests</h2><small>All your emergency request history</small></div>
                </div>
                <div class="pat-card">
                    <div class="pat-card-header">
                        <h3>Request History</h3>
                        <small>{{ $totalRequests }} total request{{ $totalRequests !== 1 ? 's' : '' }}</small>
                    </div>
                    <div class="req-list">
                        @forelse($requests as $req)
                            @php
                                $bc = match($req->status) {
                                    'pending'   => 'badge-pending',
                                    'assigned'  => 'badge-assigned',
                                    'on_route'  => 'badge-on-route',
                                    'completed' => 'badge-completed',
                                    'cancelled' => 'badge-cancelled',
                                    default     => 'badge-assigned',
                                };
                            @endphp
                            <div class="req-item" style="animation-delay:{{ $loop->index * .05 }}s">
                                <div class="req-item__info">
                                    <strong>{{ $req->pickup_location }}</strong>
                                    <small>
                                        <i class="fa-solid fa-triangle-exclamation" style="color:#d97706;"></i>
                                        {{ $req->emergency_type ?? 'Medical emergency' }}
                                    </small>
                                    @if($req->notes)
                                        <small>
                                            <i class="fa-solid fa-note-sticky" style="color:#6b7280;"></i>
                                            {{ Str::limit($req->notes, 80) }}
                                        </small>
                                    @endif
                                    @if($req->ambulance)
                                        <small>
                                            <i class="fa-solid fa-truck-medical" style="color:#f59e0b;"></i>
                                            Ambulance: {{ $req->ambulance->ambulance_number }}
                                        </small>
                                    @endif
                                    {{-- No dispatcher name shown to patient --}}
                                </div>
                                <div class="req-item__meta">
                                    <span class="badge {{ $bc }}">
                                        {{ str_replace('_',' ',ucfirst($req->status)) }}
                                    </span>
                                    <small>{{ $req->created_at->format('M d, Y') }}</small>
                                    <small>{{ $req->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div style="padding:30px;text-align:center;color:#94a3b8;">
                                <i class="fa-solid fa-file-circle-xmark" style="font-size:30px;margin-bottom:10px;display:block;"></i>
                                No ambulance requests yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ── 4. LIVE TRACKING — driver position auto-updates ── --}}
            <div id="tracking">
                <div class="pat-section-title">
                    <div class="pat-section-icon" style="background:linear-gradient(135deg,#059669,#065f46);">
                        <i class="fa-solid fa-location-crosshairs"></i>
                    </div>
                    <div><h2>Live Tracking</h2><small id="trackingSubtitle">Tracking your ambulance in real time</small></div>
                </div>

                {{-- Active request banner --}}
                @if($activeRequest)
                    <div class="tracking-banner">
                        <div class="ping-dot"></div>
                        <div>
                            <strong style="font-size:14px;">
                                Ambulance {{ $activeRequest->ambulance->ambulance_number ?? '—' }} is
                                {{ $activeRequest->status === 'on_route' ? 'on the way to you' : 'assigned to your request' }}
                            </strong>
                            <div style="font-size:12px;opacity:.85;margin-top:3px;">
                                Map updates every 5 seconds automatically
                            </div>
                        </div>
                    </div>
                @endif

                <div class="pat-card">
                    <div class="pat-card-header">
                        <h3>
                            <i class="fa-solid fa-map-location-dot" style="color:#f59e0b;"></i>
                            Driver &amp; Your Pickup Location
                        </h3>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#475569;">
                                <span style="width:10px;height:10px;border-radius:50%;background:#6366f1;display:inline-block;"></span> Driver
                                <span style="width:10px;height:10px;border-radius:50%;background:#0d9488;display:inline-block;margin-left:6px;"></span> Your pickup
                            </div>
                            <span id="liveStatus" style="font-size:11px;color:#94a3b8;">Connecting…</span>
                        </div>
                    </div>
                    <div id="trackingMap" style="height:400px;border-radius:0 0 14px 14px;"></div>
                </div>
            </div>

            {{-- ── 5. HOSPITAL INFO ── --}}
            <div id="info">
                <div class="pat-section-title">
                    <div class="pat-section-icon" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div><h2>Hospital Information</h2><small>Emergency contacts and services</small></div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;">
                    <div class="pat-card">
                        <div class="pat-card-body" style="display:flex;align-items:center;gap:12px;">
                            <div class="pat-section-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);width:42px;height:42px;flex-shrink:0;">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div>
                                <div style="font-size:12px;color:#64748b;">Emergency Hotline</div>
                                <div style="font-size:22px;font-weight:800;color:#f59e0b;">912</div>
                            </div>
                        </div>
                    </div>
                    <div class="pat-card">
                        <div class="pat-card-body" style="display:flex;align-items:center;gap:12px;">
                            <div class="pat-section-icon" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);width:42px;height:42px;flex-shrink:0;">
                                <i class="fa-solid fa-hospital"></i>
                            </div>
                            <div>
                                <div style="font-size:12px;color:#64748b;">Hospital</div>
                                <div style="font-size:14px;font-weight:700;color:#0f172a;">Nyarugenge Hospital</div>
                                <div style="font-size:11px;color:#64748b;">Kigali, Rwanda</div>
                            </div>
                        </div>
                    </div>
                    <div class="pat-card">
                        <div class="pat-card-body" style="display:flex;align-items:center;gap:12px;">
                            <div class="pat-section-icon" style="background:linear-gradient(135deg,#059669,#065f46);width:42px;height:42px;flex-shrink:0;">
                                <i class="fa-solid fa-truck-medical"></i>
                            </div>
                            <div>
                                <div style="font-size:12px;color:#64748b;">Available Ambulances</div>
                                <div style="font-size:22px;font-weight:800;color:#059669;">{{ $ambulancesAvailable }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end .pat-content --}}
    </div>{{-- end .pat-main --}}
</div>{{-- end .pat-shell --}}

<script>
/* ══════════════════════════════════════════
   CONFIG
══════════════════════════════════════════ */
const PAT_ROUTES = {
    liveTracking: "{{ route('patient.api.tracking') }}",
};
const HAS_ACTIVE = {{ $activeRequest ? 'true' : 'false' }};

/* ══════════════════════════════════════════
   ICONS
══════════════════════════════════════════ */
const driverIcon = L.divIcon({
    className: '',
    html: `<div style="position:relative;width:36px;height:36px;">
        <div style="position:absolute;inset:0;border-radius:50%;background:rgba(99,102,241,.35);animation:driverPing 1.4s infinite;"></div>
        <div style="position:relative;width:36px;height:36px;border-radius:50%;
            background:linear-gradient(135deg,#6366f1,#4f46e5);
            border:3px solid #fff;box-shadow:0 2px 10px rgba(99,102,241,.5);
            display:flex;align-items:center;justify-content:center;color:#fff;font-size:15px;">
            <i class='fa-solid fa-truck-medical'></i>
        </div>
    </div>`,
    iconSize: [36,36], iconAnchor: [18,18], popupAnchor: [0,-20],
});

const patientIcon = L.divIcon({
    className: '',
    html: `<div style="width:32px;height:32px;border-radius:50%;
        background:linear-gradient(135deg,#0d9488,#059669);
        border:3px solid #fff;box-shadow:0 2px 10px rgba(13,148,136,.5);
        display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;">
        <i class='fa-solid fa-user-injured'></i>
    </div>`,
    iconSize: [32,32], iconAnchor: [16,16], popupAnchor: [0,-18],
});

/* ══════════════════════════════════════════
   MAPS
══════════════════════════════════════════ */
let requestMap, requestMarker;
let trackingMap, driverMarker, pickupMarker;
const defaultView = [-1.9441, 30.0619];

function buildTile(map) {
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a>',
    }).addTo(map);
}

function initMaps() {
    // Request form map
    requestMap = L.map('patientMap').setView(defaultView, 13);
    buildTile(requestMap);

    // Tracking map
    trackingMap = L.map('trackingMap').setView(defaultView, 13);
    buildTile(trackingMap);
}

/* ── Place marker on request map ── */
function placeRequestMarker(lat, lng, label) {
    if (requestMarker) requestMap.removeLayer(requestMarker);
    requestMarker = L.marker([lat, lng], { icon: patientIcon })
        .addTo(requestMap).bindPopup(label).openPopup();
    requestMap.setView([lat, lng], 16);
}

/* ── Smooth move helper ── */
function moveMarker(marker, lat, lng) {
    if (marker.getLatLng().distanceTo(L.latLng(lat, lng)) > 0.5) {
        marker.setLatLng([lat, lng]);
    }
}

/* ══════════════════════════════════════════
   LIVE TRACKING — auto-refresh every 5s
══════════════════════════════════════════ */
async function refreshTracking() {
    let data;
    try {
        const res = await fetch(PAT_ROUTES.liveTracking, {
            headers: { Accept: 'application/json' }, cache: 'no-store',
        });
        data = await res.json();
    } catch (e) {
        document.getElementById('liveStatus').textContent = 'Connection error';
        return;
    }

    const statusEl = document.getElementById('liveStatus');
    const subtitleEl = document.getElementById('trackingSubtitle');

    if (!data.active) {
        statusEl.textContent = 'No active request';
        subtitleEl.textContent = 'No ambulance assigned yet';

        // Show only patient's own location if available
        const lat = parseFloat(document.getElementById('pickup_lat')?.value);
        const lng = parseFloat(document.getElementById('pickup_lng')?.value);
        if (lat && lng) {
            if (!pickupMarker) {
                pickupMarker = L.marker([lat, lng], { icon: patientIcon })
                    .addTo(trackingMap)
                    .bindPopup('<strong>Your pickup location</strong>');
                trackingMap.setView([lat, lng], 14);
            }
        }
        return;
    }

    // Active request — show driver + patient
    const dl = data.driver_location;
    const pl = data.patient_location;
    const dLat = parseFloat(dl.latitude);
    const dLng = parseFloat(dl.longitude);
    const pLat = parseFloat(pl.latitude);
    const pLng = parseFloat(pl.longitude);

    // Driver marker
    if (dLat && dLng) {
        if (driverMarker) {
            moveMarker(driverMarker, dLat, dLng);
            driverMarker.getPopup().setContent(
                `<strong>🚑 Ambulance ${data.ambulance_number}</strong><br>
                 Status: <b>${data.status.replace(/_/g,' ')}</b><br>
                 <small>${dLat.toFixed(6)}, ${dLng.toFixed(6)}</small>`
            );
        } else {
            driverMarker = L.marker([dLat, dLng], { icon: driverIcon })
                .addTo(trackingMap)
                .bindPopup(
                    `<strong>🚑 Ambulance ${data.ambulance_number}</strong><br>
                     Status: <b>${data.status.replace(/_/g,' ')}</b>`
                );
        }
    }

    // Patient pickup marker
    if (pLat && pLng) {
        if (pickupMarker) {
            moveMarker(pickupMarker, pLat, pLng);
        } else {
            pickupMarker = L.marker([pLat, pLng], { icon: patientIcon })
                .addTo(trackingMap)
                .bindPopup(`<strong>Your pickup</strong><br>${pl.pickup_location}`);
        }
    }

    // Fit both markers
    const markers = [driverMarker, pickupMarker].filter(Boolean);
    if (markers.length > 1) {
        trackingMap.fitBounds(L.featureGroup(markers).getBounds().pad(0.3));
    } else if (markers.length === 1) {
        trackingMap.setView(markers[0].getLatLng(), 15);
    }

    const now = new Date().toLocaleTimeString();
    statusEl.textContent = `Updated ${now}`;
    subtitleEl.textContent = data.status === 'on_route'
        ? '🚑 Ambulance is on the way to you'
        : '✅ Ambulance assigned — awaiting departure';
}

/* ══════════════════════════════════════════
   GEOLOCATION
══════════════════════════════════════════ */
async function getCurrentLocation() {
    if (!navigator.geolocation) { alert('Geolocation not supported.'); return; }

    const btn = document.getElementById('detectBtn');
    if (btn) btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

    navigator.geolocation.getCurrentPosition(async pos => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;

        document.getElementById('pickup_lat').value = lat;
        document.getElementById('pickup_lng').value = lng;

        let name = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        try {
            const r = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`,
                { headers: { 'Accept-Language': 'en' } }
            );
            const d = await r.json();
            name = d.display_name || name;
        } catch (_) {}

        document.getElementById('pickup_location').value = name;
        placeRequestMarker(lat, lng, `<strong>Your location</strong><br><small>${name}</small>`);

        if (btn) btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Detect';
    }, () => {
        alert('Unable to access location. Please allow location access.');
        if (btn) btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Detect';
    });
}

/* ══════════════════════════════════════════
   SIDEBAR SMOOTH SCROLL
══════════════════════════════════════════ */
document.querySelectorAll('.pat-nav a').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('.pat-nav a').forEach(a => a.classList.remove('active'));
        link.classList.add('active');
        const target = document.querySelector(link.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

document.querySelector('.sos-btn')?.addEventListener('click', e => {
    e.preventDefault();
    document.querySelector('#request')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

/* ══════════════════════════════════════════
   BOOT
══════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    initMaps();
    getCurrentLocation();

    // Start live tracking immediately, then every 5 seconds
    refreshTracking();
    setInterval(refreshTracking, 5000);
});
</script>

</x-layouts.app>

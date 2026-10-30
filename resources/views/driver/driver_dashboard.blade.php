<x-layouts.app>
@php
    $driver        = auth()->user();
    $activeRequest = $assignedRequests->first();
    $serviceDue    = $ambulance?->next_service_due_at;
    $serviceDays   = $serviceDue ? now()->startOfDay()->diffInDays($serviceDue->startOfDay(), false) : null;
    $techUntil     = $ambulance?->technical_control_until;

    $ambStatusColor = match($ambulance?->status) {
        'available'             => ['bg'=>'#059669','label'=>'Available'],
        'busy'                  => ['bg'=>'#2563eb','label'=>'On Mission'],
        'maintenance'           => ['bg'=>'#d97706','label'=>'Maintenance'],
        'technical_control'     => ['bg'=>'#7c3aed','label'=>'Technical Control'],
        'scheduled_maintenance' => ['bg'=>'#0891b2','label'=>'Scheduled Maint.'],
        'out_of_service'        => ['bg'=>'#dc2626','label'=>'Out of Service'],
        default                 => ['bg'=>'#64748b','label'=>'Unknown'],
    };
@endphp

<style>
/* ══════════════════════════════════════════
   KEYFRAMES
══════════════════════════════════════════ */
@keyframes gradShift {
    0%   { background-position: 0% 50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
@keyframes fadeUp {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes slideLeft {
    from { opacity:0; transform:translateX(-20px); }
    to   { opacity:1; transform:translateX(0); }
}
@keyframes countPop {
    from { opacity:0; transform:scale(.7); }
    to   { opacity:1; transform:scale(1); }
}
@keyframes ambientGlow {
    0%,100% { box-shadow: 0 0 0 0 rgba(245,158,11,.45); }
    50%      { box-shadow: 0 0 0 10px rgba(245,158,11,0); }
}
@keyframes blueGlow {
    0%,100% { box-shadow: 0 0 0 0 rgba(37,99,235,.4); }
    50%      { box-shadow: 0 0 0 10px rgba(37,99,235,0); }
}
@keyframes softPulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.6; transform:scale(1.2); }
}
@keyframes ping {
    0%   { transform:scale(1); opacity:.9; }
    70%  { transform:scale(2.4); opacity:0; }
    100% { transform:scale(2.4); opacity:0; }
}
@keyframes barFill {
    from { width:0; }
    to   { width:var(--bar-w); }
}

/* ══════════════════════════════════════════
   SHELL
══════════════════════════════════════════ */
.drv-shell {
    display: flex;
    min-height: 100vh;
    background: #0d1117;
    font-family: 'Inter', sans-serif;
}

/* ══════════════════════════════════════════
   SIDEBAR  — deep space / amber accent
══════════════════════════════════════════ */
.drv-sidebar {
    width: 248px;
    min-width: 248px;
    background: linear-gradient(180deg,#0d1117 0%,#1a1f2e 40%,#0d1117 100%);
    background-size: 200% 300%;
    animation: gradShift 12s ease infinite;
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    border-right: 1px solid rgba(255,255,255,.06);
    z-index: 100;
}

.drv-brand {
    padding: 22px 18px 18px;
    border-bottom: 1px solid rgba(255,255,255,.07);
    display: flex; align-items: center; gap: 12px;
    animation: slideLeft .5s ease;
}
.drv-brand__icon {
    width: 44px; height: 44px;
    background: linear-gradient(135deg,#f59e0b,#ef4444);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #fff; flex-shrink: 0;
    animation: ambientGlow 2.5s infinite;
}
.drv-brand strong { color: #fef3c7; font-size: 14px; display: block; line-height: 1.3; }
.drv-brand small  { color: #64748b; font-size: 11px; }

/* ── Driver profile ── */
.drv-profile {
    padding: 14px 18px;
    border-bottom: 1px solid rgba(255,255,255,.07);
    display: flex; align-items: center; gap: 10px;
    animation: slideLeft .6s ease;
}
.drv-avatar {
    width: 38px; height: 38px;
    background: linear-gradient(135deg,#f59e0b,#2563eb);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 16px; font-weight: 700; flex-shrink: 0;
    animation: blueGlow 3s infinite;
}
.drv-profile strong { color: #f1f5f9; font-size: 13px; display: block; }
.drv-profile small  { color: #64748b; font-size: 11px; }

/* ── Ambulance status chip in sidebar ── */
.drv-amb-chip {
    margin: 10px 18px;
    padding: 8px 12px;
    border-radius: 10px;
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; font-weight: 600;
    border: 1px solid rgba(255,255,255,.08);
    animation: fadeUp .7s ease;
}
.drv-amb-chip .dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
    animation: softPulse 2s infinite;
}

/* ── Nav ── */
.drv-nav {
    padding: 12px 10px;
    flex: 1;
    display: flex; flex-direction: column; gap: 3px;
}
.drv-nav a {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 12px; border-radius: 9px;
    color: #64748b; font-size: 13px; font-weight: 500;
    text-decoration: none; transition: all .2s;
    position: relative; overflow: hidden;
}
.drv-nav a::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(90deg,rgba(245,158,11,.14),rgba(37,99,235,.12));
    opacity: 0; transition: opacity .2s; border-radius: 9px;
}
.drv-nav a:hover::before, .drv-nav a.active::before { opacity: 1; }
.drv-nav a:hover { color: #f1f5f9; }
.drv-nav a.active {
    color: #fbbf24;
    border-left: 3px solid #f59e0b;
    background: linear-gradient(90deg,rgba(245,158,11,.18),rgba(37,99,235,.1));
}
.drv-nav a i { width: 16px; text-align: center; font-size: 14px; }

.drv-nav-badge {
    margin-left: auto;
    background: #f59e0b; color: #0d1117;
    font-size: 10px; font-weight: 800;
    padding: 2px 7px; border-radius: 20px;
    animation: softPulse 1.8s infinite;
}

/* ── Logout ── */
.drv-logout {
    padding: 12px 10px;
    border-top: 1px solid rgba(255,255,255,.07);
}
.drv-logout button {
    width: 100%; padding: 9px 12px; border-radius: 9px;
    background: rgba(239,68,68,.12);
    color: #fca5a5; border: 1px solid rgba(239,68,68,.25);
    font-size: 13px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    transition: all .2s;
}
.drv-logout button:hover { background: rgba(239,68,68,.25); color: #fff; }
</style>

<style>
/* ══════════════════════════════════════════
   MAIN CONTENT
══════════════════════════════════════════ */
.drv-main { flex:1; display:flex; flex-direction:column; overflow-y:auto; min-width:0; background:#f0f4f8; }

/* ── Top bar ── */
.drv-topbar {
    background: linear-gradient(135deg,#1a1f2e,#0d1117);
    background-size: 200% 200%;
    animation: gradShift 8s ease infinite;
    padding: 16px 28px;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top:0; z-index:50;
    border-bottom: 1px solid rgba(245,158,11,.2);
}
.drv-topbar__left h1 {
    font-size: 18px; font-weight: 700; color: #fef3c7; margin: 0;
    display: flex; align-items: center; gap: 10px;
}
.drv-topbar__left small { font-size: 11px; color: #64748b; display: block; margin-top: 3px; }
.drv-topbar__right { display: flex; align-items: center; gap: 10px; }

/* ── Live pill ── */
.drv-live-pill {
    display: flex; align-items: center; gap: 6px;
    background: rgba(245,158,11,.12);
    border: 1px solid rgba(245,158,11,.3);
    border-radius: 20px; padding: 5px 12px;
    font-size: 12px; font-weight: 600; color: #fbbf24;
}
.drv-live-pill .dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #f59e0b; position: relative;
}
.drv-live-pill .dot::after {
    content: ''; position: absolute; inset: 0;
    border-radius: 50%; background: #f59e0b;
    animation: ping 1.4s infinite;
}

/* ── Send location button ── */
.drv-loc-btn {
    padding: 8px 16px; border-radius: 9px;
    background: linear-gradient(135deg,#2563eb,#1d4ed8);
    background-size: 200% 200%;
    animation: gradShift 4s ease infinite, blueGlow 2s infinite;
    color: #fff; border: none; cursor: pointer;
    font-size: 12px; font-weight: 700;
    display: flex; align-items: center; gap: 7px;
    transition: transform .15s;
}
.drv-loc-btn:hover { transform: scale(1.04); }

/* ── Content ── */
.drv-content { padding: 22px 26px; display: flex; flex-direction: column; gap: 24px; }

/* ── Section title ── */
.drv-section-title {
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 14px; animation: fadeUp .5s ease;
}
.drv-section-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: #fff; flex-shrink: 0;
}
.drv-section-title h2 { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; }
.drv-section-title small { font-size: 12px; color: #64748b; display: block; margin-top: 2px; }

/* ── Metric cards ── */
.drv-metrics { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; }
.drv-metric {
    border-radius: 14px; padding: 18px 16px;
    display: flex; align-items: center; gap: 14px;
    position: relative; overflow: hidden;
    animation: fadeUp .5s ease both;
    transition: transform .2s, box-shadow .2s;
    background: linear-gradient(135deg,var(--mc1),var(--mc2));
    background-size: 200% 200%;
    animation: gradShift 6s ease infinite, fadeUp .5s ease both;
    color: #fff;
}
.drv-metric:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(0,0,0,.18); }
.drv-metric::after {
    content: ''; position: absolute; top:-28px; right:-28px;
    width: 90px; height: 90px; border-radius: 50%;
    background: rgba(255,255,255,.08);
}
.drv-metric__icon {
    width: 44px; height: 44px; border-radius: 11px;
    background: rgba(255,255,255,.18);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.drv-metric p  { font-size: 11px; opacity: .8; margin: 0 0 3px; }
.drv-metric strong { font-size: 22px; font-weight: 800; display: block; animation: countPop .6s ease both; }
.drv-metric small  { font-size: 10px; opacity: .7; }

/* ── Card ── */
.drv-card {
    background: #fff; border-radius: 14px; border: 1px solid #e2e8f0;
    overflow: hidden; animation: fadeUp .5s ease both; transition: box-shadow .2s;
}
.drv-card:hover { box-shadow: 0 6px 22px rgba(0,0,0,.07); }
.drv-card-header {
    padding: 15px 20px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
    background: linear-gradient(90deg,#fafafa,#fff);
}
.drv-card-header h3 { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0; }
.drv-card-header small { font-size: 11px; color: #94a3b8; }
.drv-card-body { padding: 20px; }

/* ── Assignment card ── */
.drv-assignment {
    border-radius: 12px; overflow: hidden;
    border: 1px solid #e2e8f0;
    animation: fadeUp .5s ease both;
}
.drv-assignment__header {
    background: linear-gradient(135deg,#1a1f2e,#0d1117);
    background-size: 200% 200%;
    animation: gradShift 8s ease infinite;
    padding: 16px 20px;
    display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
}
.drv-assignment__header h3 { font-size: 15px; font-weight: 700; color: #fef3c7; margin: 0 0 4px; }
.drv-assignment__header p  { font-size: 12px; color: #94a3b8; margin: 0; }
.drv-assignment__body { background: #fff; padding: 18px 20px; }

/* ── Patient info grid ── */
.drv-patient-grid {
    display: grid; grid-template-columns: repeat(2,1fr); gap: 10px;
    margin-bottom: 16px;
}
.drv-patient-cell {
    background: #f8fafc; border-radius: 9px; padding: 10px 13px;
    border: 1px solid #e2e8f0; transition: transform .2s, box-shadow .2s;
}
.drv-patient-cell:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.06); }
.drv-patient-cell span  { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; display: block; margin-bottom: 3px; }
.drv-patient-cell strong { font-size: 13px; color: #0f172a; font-weight: 600; }

/* ── Action buttons ── */
.drv-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.drv-btn {
    padding: 10px 18px; border-radius: 9px; border: none; cursor: pointer;
    font-size: 13px; font-weight: 700;
    display: flex; align-items: center; gap: 7px;
    transition: transform .15s, box-shadow .15s;
    background-size: 200% 200%;
    animation: gradShift 5s ease infinite;
    color: #fff;
}
.drv-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.2); }
.drv-btn--blue   { background: linear-gradient(135deg,#2563eb,#1d4ed8); }
.drv-btn--green  { background: linear-gradient(135deg,#059669,#0d9488); }
.drv-btn--amber  { background: linear-gradient(135deg,#f59e0b,#d97706); }
.drv-btn--full   { width: 100%; justify-content: center; }

/* ── Form ── */
.drv-form { display: flex; flex-direction: column; gap: 14px; }
.drv-form label {
    font-size: 11px; font-weight: 700; color: #374151;
    text-transform: uppercase; letter-spacing: .04em;
    display: block; margin-bottom: 4px;
}
.drv-form input, .drv-form select, .drv-form textarea {
    width: 100%; padding: 10px 13px;
    border: 1.5px solid #d1d5db; border-radius: 9px;
    font-size: 13px; color: #111827; outline: none;
    transition: border-color .2s, box-shadow .2s;
    background: #fafafa; box-sizing: border-box;
}
.drv-form input:focus, .drv-form select:focus, .drv-form textarea:focus {
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245,158,11,.12);
    background: #fff;
}
.drv-form textarea { resize: vertical; min-height: 80px; }
.drv-form small { font-size: 11px; color: #94a3b8; margin-top: 2px; display: block; }

/* ── Progress bar ── */
.drv-bar-wrap { background: #f1f5f9; border-radius: 20px; height: 8px; overflow: hidden; }
.drv-bar {
    height: 100%; border-radius: 20px;
    animation: barFill 1s ease forwards;
    background: linear-gradient(90deg,var(--bar-c1),var(--bar-c2));
    background-size: 200% 200%;
    animation: gradShift 3s ease infinite, barFill 1s ease forwards;
}

/* ── Maintenance record ── */
.drv-record {
    padding: 13px 20px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    transition: background .15s;
}
.drv-record:last-child { border-bottom: none; }
.drv-record:hover { background: #fafafa; }
.drv-record__info strong { font-size: 13px; color: #0f172a; font-weight: 600; display: block; }
.drv-record__info small  { font-size: 11px; color: #64748b; display: block; margin-top: 2px; }

/* ── Notification card ── */
.drv-notif {
    padding: 14px 20px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
    transition: background .15s; animation: fadeUp .4s ease both;
}
.drv-notif:last-child { border-bottom: none; }
.drv-notif:hover { background: #fafafa; }
.drv-notif__info strong { font-size: 13px; color: #0f172a; font-weight: 600; display: block; }
.drv-notif__info small  { font-size: 11px; color: #64748b; display: block; margin-top: 2px; }

/* ── Map ── */
.drv-map { height: 420px; border-radius: 0 0 14px 14px; }

/* ── Badges ── */
.drv-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 11px; border-radius: 20px;
    font-size: 11px; font-weight: 700;
}
.drv-badge--green  { background:#dcfce7; color:#166534; }
.drv-badge--blue   { background:#dbeafe; color:#1e40af; }
.drv-badge--amber  { background:#fef3c7; color:#92400e; }
.drv-badge--purple { background:#ede9fe; color:#5b21b6; }
.drv-badge--red    { background:#fee2e2; color:#991b1b; }
.drv-badge--slate  { background:#f1f5f9; color:#475569; }

/* ── Alert ── */
.drv-alert {
    padding: 13px 16px; border-radius: 11px;
    display: flex; align-items: flex-start; gap: 10px;
    font-size: 13px; animation: fadeUp .4s ease;
    margin: 0 26px;
}
.drv-alert--success { background:#f0fdf4; border:1px solid #86efac; color:#166534; }
.drv-alert--error   { background:#fef2f2; border:1px solid #fca5a5; color:#dc2626; }

/* ── Divider ── */
.drv-divider {
    height: 1px; background: linear-gradient(90deg,transparent,#e2e8f0,transparent);
    margin: 18px 0;
}

/* ── Empty state ── */
.drv-empty {
    padding: 32px; text-align: center; color: #94a3b8; font-size: 13px;
}
.drv-empty i { font-size: 32px; display: block; margin-bottom: 10px; opacity: .5; }

/* ── Responsive ── */
@media (max-width:1100px) { .drv-metrics { grid-template-columns: repeat(2,1fr); } }
@media (max-width:900px)  { .drv-sidebar { display:none; } .drv-content { padding:14px; } }
</style>

<div class="drv-shell">

    {{-- ══════════ SIDEBAR ══════════ --}}
    <aside class="drv-sidebar">

        {{-- Brand --}}
        <div class="drv-brand">
            <div class="drv-brand__icon"><i class="fa-solid fa-truck-medical"></i></div>
            <div>
                <strong>Nyarugenge EMS</strong>
                <small>Driver Operations</small>
            </div>
        </div>

        {{-- Driver profile --}}
        <div class="drv-profile">
            <div class="drv-avatar">{{ strtoupper(substr($driver->name,0,1)) }}</div>
            <div>
                <strong>{{ $driver->name }}</strong>
                <small>{{ $driver->email }}</small>
            </div>
        </div>

        {{-- Ambulance status chip --}}
        @if($ambulance)
            <div class="drv-amb-chip" style="background:rgba(255,255,255,.04);color:#f1f5f9;">
                <span class="dot" style="background:{{ $ambStatusColor['bg'] }};"></span>
                <div>
                    <div style="font-size:13px;font-weight:700;">{{ $ambulance->ambulance_number }}</div>
                    <div style="font-size:10px;color:#94a3b8;">{{ $ambStatusColor['label'] }}</div>
                </div>
            </div>
        @endif

        {{-- Nav --}}
        <nav class="drv-nav">
            <a href="#overview" class="active">
                <i class="fa-solid fa-gauge-high"></i> Overview
            </a>
            <a href="#assignment">
                <i class="fa-solid fa-route"></i> Assignment
                @if($assignedRequests->count())
                    <span class="drv-nav-badge">{{ $assignedRequests->count() }}</span>
                @endif
            </a>
            <a href="#vehicle">
                <i class="fa-solid fa-truck-medical"></i> Vehicle Status
            </a>
            <a href="#maintenance">
                <i class="fa-solid fa-screwdriver-wrench"></i> Report Issue
            </a>
            <a href="#map">
                <i class="fa-solid fa-map-location-dot"></i> Live Map
            </a>
            <a href="#notifications">
                <i class="fa-solid fa-bell"></i> Notifications
                @if($notifications->where('read_at',null)->count())
                    <span class="drv-nav-badge">{{ $notifications->where('read_at',null)->count() }}</span>
                @endif
            </a>
        </nav>

        {{-- Logout --}}
        <div class="drv-logout">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>

    </aside>

    {{-- ══════════ MAIN ══════════ --}}
    <div class="drv-main">

        {{-- Top bar --}}
        <div class="drv-topbar">
            <div class="drv-topbar__left">
                <h1>
                    <i class="fa-solid fa-steering-wheel" style="color:#f59e0b;"></i>
                    Driver Dashboard
                </h1>
                <small>Welcome, {{ $driver->name }} — Nyarugenge Hospital EMS</small>
            </div>
            <div class="drv-topbar__right">
                <div class="drv-live-pill">
                    <span class="dot"></span> Live
                </div>
                <button class="drv-loc-btn" id="sendLocationBtn">
                    <i class="fa-solid fa-location-crosshairs"></i> Send My Location
                </button>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div style="padding:14px 26px 0;">
                <div class="drv-alert drv-alert--success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if($errors->any())
            <div style="padding:14px 26px 0;">
                <div class="drv-alert drv-alert--error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>@foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach</div>
                </div>
            </div>
        @endif

        <div class="drv-content">

            {{-- ── 1. OVERVIEW METRICS ── --}}
            <div id="overview">
                <div class="drv-section-title">
                    <div class="drv-section-icon" style="background:linear-gradient(135deg,#f59e0b,#ef4444);">
                        <i class="fa-solid fa-gauge-high"></i>
                    </div>
                    <div><h2>Overview</h2><small>Your current operational status</small></div>
                </div>

                <div class="drv-metrics">
                    {{-- Trip status --}}
                    <div class="drv-metric" style="--mc1:#1a1f2e;--mc2:#0d1117;animation-delay:.05s">
                        <div class="drv-metric__icon"><i class="fa-solid fa-route"></i></div>
                        <div>
                            <p>Trip Status</p>
                            <strong style="font-size:16px;">
                                {{ $activeRequest ? str_replace('_',' ',ucfirst($activeRequest->status)) : 'Standby' }}
                            </strong>
                            <small>{{ $activeRequest ? $activeRequest->pickup_location : 'No active assignment' }}</small>
                        </div>
                    </div>

                    {{-- Fuel --}}
                    @php $fuel = $ambulance->fuel_level ?? 0; @endphp
                    <div class="drv-metric" style="--mc1:#065f46;--mc2:#059669;animation-delay:.1s">
                        <div class="drv-metric__icon"><i class="fa-solid fa-gas-pump"></i></div>
                        <div style="flex:1;min-width:0;">
                            <p>Fuel Level</p>
                            <strong>{{ $fuel }}%</strong>
                            <div class="drv-bar-wrap" style="margin-top:6px;">
                                <div class="drv-bar" style="--bar-w:{{ $fuel }}%;--bar-c1:#34d399;--bar-c2:#059669;width:{{ $fuel }}%;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Battery --}}
                    @php $bat = $ambulance->battery_level ?? 0; @endphp
                    <div class="drv-metric" style="--mc1:#1e3a8a;--mc2:#2563eb;animation-delay:.15s">
                        <div class="drv-metric__icon"><i class="fa-solid fa-car-battery"></i></div>
                        <div style="flex:1;min-width:0;">
                            <p>Battery</p>
                            <strong>{{ $bat }}%</strong>
                            <div class="drv-bar-wrap" style="margin-top:6px;">
                                <div class="drv-bar" style="--bar-w:{{ $bat }}%;--bar-c1:#93c5fd;--bar-c2:#2563eb;width:{{ $bat }}%;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Next service --}}
                    <div class="drv-metric" style="--mc1:#78350f;--mc2:#d97706;animation-delay:.2s">
                        <div class="drv-metric__icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                        <div>
                            <p>Next Service</p>
                            @if($serviceDays !== null)
                                <strong style="font-size:16px;">
                                    {{ $serviceDays >= 0 ? 'In '.$serviceDays.'d' : abs($serviceDays).'d late' }}
                                </strong>
                                <small>{{ $serviceDue->format('M d, Y') }}</small>
                            @else
                                <strong style="font-size:14px;">Not set</strong>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── 2. CURRENT ASSIGNMENT ── --}}
            <div id="assignment">
                <div class="drv-section-title">
                    <div class="drv-section-icon" style="background:linear-gradient(135deg,#2563eb,#7c3aed);">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <div><h2>Current Assignment</h2><small>Active patient pickup request</small></div>
                </div>

                @forelse($assignedRequests as $request)
                    <div class="drv-assignment" style="margin-bottom:14px;">
                        <div class="drv-assignment__header">
                            <div>
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                                    <span class="drv-badge {{ $request->status==='on_route' ? 'drv-badge--blue' : 'drv-badge--amber' }}">
                                        <i class="fa-solid fa-circle" style="font-size:7px;"></i>
                                        {{ str_replace('_',' ',ucfirst($request->status)) }}
                                    </span>
                                    <span style="font-size:11px;color:#64748b;">{{ $request->emergency_type ?? 'Medical emergency' }}</span>
                                </div>
                                <h3>{{ $request->pickup_location }}</h3>
                                <p>{{ $request->created_at->diffForHumans() }}</p>
                            </div>
                            @if($request->pickup_lat && $request->pickup_lng)
                                <a href="https://www.google.com/maps?q={{ $request->pickup_lat }},{{ $request->pickup_lng }}"
                                   target="_blank"
                                   style="background:rgba(245,158,11,.2);border:1px solid rgba(245,158,11,.4);
                                          color:#fbbf24;padding:7px 12px;border-radius:8px;font-size:12px;
                                          font-weight:600;text-decoration:none;white-space:nowrap;
                                          display:flex;align-items:center;gap:6px;">
                                    <i class="fa-solid fa-map"></i> Navigate
                                </a>
                            @endif
                        </div>

                        <div class="drv-assignment__body">
                            <div class="drv-patient-grid">
                                <div class="drv-patient-cell">
                                    <span><i class="fa-solid fa-user" style="color:#2563eb;"></i> Patient</span>
                                    <strong>{{ $request->patient->name ?? 'Patient #'.$request->patient_id }}</strong>
                                </div>
                                <div class="drv-patient-cell">
                                    <span><i class="fa-solid fa-phone" style="color:#059669;"></i> Phone</span>
                                    <strong>{{ $request->patient_phone ?? $request->patient->phone ?? 'No phone' }}</strong>
                                </div>
                                <div class="drv-patient-cell">
                                    <span><i class="fa-solid fa-heart-pulse" style="color:#ef4444;"></i> Condition</span>
                                    <strong>{{ $request->notes ?? 'Not specified' }}</strong>
                                </div>
                                <div class="drv-patient-cell">
                                    <span><i class="fa-solid fa-location-dot" style="color:#f59e0b;"></i> Coordinates</span>
                                    <strong style="font-size:12px;">{{ $request->pickup_lat }}, {{ $request->pickup_lng }}</strong>
                                </div>
                                @if($request->additional_notes)
                                    <div class="drv-patient-cell" style="grid-column:span 2;">
                                        <span><i class="fa-solid fa-note-sticky" style="color:#7c3aed;"></i> Additional Notes</span>
                                        <strong style="white-space:pre-line;font-size:12px;">{{ $request->additional_notes }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="drv-actions">
                                @if($request->status === 'assigned')
                                    <form method="POST" action="{{ route('driver.requests.on-route', $request) }}">
                                        @csrf @method('PATCH')
                                        <button class="drv-btn drv-btn--blue">
                                            <i class="fa-solid fa-truck-fast"></i> Start Pickup
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('driver.requests.complete', $request) }}">
                                    @csrf @method('PATCH')
                                    <button class="drv-btn drv-btn--green">
                                        <i class="fa-solid fa-flag-checkered"></i> Complete Trip
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="drv-card">
                        <div class="drv-empty">
                            <i class="fa-solid fa-circle-check" style="color:#059669;"></i>
                            No active assignment. You are on standby.
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- ── 3. VEHICLE STATUS ── --}}
            <div id="vehicle">
                <div class="drv-section-title">
                    <div class="drv-section-icon" style="background:linear-gradient(135deg,#059669,#0d9488);">
                        <i class="fa-solid fa-truck-medical"></i>
                    </div>
                    <div>
                        <h2>Vehicle Status — {{ $ambulance->ambulance_number ?? 'No ambulance assigned' }}</h2>
                        <small>Update fuel, battery and overall condition</small>
                    </div>
                </div>

                <div class="drv-card">
                    @if($ambulance)
                        <div class="drv-card-header">
                            <h3><i class="fa-solid fa-gauge" style="color:#059669;"></i> Vehicle Readings</h3>
                            <span class="drv-badge" style="background:{{ $ambStatusColor['bg'] }}22;color:{{ $ambStatusColor['bg'] }};border:1px solid {{ $ambStatusColor['bg'] }}44;">
                                {{ $ambStatusColor['label'] }}
                            </span>
                        </div>
                        <div class="drv-card-body">
                            {{-- Visual gauges --}}
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">
                                <div style="background:#f8fafc;border-radius:10px;padding:14px;text-align:center;border:1px solid #e2e8f0;">
                                    <div style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Fuel</div>
                                    <div style="font-size:26px;font-weight:800;color:{{ $fuel >= 50 ? '#059669' : ($fuel >= 20 ? '#d97706' : '#dc2626') }};">{{ $fuel }}%</div>
                                    <div class="drv-bar-wrap" style="margin-top:8px;">
                                        <div class="drv-bar" style="--bar-c1:#34d399;--bar-c2:#059669;width:{{ $fuel }}%;"></div>
                                    </div>
                                </div>
                                <div style="background:#f8fafc;border-radius:10px;padding:14px;text-align:center;border:1px solid #e2e8f0;">
                                    <div style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Battery</div>
                                    <div style="font-size:26px;font-weight:800;color:{{ $bat >= 50 ? '#2563eb' : ($bat >= 20 ? '#d97706' : '#dc2626') }};">{{ $bat }}%</div>
                                    <div class="drv-bar-wrap" style="margin-top:8px;">
                                        <div class="drv-bar" style="--bar-c1:#93c5fd;--bar-c2:#2563eb;width:{{ $bat }}%;"></div>
                                    </div>
                                </div>
                                <div style="background:#f8fafc;border-radius:10px;padding:14px;text-align:center;border:1px solid #e2e8f0;">
                                    <div style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Condition</div>
                                    <div style="font-size:16px;font-weight:700;color:#0f172a;margin-top:6px;">{{ $ambulance->overall_condition }}</div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('driver.vehicle.status') }}" class="drv-form">
                                @csrf @method('PATCH')
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                                    <div>
                                        <label>Fuel Level (%)</label>
                                        <input type="number" name="fuel_level" min="0" max="100" value="{{ $ambulance->fuel_level }}" required>
                                    </div>
                                    <div>
                                        <label>Battery Level (%)</label>
                                        <input type="number" name="battery_level" min="0" max="100" value="{{ $ambulance->battery_level }}" required>
                                    </div>
                                </div>
                                <div>
                                    <label>Overall Condition</label>
                                    <select name="overall_condition" required>
                                        @foreach(['Excellent','Good','Fair','Needs attention','Unsafe'] as $cond)
                                            <option value="{{ $cond }}" @selected($ambulance->overall_condition===$cond)>{{ $cond }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="drv-btn drv-btn--green drv-btn--full">
                                    <i class="fa-solid fa-floppy-disk"></i> Save Vehicle Readings
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="drv-empty">
                            <i class="fa-solid fa-truck-medical"></i>
                            Admin has not assigned an ambulance to your account.
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── 4. REPORT MAINTENANCE / TECHNICAL CONTROL ── --}}
            <div id="maintenance">
                <div class="drv-section-title">
                    <div class="drv-section-icon" style="background:linear-gradient(135deg,#d97706,#f59e0b);">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                    <div><h2>Report Issue</h2><small>Maintenance or technical control — updates vehicle status</small></div>
                </div>

                @if($ambulance)
                    <div class="drv-card">
                        <div class="drv-card-header">
                            <h3><i class="fa-solid fa-triangle-exclamation" style="color:#d97706;"></i> Maintenance / Technical Control</h3>
                        </div>
                        <div class="drv-card-body">
                            <form method="POST" action="{{ route('driver.maintenance.store') }}" class="drv-form">
                                @csrf
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                                    <div>
                                        <label>Issue Type</label>
                                        <select name="issue" required>
                                            <option value="">Select issue type</option>
                                            <optgroup label="Maintenance">
                                                <option>Engine problem</option>
                                                <option>Brake failure</option>
                                                <option>Tyre puncture</option>
                                                <option>Electrical fault</option>
                                                <option>Oil leak</option>
                                                <option>Coolant issue</option>
                                                <option>Scheduled maintenance</option>
                                                <option>Other maintenance</option>
                                            </optgroup>
                                            <optgroup label="Technical Control">
                                                <option>Annual technical control</option>
                                                <option>Emission test</option>
                                                <option>Safety inspection</option>
                                                <option>Other technical control</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div>
                                        <label>Vehicle Status During Issue</label>
                                        <select name="vehicle_status" required>
                                            <option value="maintenance">Maintenance (unavailable)</option>
                                            <option value="technical_control">Technical Control</option>
                                            <option value="scheduled_maintenance">Scheduled Maintenance</option>
                                            <option value="out_of_service">Out of Service</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label>Additional Notes</label>
                                    <textarea name="notes" placeholder="Describe the issue in detail…">{{ old('notes') }}</textarea>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                                    <div>
                                        <label>Expected Return / Service Due</label>
                                        <input type="date" name="next_service_due_at"
                                               value="{{ $ambulance->next_service_due_at?->format('Y-m-d') }}">
                                        <small>Leave blank to keep current date</small>
                                    </div>
                                    <div>
                                        <label>Technical Control Valid Until</label>
                                        <input type="datetime-local" name="technical_control_until"
                                               value="{{ $ambulance->technical_control_until?->format('Y-m-d\TH:i') }}">
                                        <small>Only fill for technical control reports</small>
                                    </div>
                                </div>
                                <button class="drv-btn drv-btn--amber drv-btn--full">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Report Issue & Update Status
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Maintenance history --}}
                    @if($maintenanceRecords->isNotEmpty())
                        <div class="drv-card" style="margin-top:14px;">
                            <div class="drv-card-header">
                                <h3><i class="fa-solid fa-clock-rotate-left" style="color:#64748b;"></i> Recent Maintenance Records</h3>
                                <small>{{ $maintenanceRecords->count() }} records</small>
                            </div>
                            @foreach($maintenanceRecords as $rec)
                                <div class="drv-record">
                                    <div class="drv-record__info">
                                        <strong>{{ $rec->issue }}</strong>
                                        <small>{{ $rec->notes ?? 'No notes' }}</small>
                                        <small>{{ $rec->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    <span class="drv-badge {{ match($rec->status) {
                                        'reported'    => 'drv-badge--amber',
                                        'pending'     => 'drv-badge--amber',
                                        'in_progress' => 'drv-badge--blue',
                                        'resolved'    => 'drv-badge--green',
                                        default       => 'drv-badge--slate',
                                    } }}">{{ ucfirst($rec->status) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="drv-card">
                        <div class="drv-empty">
                            <i class="fa-solid fa-truck-medical"></i>
                            No ambulance assigned — cannot report issues.
                        </div>
                    </div>
                @endif
            </div>

            {{-- ── 5. LIVE MAP ── --}}
            <div id="map">
                <div class="drv-section-title">
                    <div class="drv-section-icon" style="background:linear-gradient(135deg,#0891b2,#0d9488);">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div>
                        <h2>Live Map</h2>
                        <small id="mapStatus">Detecting your location…</small>
                    </div>
                </div>

                <div class="drv-card">
                    <div class="drv-card-header">
                        <h3><i class="fa-solid fa-location-crosshairs" style="color:#f59e0b;"></i> Your Position &amp; Patient Pickup</h3>
                        <div style="display:flex;align-items:center;gap:14px;">
                            <div style="display:flex;align-items:center;gap:12px;font-size:12px;color:#475569;">
                                <span style="display:flex;align-items:center;gap:5px;">
                                    <span style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block;"></span> You
                                </span>
                                <span style="display:flex;align-items:center;gap:5px;">
                                    <span style="width:10px;height:10px;border-radius:50%;background:#dc2626;display:inline-block;"></span> Patient
                                </span>
                            </div>
                            <button class="drv-btn drv-btn--blue" id="sendLocationBtn" style="padding:7px 14px;font-size:12px;">
                                <i class="fa-solid fa-location-crosshairs"></i> Update My Location
                            </button>
                        </div>
                    </div>
                    <div id="driverMap" class="drv-map"></div>
                </div>
            </div>

            {{-- ── 6. NOTIFICATIONS ── --}}
            <div id="notifications">
                <div class="drv-section-title">
                    <div class="drv-section-icon" style="background:linear-gradient(135deg,#7c3aed,#6366f1);">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div><h2>Dispatch Notifications</h2><small>Messages from the dispatch center</small></div>
                </div>

                <div class="drv-card">
                    @forelse($notifications as $notif)
                        <div class="drv-notif" style="animation-delay:{{ $loop->index * .05 }}s">
                            <div class="drv-notif__info">
                                <strong>{{ $notif->data['title'] ?? 'Dispatch notification' }}</strong>
                                <small>{{ $notif->data['message'] ?? '' }}</small>
                                @if(!empty($notif->data['pickup_location']))
                                    <small>
                                        <i class="fa-solid fa-location-dot" style="color:#f59e0b;"></i>
                                        {{ $notif->data['pickup_location'] }}
                                    </small>
                                @endif
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <span class="drv-badge {{ $notif->read_at ? 'drv-badge--slate' : 'drv-badge--amber' }}">
                                    {{ $notif->read_at ? 'Read' : 'New' }}
                                </span>
                                <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                                    {{ $notif->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="drv-empty">
                            <i class="fa-solid fa-bell-slash"></i>
                            No dispatch notifications yet.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>{{-- end .drv-content --}}
    </div>{{-- end .drv-main --}}
</div>{{-- end .drv-shell --}}

<script>
const DRV_ROUTES = {
    tracking: "{{ route('driver.api.tracking') }}",
    location:  "{{ route('driver.api.location') }}",
    csrf:      "{{ csrf_token() }}",
};

/* ── Icons ── */
const driverIcon = L.divIcon({
    className: '',
    html: `<div style="position:relative;width:38px;height:38px;">
        <div style="position:absolute;inset:0;border-radius:50%;
            background:rgba(245,158,11,.35);animation:ping 1.4s infinite;"></div>
        <div style="position:relative;width:38px;height:38px;border-radius:50%;
            background:linear-gradient(135deg,#f59e0b,#ef4444);
            border:3px solid #fff;box-shadow:0 2px 12px rgba(245,158,11,.5);
            display:flex;align-items:center;justify-content:center;
            color:#fff;font-size:16px;">
            <i class='fa-solid fa-truck-medical'></i>
        </div>
    </div>`,
    iconSize:[38,38], iconAnchor:[19,19], popupAnchor:[0,-22],
});

const patientIcon = L.divIcon({
    className: '',
    html: `<div style="width:32px;height:32px;border-radius:50%;
        background:linear-gradient(135deg,#dc2626,#b91c1c);
        border:3px solid #fff;box-shadow:0 2px 10px rgba(220,38,38,.5);
        display:flex;align-items:center;justify-content:center;
        color:#fff;font-size:14px;">
        <i class='fa-solid fa-user-injured'></i>
    </div>`,
    iconSize:[32,32], iconAnchor:[16,16], popupAnchor:[0,-18],
});

/* ── Map ── */
let driverMap;
let driverMarker = null;
let patientMarker = null;

function bootMap() {
    driverMap = L.map('driverMap').setView([-1.9441, 30.0619], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a>',
    }).addTo(driverMap);
}

function moveMarker(marker, lat, lng) {
    if (marker.getLatLng().distanceTo(L.latLng(lat, lng)) > 0.5) {
        marker.setLatLng([lat, lng]);
    }
}

async function refreshMap() {
    let data;
    try {
        const res = await fetch(DRV_ROUTES.tracking, { headers: { Accept: 'application/json' }, cache: 'no-store' });
        data = await res.json();
    } catch (e) { return; }

    const dl = data.driver_location;
    const pl = data.patient_location;

    if (dl?.latitude && dl?.longitude) {
        const lat = parseFloat(dl.latitude), lng = parseFloat(dl.longitude);
        if (driverMarker) {
            moveMarker(driverMarker, lat, lng);
        } else {
            driverMarker = L.marker([lat, lng], { icon: driverIcon })
                .addTo(driverMap)
                .bindPopup('<strong>Your ambulance</strong>');
        }
    }

    if (pl?.latitude && pl?.longitude) {
        const lat = parseFloat(pl.latitude), lng = parseFloat(pl.longitude);
        if (patientMarker) {
            moveMarker(patientMarker, lat, lng);
        } else {
            patientMarker = L.marker([lat, lng], { icon: patientIcon })
                .addTo(driverMap)
                .bindPopup(`<strong>Patient pickup</strong><br>${pl.pickup_location ?? ''}`);
        }
    }

    const markers = [driverMarker, patientMarker].filter(Boolean);
    if (markers.length > 1) {
        driverMap.fitBounds(L.featureGroup(markers).getBounds().pad(0.3));
    } else if (markers.length === 1) {
        driverMap.setView(markers[0].getLatLng(), 15);
    }

    const now = new Date().toLocaleTimeString();
    document.getElementById('mapStatus').textContent = `Updated ${now}`;
}

/* ── Send location ── */
async function sendLocation() {
    if (!navigator.geolocation) { alert('Geolocation not supported'); return; }

    const btn = document.getElementById('sendLocationBtn');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending…';

    navigator.geolocation.getCurrentPosition(async pos => {
        try {
            await fetch(DRV_ROUTES.location, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': DRV_ROUTES.csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    latitude:  pos.coords.latitude,
                    longitude: pos.coords.longitude,
                    speed:     pos.coords.speed,
                }),
            });
            await refreshMap();
        } catch (e) { console.error(e); }
        btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Send My Location';
    }, () => {
        alert('Unable to access location.');
        btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Send My Location';
    });
}

/* ── Sidebar smooth scroll ── */
document.querySelectorAll('.drv-nav a').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('.drv-nav a').forEach(a => a.classList.remove('active'));
        link.classList.add('active');
        const target = document.querySelector(link.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

/* ── Boot ── */
document.addEventListener('DOMContentLoaded', () => {
    bootMap();
    refreshMap();
    setInterval(refreshMap, 10000);   // refresh map every 10s
    document.getElementById('sendLocationBtn').addEventListener('click', sendLocation);
});
</script>

</x-layouts.app>

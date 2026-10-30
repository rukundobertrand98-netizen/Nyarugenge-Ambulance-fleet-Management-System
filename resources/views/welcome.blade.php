<x-layouts.app>
    <div class="main-content">
        <div class="dashboard-header">
            <h1>Laravel Backend API for Nyarugenge Ambulance</h1>
            <p>Use this application as backend only. The Laravel backend now exposes JSON API endpoints while keeping the hospital UI style available for future frontend integration.</p>
        </div>

        <div class="cards">
            <div class="card">
                <h3>API Status</h3>
                <div class="number">/api/status</div>
            </div>
            <div class="card">
                <h3>Emergency Requests</h3>
                <div class="number">POST /api/emergency-requests</div>
            </div>
            <div class="card">
                <h3>Ambulance Data</h3>
                <div class="number">GET /api/ambulances</div>
            </div>
            <div class="card">
                <h3>Request History</h3>
                <div class="number">GET /api/emergency-requests</div>
            </div>
        </div>

        <div class="request-box">
            <h2>Backend API ready</h2>
            <p>This Laravel application now includes a backend JSON API for ambulance dispatch and emergency request workflows.</p>
            <p>Use the endpoints above to connect a separate frontend or mobile client.</p>
            <a href="/api/status" class="btn-emergency">Check API Status</a>
        </div>
    </div>
</x-layouts.app>

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;
use App\Models\AmbulanceLocation;
use App\Models\EmergencyRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class AdminController extends Controller
{
    private const AMBULANCE_STATUSES = [
        'available',
        'busy',
        'maintenance',
        'technical_control',
        'scheduled_maintenance',
        'out_of_service',
    ];

    public function dashboard()
    {
        $this->ensureAdmin();

        $requests = EmergencyRequest::with([
            'patient',
            'dispatcher',
            'ambulance'
        ])->latest()->take(12)->get();

        $ambulances = Ambulance::with('driver')->latest()->get();
        $drivers = User::where('role', 'driver')->orderBy('name')->get();
        $users = User::orderBy('role')->orderBy('name')->get();

        $stats = $this->buildDashboardPayload();

        return view('admin.admin_dashboard', compact(
            'requests',
            'ambulances',
            'drivers',
            'users',
            'stats'
        ));
    }

    public function overview()
    {
        $this->ensureAdmin();

        return response()->json($this->buildDashboardPayload());
    }

    public function downloadWeeklyReport(Request $request)
    {
        $this->ensureAdmin();

        $reportType = $request->input('report_type', 'weekly');
        $format = $request->input('format', 'pdf');

        if ($format === 'pdf') {
            return match ($reportType) {
                'fleet' => $this->exportFleetReportPdf(),
                'users' => $this->exportUserReportPdf(),
                default => $this->exportWeeklyReportPdf(),
            };
        }

        return match ($reportType) {
            'fleet' => $this->exportFleetReport(),
            'users' => $this->exportUserReport(),
            default => $this->exportWeeklyReport(),
        };
    }

    private function streamExcelDownload(string $filename, callable $callback)
    {
        return response()->streamDownload(function () use ($callback) {
            $writer = new Writer();
            $writer->openToFile('php://output');
            $callback($writer);
            $writer->close();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    private function streamPdfDownload(string $filename, string $title, array $sections)
    {
        return response()->streamDownload(function () use ($title, $sections) {
            echo $this->buildSimplePdf($title, $sections);
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    private function exportWeeklyReport()
    {
        $startOfWeek = now()->subDays(6)->startOfDay();
        $endOfWeek = now()->endOfDay();

        $weeklyRequests = EmergencyRequest::with(['patient', 'dispatcher', 'ambulance'])
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->orderBy('created_at')
            ->get();

        $weeklyTotals = [
            'requests' => $weeklyRequests->count(),
            'completed' => $weeklyRequests->where('status', 'completed')->count(),
            'cancelled' => $weeklyRequests->where('status', 'cancelled')->count(),
            'assigned' => $weeklyRequests->where('status', 'assigned')->count(),
            'on_route' => $weeklyRequests->where('status', 'on_route')->count(),
            'pending' => $weeklyRequests->where('status', 'pending')->count(),
        ];

        $filename = sprintf('nyarugenge-weekly-report-%s.xlsx', now()->format('Ymd'));

        return $this->streamExcelDownload($filename, function (Writer $writer) use ($startOfWeek, $endOfWeek, $weeklyTotals, $weeklyRequests) {
            $writer->addRow(Row::fromValues(['Nyarugenge Ambulance Service', 'Weekly Operational Summary']));
            $writer->addRow(Row::fromValues([]));
            $writer->addRow(Row::fromValues(['Generated At', now()->toDateTimeString()]));
            $writer->addRow(Row::fromValues(['Period Start', $startOfWeek->toDateTimeString()]));
            $writer->addRow(Row::fromValues(['Period End', $endOfWeek->toDateTimeString()]));
            $writer->addRow(Row::fromValues([]));
            $writer->addRow(Row::fromValues(['Metric', 'Count']));
            foreach ($weeklyTotals as $metric => $count) {
                $writer->addRow(Row::fromValues([ucwords(str_replace('_', ' ', $metric)), $count]));
            }
            $writer->addRow(Row::fromValues([]));
            $writer->addRow(Row::fromValues(['Request ID', 'Patient', 'Status', 'Created At', 'Assigned Ambulance']));
            foreach ($weeklyRequests as $request) {
                $writer->addRow(Row::fromValues([
                    $request->id,
                    $request->patient?->name ?? 'Unknown',
                    $request->status,
                    $request->created_at?->format('Y-m-d H:i:s'),
                    $request->ambulance?->ambulance_number ?? 'Unassigned',
                ]));
            }
        });
    }

    private function exportWeeklyReportPdf()
    {
        $startOfWeek = now()->subDays(6)->startOfDay();
        $endOfWeek = now()->endOfDay();

        $weeklyRequests = EmergencyRequest::with(['patient', 'dispatcher', 'ambulance'])
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->orderBy('created_at')
            ->get();

        $weeklyTotals = [
            'Requests' => $weeklyRequests->count(),
            'Completed' => $weeklyRequests->where('status', 'completed')->count(),
            'Cancelled' => $weeklyRequests->where('status', 'cancelled')->count(),
            'Assigned' => $weeklyRequests->where('status', 'assigned')->count(),
            'On Route' => $weeklyRequests->where('status', 'on_route')->count(),
            'Pending' => $weeklyRequests->where('status', 'pending')->count(),
        ];

        $filename = sprintf('nyarugenge-weekly-report-%s.pdf', now()->format('Ymd'));

        return $this->streamPdfDownload($filename, 'Weekly Operational Summary', [
            [
                'heading' => 'Report Details',
                'rows' => [
                    ['Generated At', now()->toDateTimeString()],
                    ['Period Start', $startOfWeek->toDateTimeString()],
                    ['Period End', $endOfWeek->toDateTimeString()],
                ],
            ],
            [
                'heading' => 'Summary',
                'rows' => collect($weeklyTotals)->map(fn ($count, $metric) => [$metric, $count])->values()->all(),
            ],
            [
                'heading' => 'Emergency Requests',
                'headers' => ['ID', 'Patient', 'Status', 'Created At', 'Ambulance'],
                'rows' => $weeklyRequests->map(fn ($request) => [
                    $request->id,
                    $request->patient?->name ?? 'Unknown',
                    $request->status,
                    $request->created_at?->format('Y-m-d H:i:s'),
                    $request->ambulance?->ambulance_number ?? 'Unassigned',
                ])->all(),
            ],
        ]);
    }

    private function exportFleetReport()
    {
        $ambulances = Ambulance::with('driver')
            ->orderBy('ambulance_number')
            ->get();

        $filename = sprintf('nyarugenge-fleet-report-%s.xlsx', now()->format('Ymd'));

        return $this->streamExcelDownload($filename, function (Writer $writer) use ($ambulances) {
            $writer->addRow(Row::fromValues(['Nyarugenge Ambulance Service', 'Fleet Status Summary']));
            $writer->addRow(Row::fromValues([]));
            $writer->addRow(Row::fromValues(['Generated At', now()->toDateTimeString()]));
            $writer->addRow(Row::fromValues([]));
            $writer->addRow(Row::fromValues(['Ambulance Number', 'Status', 'Driver', 'Phone', 'Capacity']));

            foreach ($ambulances as $ambulance) {
                $writer->addRow(Row::fromValues([
                    $ambulance->ambulance_number,
                    $ambulance->status,
                    $ambulance->driver?->name ?? 'Unassigned',
                    $ambulance->driver?->phone ?? 'N/A',
                    $ambulance->capacity ?? 'N/A',
                ]));
            }
        });
    }

    private function exportFleetReportPdf()
    {
        $ambulances = Ambulance::with('driver')
            ->orderBy('ambulance_number')
            ->get();

        $filename = sprintf('nyarugenge-fleet-report-%s.pdf', now()->format('Ymd'));

        return $this->streamPdfDownload($filename, 'Fleet Status Summary', [
            [
                'heading' => 'Report Details',
                'rows' => [
                    ['Generated At', now()->toDateTimeString()],
                    ['Total Ambulances', $ambulances->count()],
                ],
            ],
            [
                'heading' => 'Ambulances',
                'headers' => ['Ambulance', 'Status', 'Driver', 'Phone', 'Capacity'],
                'rows' => $ambulances->map(fn ($ambulance) => [
                    $ambulance->ambulance_number,
                    $ambulance->status,
                    $ambulance->driver?->name ?? 'Unassigned',
                    $ambulance->driver?->phone ?? 'N/A',
                    $ambulance->capacity ?? 'N/A',
                ])->all(),
            ],
        ]);
    }

    private function exportUserReport()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        $roleCounts = User::query()
            ->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $filename = sprintf('nyarugenge-user-report-%s.xlsx', now()->format('Ymd'));

        return $this->streamExcelDownload($filename, function (Writer $writer) use ($roleCounts, $users) {
            $writer->addRow(Row::fromValues(['Nyarugenge Ambulance Service', 'User Role Summary']));
            $writer->addRow(Row::fromValues([]));
            $writer->addRow(Row::fromValues(['Generated At', now()->toDateTimeString()]));
            $writer->addRow(Row::fromValues([]));
            $writer->addRow(Row::fromValues(['Role', 'Count']));
            foreach ($roleCounts as $role => $count) {
                $writer->addRow(Row::fromValues([$role, $count]));
            }
            $writer->addRow(Row::fromValues([]));
            $writer->addRow(Row::fromValues(['Name', 'Email', 'Phone', 'Role']));
            foreach ($users as $user) {
                $writer->addRow(Row::fromValues([$user->name, $user->email, $user->phone ?? '-', $user->role]));
            }
        });
    }

    private function exportUserReportPdf()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        $roleCounts = User::query()
            ->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $filename = sprintf('nyarugenge-user-report-%s.pdf', now()->format('Ymd'));

        return $this->streamPdfDownload($filename, 'User Role Summary', [
            [
                'heading' => 'Report Details',
                'rows' => [
                    ['Generated At', now()->toDateTimeString()],
                    ['Total Users', $users->count()],
                ],
            ],
            [
                'heading' => 'Role Counts',
                'rows' => $roleCounts->map(fn ($count, $role) => [ucfirst($role), $count])->values()->all(),
            ],
            [
                'heading' => 'Users',
                'headers' => ['Name', 'Email', 'Phone', 'Role'],
                'rows' => $users->map(fn ($user) => [
                    $user->name,
                    $user->email,
                    $user->phone ?? '-',
                    $user->role,
                ])->all(),
            ],
        ]);
    }

    public function updateAmbulanceStatus(Request $request, Ambulance $ambulance)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'status' => ['required', Rule::in(self::AMBULANCE_STATUSES)],
        ]);

        $ambulance->update($data);

        return response()->json([
            'message' => 'Ambulance status updated successfully.',
            'ambulance' => $ambulance->fresh('driver'),
        ]);
    }

    public function storeUser(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'role' => 'required|in:admin,dispatcher,driver,patient',
            'password' => 'required|string|min:6',
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()
            ->to(route('admin.dashboard').'#users')
            ->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'role' => 'required|in:admin,dispatcher,driver,patient',
            'password' => 'nullable|string|min:6',
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->to(route('admin.dashboard').'#users')
            ->with('success', 'User updated successfully.');
    }

    public function destroyUser(User $user)
    {
        $this->ensureAdmin();

        if ($user->id === Auth::id()) {
            return redirect()
                ->to(route('admin.dashboard').'#users')
                ->withErrors(['user' => 'You cannot delete your own admin account.']);
        }

        $user->delete();

        return redirect()
            ->to(route('admin.dashboard').'#users')
            ->with('success', 'User deleted successfully.');
    }

    public function storeAmbulance(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'ambulance_number' => 'required|string|max:255|unique:ambulances,ambulance_number',
            'plate_number' => 'required|string|max:255|unique:ambulances,plate_number',
            'status' => ['required', Rule::in(self::AMBULANCE_STATUSES)],
            'driver_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', 'driver'),
                Rule::unique('ambulances', 'driver_id'),
            ],
        ]);

        Ambulance::create($data);

        return redirect()
            ->to(route('admin.dashboard').'#fleet')
            ->with('success', 'Ambulance added successfully.');
    }

    public function updateAmbulance(Request $request, Ambulance $ambulance)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'ambulance_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ambulances', 'ambulance_number')->ignore($ambulance->id),
            ],
            'plate_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ambulances', 'plate_number')->ignore($ambulance->id),
            ],
            'status' => ['required', Rule::in(self::AMBULANCE_STATUSES)],
            'driver_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', 'driver'),
                Rule::unique('ambulances', 'driver_id')->ignore($ambulance->id),
            ],
        ]);

        $ambulance->update($data);

        return redirect()
            ->to(route('admin.dashboard').'#fleet')
            ->with('success', 'Ambulance updated successfully.');
    }

    public function destroyAmbulance(Ambulance $ambulance)
    {
        $this->ensureAdmin();

        $ambulance->delete();

        return redirect()
            ->to(route('admin.dashboard').'#fleet')
            ->with('success', 'Ambulance deleted successfully.');
    }

    private function ensureAdmin(): void
    {
        if (Auth::user()?->role !== 'admin') {
            abort(403, 'Unauthorized Access');
        }
    }

    private function buildSimplePdf(string $title, array $sections): string
    {
        $pages = [[]];
        $currentPage = 0;
        $y = 790;

        $addPage = function () use (&$pages, &$currentPage, &$y) {
            $pages[] = [];
            $currentPage++;
            $y = 790;
        };

        $addRect = function (int $x, int $y, int $width, int $height, string $fill, string $stroke = '0 0 0') use (&$pages, &$currentPage) {
            $pages[$currentPage][] = sprintf(
                'q %.2f %.2f %.2f rg %.2f %.2f %.2f RG %d %d %d %d re B Q',
                ...array_merge(explode(' ', $fill), explode(' ', $stroke), [$x, $y, $width, $height])
            );
        };

        $addLine = function (string $text, int $size = 10, bool $bold = false, int $indent = 0, int $x = 48) use (&$pages, &$currentPage, &$y, $addPage) {
            if ($y < 54) {
                $addPage();
            }

            $font = $bold ? 'F2' : 'F1';
            $pages[$currentPage][] = sprintf(
                'BT /%s %d Tf %d %d Td (%s) Tj ET',
                $font,
                $size,
                $x + $indent,
                $y,
                $this->escapePdfText($text)
            );
            $y -= $size + 6;
        };

        $addWrappedLine = function (string $text, int $size = 10, bool $bold = false, int $indent = 0, int $x = 48) use (&$pages, &$currentPage, &$y, $addLine) {
            $limit = max(35, 98 - (int) floor($indent / 5));
            foreach (explode("\n", wordwrap($text, $limit, "\n", true)) as $line) {
                $addLine($line, $size, $bold, $indent, $x);
            }
        };

        $addDivider = function (int $x1, int $y1, int $x2, int $y2, string $color = '0.78 0.82 0.89') use (&$pages, &$currentPage) {
            $pages[$currentPage][] = sprintf('q %s RG %d %d m %d %d l S Q', $color, $x1, $y1, $x2, $y2);
        };

        $addRect(26, 770, 543, 58, '0.11 0.27 0.52', '0.11 0.27 0.52');
        $addRect(26, 710, 543, 18, '0.93 0.96 1', '0.93 0.96 1');

        $pages[$currentPage][] = 'BT /F2 16 Tf 40 800 Td (Nyarugenge Ambulance Service) Tj ET';
        $pages[$currentPage][] = 'BT /F1 10 Tf 40 782 Td (Operational Report) Tj ET';
        $pages[$currentPage][] = 'BT /F1 9 Tf 40 768 Td (Generated At: '. $this->escapePdfText(now()->toDateTimeString()) .') Tj ET';
        $pages[$currentPage][] = 'BT /F1 9 Tf 40 754 Td (Report Title: '. $this->escapePdfText($title) .') Tj ET';
        $y = 730;

        foreach ($sections as $section) {
            if ($y < 120) {
                $addPage();
                $y = 790;
            }

            $addRect(32, $y, 520, 18, '0.92 0.96 1', '0.17 0.24 0.39');
            $pages[$currentPage][] = sprintf('BT /F2 11 Tf 42 %d Td (%s) Tj ET', $y + 12, $this->escapePdfText($section['heading']));
            $y -= 28;

            if (! empty($section['headers'])) {
                $addDivider(42, $y + 8, 548, $y + 8, '0.69 0.77 0.88');
                $addWrappedLine(implode('  |  ', $section['headers']), 8, true, 8, 42);
                $y -= 18;
            }

            $rows = $section['rows'] ?? [];
            if (empty($rows)) {
                $addWrappedLine('No records found.', 9, false, 12, 42);
                $y -= 12;
            } else {
                foreach ($rows as $row) {
                    $addWrappedLine(implode('  |  ', array_map(fn ($value) => (string) $value, $row)), 8, false, 8, 42);
                    $y -= 14;
                }
            }

            $addDivider(42, $y, 548, $y, '0.82 0.87 0.93');
            $y -= 18;
        }

        return $this->compilePdf($pages);
    }

    private function escapePdfText(string $text): string
    {
        $text = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text) ?: $text;
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $text);

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function compilePdf(array $pages): string
    {
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>',
        ];

        $pageObjectIds = [];
        $nextObjectId = 5;

        foreach ($pages as $commands) {
            $pageObjectId = $nextObjectId++;
            $contentObjectId = $nextObjectId++;
            $pageObjectIds[] = $pageObjectId;
            $content = implode("\n", $commands);

            $objects[$pageObjectId] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents %d 0 R >>',
                $contentObjectId
            );
            $objects[$contentObjectId] = "<< /Length ".strlen($content)." >>\nstream\n{$content}\nendstream";
        }

        $objects[2] = sprintf(
            '<< /Type /Pages /Kids [%s] /Count %d >>',
            implode(' ', array_map(fn ($id) => "{$id} 0 R", $pageObjectIds)),
            count($pageObjectIds)
        );

        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0 => 0];

        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$body}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($id = 1; $id <= count($objects); $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    private function buildDashboardPayload(): array
    {
        $requestStatuses = EmergencyRequest::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $ambulanceStatuses = Ambulance::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $roleCounts = User::query()
            ->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $trendStart = now()->subDays(6)->startOfDay();
        $requestTrend = EmergencyRequest::query()
            ->where('created_at', '>=', $trendStart)
            ->selectRaw('DATE(created_at) as request_date, COUNT(*) as total')
            ->groupBy('request_date')
            ->orderBy('request_date')
            ->pluck('total', 'request_date');

        $trend = collect(range(0, 6))->map(function ($day) use ($trendStart, $requestTrend) {
            $date = $trendStart->copy()->addDays($day);

            return [
                'label' => $date->format('D'),
                'date' => $date->toDateString(),
                'total' => (int) ($requestTrend[$date->toDateString()] ?? 0),
            ];
        })->values();

        $latestLocations = AmbulanceLocation::query()
            ->with('ambulance.driver')
            ->latest('recorded_at')
            ->get()
            ->unique('ambulance_id')
            ->values()
            ->map(function (AmbulanceLocation $location) {
                return [
                    'ambulance_id' => $location->ambulance_id,
                    'ambulance_number' => $location->ambulance?->ambulance_number,
                    'plate_number' => $location->ambulance?->plate_number,
                    'driver' => $location->ambulance?->driver?->name,
                    'status' => $location->ambulance?->status,
                    'latitude' => (float) $location->latitude,
                    'longitude' => (float) $location->longitude,
                    'speed' => $location->speed ? (float) $location->speed : null,
                    'recorded_at' => optional($location->recorded_at)->toDateTimeString(),
                ];
            });

        $pending = (int) ($requestStatuses['pending'] ?? 0);
        $active = (int) ($requestStatuses['assigned'] ?? 0) + (int) ($requestStatuses['on_route'] ?? 0);
        $completedToday = EmergencyRequest::whereDate('updated_at', Carbon::today())
            ->where('status', 'completed')
            ->count();

        return [
            'totals' => [
                'users' => User::count(),
                'patients' => (int) ($roleCounts['patient'] ?? 0),
                'dispatchers' => (int) ($roleCounts['dispatcher'] ?? 0),
                'drivers' => (int) ($roleCounts['driver'] ?? 0),
                'admins' => (int) ($roleCounts['admin'] ?? 0),
                'requests' => EmergencyRequest::count(),
                'pending_requests' => $pending,
                'active_requests' => $active,
                'completed_today' => $completedToday,
                'ambulances' => Ambulance::count(),
                'available_ambulances' => (int) ($ambulanceStatuses['available'] ?? 0),
                'busy_ambulances' => (int) ($ambulanceStatuses['busy'] ?? 0),
                'maintenance_ambulances' => (int) ($ambulanceStatuses['maintenance'] ?? 0),
                'technical_control_ambulances' => (int) ($ambulanceStatuses['technical_control'] ?? 0),
                'scheduled_maintenance_ambulances' => (int) ($ambulanceStatuses['scheduled_maintenance'] ?? 0),
                'out_of_service_ambulances' => (int) ($ambulanceStatuses['out_of_service'] ?? 0),
            ],
            'request_statuses' => [
                'pending' => $pending,
                'assigned' => (int) ($requestStatuses['assigned'] ?? 0),
                'on_route' => (int) ($requestStatuses['on_route'] ?? 0),
                'completed' => (int) ($requestStatuses['completed'] ?? 0),
                'cancelled' => (int) ($requestStatuses['cancelled'] ?? 0),
            ],
            'ambulance_statuses' => [
                'available' => (int) ($ambulanceStatuses['available'] ?? 0),
                'busy' => (int) ($ambulanceStatuses['busy'] ?? 0),
                'maintenance' => (int) ($ambulanceStatuses['maintenance'] ?? 0),
                'technical_control' => (int) ($ambulanceStatuses['technical_control'] ?? 0),
                'scheduled_maintenance' => (int) ($ambulanceStatuses['scheduled_maintenance'] ?? 0),
                'out_of_service' => (int) ($ambulanceStatuses['out_of_service'] ?? 0),
            ],
            'role_counts' => [
                'patients' => (int) ($roleCounts['patient'] ?? 0),
                'dispatchers' => (int) ($roleCounts['dispatcher'] ?? 0),
                'drivers' => (int) ($roleCounts['driver'] ?? 0),
                'admins' => (int) ($roleCounts['admin'] ?? 0),
            ],
            'request_trend' => $trend,
            'latest_locations' => $latestLocations,
            'last_updated' => now()->toDateTimeString(),
        ];
    }
}

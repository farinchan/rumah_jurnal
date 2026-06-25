<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class LogsController extends Controller
{
    public function index()
    {
        // Ambil daftar log_name unik dari database
        $logNames = Activity::select('log_name')
            ->distinct()
            ->whereNotNull('log_name')
            ->orderBy('log_name')
            ->pluck('log_name');

        // Ambil daftar event unik dari database
        $events = Activity::select('event')
            ->distinct()
            ->whereNotNull('event')
            ->orderBy('event')
            ->pluck('event');

        // Ambil daftar subject_type unik dari database
        $subjectTypes = Activity::select('subject_type')
            ->distinct()
            ->whereNotNull('subject_type')
            ->orderBy('subject_type')
            ->pluck('subject_type')
            ->map(function ($type) {
                return [
                    'full' => $type,
                    'short' => class_basename($type),
                ];
            });

        // Ambil daftar causer (user) yang pernah melakukan activity
        $causers = User::whereIn('id', function ($query) {
            $query->select('causer_id')
                ->from(config('activitylog.table_name', 'activity_log'))
                ->whereNotNull('causer_id')
                ->distinct();
        })->orderBy('name')->get(['id', 'name', 'email']);

        $data = [
            'title' => 'Activity Log',
            'breadcrumbs' => [
                [
                    'name' => 'Dashboard',
                    'link' => route('back.dashboard')
                ],
                [
                    'name' => 'Activity Log',
                    'link' => route('back.logs.index')
                ]
            ],
            'logNames' => $logNames,
            'events' => $events,
            'subjectTypes' => $subjectTypes,
            'causers' => $causers,
        ];

        return view('back.pages.logs.index', $data);
    }

    public function datatable(Request $request)
    {
        $search = $request->search_keyword;
        $log_name = $request->log_name;
        $event = $request->event;
        $causer_id = $request->causer_id;
        $subject_type = $request->subject_type;
        $date_start = $request->date_start;
        $date_end = $request->date_end;

        $activities = Activity::with(['causer', 'subject'])
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', '%' . $search . '%')
                        ->orWhere('subject_type', 'like', '%' . $search . '%')
                        ->orWhere('log_name', 'like', '%' . $search . '%')
                        ->orWhere('event', 'like', '%' . $search . '%')
                        ->orWhere('properties', 'like', '%' . $search . '%')
                        ->orWhereHas('causer', function ($q2) use ($search) {
                            $q2->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($log_name, function ($query) use ($log_name) {
                return $query->where('log_name', $log_name);
            })
            ->when($event, function ($query) use ($event) {
                return $query->where('event', $event);
            })
            ->when($causer_id, function ($query) use ($causer_id) {
                return $query->where('causer_id', $causer_id);
            })
            ->when($subject_type, function ($query) use ($subject_type) {
                return $query->where('subject_type', $subject_type);
            })
            ->when($date_start, function ($query) use ($date_start) {
                return $query->whereDate('created_at', '>=', $date_start);
            })
            ->when($date_end, function ($query) use ($date_end) {
                return $query->whereDate('created_at', '<=', $date_end);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung statistik
        $total = $activities->count();
        $totalCreated = $activities->where('event', 'created')->count();
        $totalUpdated = $activities->where('event', 'updated')->count();
        $totalDeleted = $activities->where('event', 'deleted')->count();

        return datatables()
            ->of($activities)
            ->addColumn('causer_info', function ($activity) {
                if ($activity->causer) {
                    return '
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-circle symbol-35px me-3">
                                <img src="' . $activity->causer->getPhoto() . '" alt="' . e($activity->causer->name) . '" />
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 fw-bold">' . e($activity->causer->name) . '</span>
                                <span class="text-gray-500 fs-7">' . e($activity->causer->email) . '</span>
                            </div>
                        </div>
                    ';
                }
                return '
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-35px me-3">
                            <span class="symbol-label bg-light-dark text-dark fw-bold">S</span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fw-bold">System</span>
                            <span class="text-gray-500 fs-7">Otomatis</span>
                        </div>
                    </div>
                ';
            })
            ->addColumn('event_badge', function ($activity) {
                $event = $activity->event ?? '-';
                $badgeClass = match ($event) {
                    'created' => 'badge-light-success',
                    'updated' => 'badge-light-warning',
                    'deleted' => 'badge-light-danger',
                    default => 'badge-light-info',
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($event) . '</span>';
            })
            ->addColumn('log_name_badge', function ($activity) {
                $logName = $activity->log_name ?? '-';
                return '<span class="badge badge-light-primary">' . e($logName) . '</span>';
            })
            ->addColumn('description_info', function ($activity) {
                return '<span class="text-gray-700">' . e($activity->description) . '</span>';
            })
            ->addColumn('subject_info', function ($activity) {
                $subjectType = $activity->subject_type
                    ? class_basename($activity->subject_type)
                    : '-';
                $subjectId = $activity->subject_id ?? '-';

                $badgeClass = 'badge-light-info';
                if ($subjectType === 'User') $badgeClass = 'badge-light-primary';
                elseif ($subjectType === 'Journal') $badgeClass = 'badge-light-success';
                elseif ($subjectType === 'Submission') $badgeClass = 'badge-light-warning';
                elseif ($subjectType === 'Payment') $badgeClass = 'badge-light-danger';

                return '
                    <div class="d-flex flex-column">
                        <span class="badge ' . $badgeClass . ' mb-1" style="width: fit-content;">' . e($subjectType) . '</span>
                        <span class="text-gray-500 fs-7">Data ID: ' . e($subjectId) . '</span>
                    </div>
                ';
            })
            ->addColumn('properties_info', function ($activity) {
                $properties = $activity->properties;
                if ($properties && $properties->count() > 0) {
                    $changedCount = 0;
                    if ($properties->has('attributes')) {
                        $changedCount = count($properties['attributes']);
                    }
                    $label = $changedCount > 0 ? $changedCount . ' field' : 'Detail';

                    return '
                        <a href="#" class="btn btn-sm btn-light-primary btn-properties"
                            data-bs-toggle="modal"
                            data-bs-target="#modal_properties"
                            data-properties=\'' . e($properties->toJson()) . '\'
                            data-description="' . e($activity->description) . '"
                            data-event="' . e($activity->event) . '"
                            data-subject="' . e($activity->subject_type ? class_basename($activity->subject_type) : '-') . '"
                            data-subject-id="' . e($activity->subject_id ?? '-') . '"
                            data-causer="' . e($activity->causer ? $activity->causer->name : 'System') . '"
                            data-time="' . $activity->created_at->format('d M Y H:i:s') . '">
                            <i class="ki-duotone ki-eye fs-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i> ' . $label . '
                        </a>
                    ';
                }
                return '<span class="text-muted fs-7">-</span>';
            })
            ->addColumn('created_at_formatted', function ($activity) {
                return '
                    <div class="d-flex flex-column">
                        <span class="text-gray-800">' . $activity->created_at->format('d M Y') . '</span>
                        <span class="text-gray-500 fs-7">' . $activity->created_at->format('H:i:s') . '</span>
                    </div>
                ';
            })
            ->with([
                'stat_total' => $total,
                'stat_created' => $totalCreated,
                'stat_updated' => $totalUpdated,
                'stat_deleted' => $totalDeleted,
            ])
            ->rawColumns(['causer_info', 'event_badge', 'log_name_badge', 'description_info', 'subject_info', 'properties_info', 'created_at_formatted'])
            ->make(true);
    }
}

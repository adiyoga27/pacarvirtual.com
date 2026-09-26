<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->query('module'), fn ($q, $m) => $q->where('module', $m))
            ->when($request->query('action'), fn ($q, $a) => $q->where('action', $a))
            ->when($request->query('q'), fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('description', 'like', "%{$s}%")
                ->orWhere('user_name', 'like', "%{$s}%")
                ->orWhere('url', 'like', "%{$s}%")))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $modules = ActivityLog::select('module')->distinct()->pluck('module');
        $actions = ['login', 'logout', 'create', 'update', 'delete'];

        return view('admin.activity-logs.index', compact('logs', 'modules', 'actions'));
    }

    public function destroy(Request $request)
    {
        $days = (int) $request->input('days', 90);
        $cutoff = now()->subDays(max($days, 1));
        $count = ActivityLog::where('created_at', '<', $cutoff)->delete();
        return back()->with('success', "Hapus {$count} log lebih lama dari {$days} hari.");
    }
}

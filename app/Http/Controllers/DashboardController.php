<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UpdateChecker;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, UpdateChecker $checker): View
    {
        $userCount = User::count();

        $recentActivities = collect();
        try {
            if (function_exists('activity')) {
                $recentActivities = activity()->latest()->limit(10)->get();
            }
        } catch (\Throwable $e) {
        }

        $currentVersion = config('update.current_version');
        $latest = null;
        try {
            $latest = $checker->latestRelease();
        } catch (\Throwable $e) {
            $latest = null;
        }

        return view('dashboards.index', compact('userCount', 'recentActivities', 'currentVersion', 'latest'));
    }

    public function changelog(Request $request, UpdateChecker $checker)
    {
        $releases = $checker->releases(20) ?: [];

        return response()->json($releases);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MangroveLocation;
use App\Models\LocationDamage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data['title'] = 'Dashboard Admin';

        // Statistics from database
        $data['total_sites'] = MangroveLocation::where('is_active', true)->count();
        $data['total_area'] = MangroveLocation::where('is_active', true)->sum('area');
        $data['active_damages'] = LocationDamage::whereIn('status', ['pending', 'in_progress'])->count();

        // Count activities this month (you can create Activity model later)
        $data['this_month_activities'] = 0; // TODO: Implement when Activity model is created

        // Density distribution from database
        $densityStats = MangroveLocation::where('is_active', true)
            ->select('density', DB::raw('count(*) as count'))
            ->groupBy('density')
            ->pluck('count', 'density')
            ->toArray();

        $data['density_distribution'] = [
            'jarang' => $densityStats['jarang'] ?? 0,
            'sedang' => $densityStats['sedang'] ?? 0,
            'lebat' => $densityStats['lebat'] ?? 0
        ];

        // Recent activities (static for now, can be dynamic later)
        $data['recent_activities'] = [
            ['name' => 'Penanaman Mangrove', 'site' => 'TWA Angke Kapuk', 'date' => '2025-01-25'],
            ['name' => 'Monitoring Rutin', 'site' => 'Rawa Hutan Lindung', 'date' => '2025-01-23'],
            ['name' => 'Pembersihan Area', 'site' => 'Pantai Marunda', 'date' => '2025-01-20'],
        ];

        // Pending damage reports from database
        $pendingDamages = LocationDamage::with('location')
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $data['pending_reports'] = $pendingDamages->map(function ($damage) {
            return [
                'title' => $damage->title,
                'site' => $damage->location->name ?? 'N/A',
                'priority' => $damage->priority,
                'id' => encode_id($damage->id)
            ];
        })->toArray();

        return view('admin.dashboard', $data);
    }
}

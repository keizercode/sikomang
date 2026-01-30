<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MangroveLocation;
use App\Models\DamageReport;
use App\Models\Activity;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $data['title'] = 'Dashboard Admin';

        // Statistics
        $data['total_sites'] = 23; // Will be dynamic from database
        $data['total_area'] = 297; // hectares
        $data['active_damages'] = 12;
        $data['this_month_activities'] = 5;

        // Chart data
        $data['density_distribution'] = [
            'jarang' => 8,
            'sedang' => 7,
            'lebat' => 8
        ];

        $data['recent_activities'] = [
            ['name' => 'Penanaman Mangrove', 'site' => 'TWA Angke Kapuk', 'date' => '2025-01-25'],
            ['name' => 'Monitoring Rutin', 'site' => 'Rawa Hutan Lindung', 'date' => '2025-01-23'],
            ['name' => 'Pembersihan Area', 'site' => 'Pantai Marunda', 'date' => '2025-01-20'],
        ];

        $data['pending_reports'] = [
            ['title' => 'Kerusakan Akar Mangrove', 'site' => 'Pos 2 HL', 'priority' => 'high'],
            ['title' => 'Sampah Menumpuk', 'site' => 'Tanah Timbul', 'priority' => 'medium'],
        ];

        return view('admin.dashboard', $data);
    }
}

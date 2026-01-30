<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    protected $title = 'Manajemen Lokasi Mangrove';
    protected $route = 'admin.monitoring';

    public function index()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Monitoring Mangrove'],
            ['name' => 'Data Lokasi', 'active' => true],
        ];
        $data['title'] = $this->title;
        $data['route'] = $this->route;

        return view('admin.monitoring.index', $data);
    }

    public function grid(Request $request)
    {
        // Dummy data - replace with actual database query
        $data = [
            [
                'id' => encode_id(1),
                'no' => 1,
                'name' => 'Rawa Hutan Lindung',
                'region' => 'Penjaringan, Jakarta Utara',
                'area' => '44.7 ha',
                'density' => 'Jarang',
                'health' => '98%',
                'type' => 'Pengkayaan',
                'action' => '<div class="d-flex gap-1">
                    <a href="' . route('admin.monitoring.edit', encode_id(1)) . '" class="btn btn-sm btn-success"><i class="mdi mdi-pencil"></i></a>
                    <a href="#" data-href="' . route('admin.monitoring.delete', encode_id(1)) . '" class="btn btn-sm btn-danger remove_data"><i class="mdi mdi-delete"></i></a>
                </div>'
            ],
            [
                'id' => encode_id(2),
                'no' => 2,
                'name' => 'TWA Angke Kapuk',
                'region' => 'Penjaringan, Jakarta Utara',
                'area' => '99.82 ha',
                'density' => 'Sedang',
                'health' => '95%',
                'type' => 'Pengkayaan',
                'action' => '<div class="d-flex gap-1">
                    <a href="' . route('admin.monitoring.edit', encode_id(2)) . '" class="btn btn-sm btn-success"><i class="mdi mdi-pencil"></i></a>
                    <a href="#" data-href="' . route('admin.monitoring.delete', encode_id(2)) . '" class="btn btn-sm btn-danger remove_data"><i class="mdi mdi-delete"></i></a>
                </div>'
            ],
        ];

        return response()->json($data);
    }

    public function create()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Data Lokasi', 'url' => route('admin.monitoring.index')],
            ['name' => 'Tambah Lokasi', 'active' => true],
        ];
        $data['title'] = 'Tambah Lokasi Mangrove';
        $data['route'] = $this->route;
        $data['item'] = null;

        return view('admin.monitoring.form', $data);
    }

    public function edit($id)
    {
        $keyId = decode_id($id);

        $data['breadcrumbs'] = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Data Lokasi', 'url' => route('admin.monitoring.index')],
            ['name' => 'Edit Lokasi', 'active' => true],
        ];
        $data['title'] = 'Edit Lokasi Mangrove';
        $data['route'] = $this->route;
        $data['keyId'] = $id;

        // Dummy data - replace with actual database query
        $data['item'] = (object)[
            'id' => $keyId,
            'name' => 'Rawa Hutan Lindung',
            'slug' => 'rawa-hutan-lindung',
            'latitude' => -6.1023,
            'longitude' => 106.7655,
            'area' => 44.7,
            'density' => 'jarang',
            'type' => 'pengkayaan',
            'health' => 98,
            'description' => 'Kawasan hutan mangrove lindung'
        ];

        return view('admin.monitoring.form', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'area' => 'required|numeric',
            'density' => 'required',
            'type' => 'required',
        ]);

        // Save to database (implementation needed)

        return redirect()->route('admin.monitoring.index')->with([
            'message' => 'Data lokasi berhasil disimpan',
            'type' => 'success'
        ]);
    }

    public function destroy($id)
    {
        // Delete from database (implementation needed)

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus',
            'type' => 'success'
        ]);
    }

    public function damages()
    {
        $data['title'] = 'Data Kerusakan';
        return view('admin.monitoring.damages', $data);
    }

    public function reports()
    {
        $data['title'] = 'Laporan Monitoring';
        return view('admin.monitoring.reports', $data);
    }
}

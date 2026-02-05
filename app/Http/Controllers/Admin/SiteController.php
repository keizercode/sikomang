<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\MangroveLocation;
use App\Models\LocationDetail;
use App\Models\LocationImage;
use App\Models\LocationDamage;

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

        return view('pages.admin.monitoring.index', $data);
    }

    public function grid(Request $request)
    {
        $locations = MangroveLocation::with(['damages'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $locations->map(function ($location, $index) {
            return [
                'id' => encode_id($location->id),
                'no' => $index + 1,
                'name' => $location->name,
                'region' => $location->region ?? '-',
                'area' => $location->area ? $location->area . ' ha' : 'Belum diidentifikasi',
                'density' => ucfirst($location->density),
                'health' => $location->health_percentage ? $location->health_percentage . '%' : 'N/A',
                'type' => ucfirst($location->type),
                'action' => '<div class="d-flex gap-1">
                    <a href="' . route('admin.monitoring.detail', encode_id($location->id)) . '" class="btn btn-sm btn-secondary" title="Detail"><i class="mdi mdi-eye"></i></a>
                    <a href="' . route('admin.monitoring.edit', encode_id($location->id)) . '" class="btn btn-sm btn-success" title="Edit"><i class="mdi mdi-pencil"></i></a>
                    <a href="#" data-href="' . route('admin.monitoring.delete', encode_id($location->id)) . '" class="btn btn-sm btn-danger remove_data" title="Hapus"><i class="mdi mdi-delete"></i></a>
                </div>'
            ];
        })->toArray();

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

        return view('pages.admin.monitoring.form', $data);
    }

    public function edit($id)
    {
        $keyId = decode_id($id);
        $location = MangroveLocation::with(['details', 'images', 'damages'])->findOrFail($keyId);

        $data['breadcrumbs'] = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Data Lokasi', 'url' => route('admin.monitoring.index')],
            ['name' => 'Edit Lokasi', 'active' => true],
        ];
        $data['title'] = 'Edit Lokasi Mangrove';
        $data['route'] = $this->route;
        $data['keyId'] = $id;
        $data['item'] = $location;

        return view('pages.admin.monitoring.form', $data);
    }

    public function store(Request $request)
    {
        $keyId = decode_id($request->secure_id);

        // Validation rules - area dan health_percentage tidak wajib diisi
        $validated = $request->validate([
            'name' => 'required|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'area' => 'nullable|numeric|min:0', // NULLABLE - tidak wajib
            'density' => 'required|in:jarang,sedang,lebat',
            'type' => 'required|in:pengkayaan,rehabilitasi,dilindungi,restorasi',
            'region' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'health_percentage' => 'nullable|numeric|between:0,100', // NULLABLE - tidak wajib
            'health_score' => 'nullable|string|max:255',
            'manager' => 'nullable|string|max:255',
            'year_established' => 'nullable|integer|min:1900|max:' . date('Y'),
            'location_address' => 'nullable|string',
            'species' => 'nullable|string',
            'carbon_data' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Generate slug jika kosong
        if (empty($validated['slug']) || !isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);

            // Pastikan slug unik
            $originalSlug = $validated['slug'];
            $count = 1;
            while (MangroveLocation::where('slug', $validated['slug'])
                ->when($keyId, function ($query) use ($keyId) {
                    return $query->where('id', '!=', $keyId);
                })
                ->exists()
            ) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        // Set is_active default to true if not provided
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        if ($keyId) {
            // Update existing
            $location = MangroveLocation::findOrFail($keyId);
            $location->update($validated);

            $message = 'Data lokasi berhasil diperbarui';
        } else {
            // Create new
            $location = MangroveLocation::create($validated);

            // Create details record
            LocationDetail::create([
                'mangrove_location_id' => $location->id,
            ]);

            $message = 'Data lokasi berhasil ditambahkan';
        }

        return redirect()
            ->route('admin.monitoring.index')
            ->with([
                'message' => $message,
                'type' => 'success'
            ]);
    }

    public function destroy($id)
    {
        $keyId = decode_id($id);
        $location = MangroveLocation::findOrFail($keyId);

        // Delete related images
        foreach ($location->images as $image) {
            if (str_contains($image->image_url, 'storage/')) {
                $path = str_replace(asset('storage/'), '', $image->image_url);
                \Storage::disk('public')->delete($path);
            }
        }

        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus',
            'type' => 'success'
        ]);
    }

    public function damages()
    {
        $data['title'] = 'Data Kerusakan';
        $data['damages'] = LocationDamage::with(['location'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pages.admin.monitoring.damages', $data);
    }

    public function reports()
    {
        $data['title'] = 'Laporan Monitoring';
        $data['locations'] = MangroveLocation::with(['damages', 'images'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.admin.monitoring.reports', $data);
    }
}

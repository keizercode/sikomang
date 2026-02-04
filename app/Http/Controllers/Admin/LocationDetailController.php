<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MangroveLocation;
use App\Models\LocationDetail;
use App\Models\LocationImage;
use App\Models\LocationDamage;
use App\Models\LocationAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocationDetailController extends Controller
{
    /**
     * Display location details
     */
    public function show($id)
    {
        $keyId = decode_id($id);
        $location = MangroveLocation::with(['details', 'images', 'damages.actions'])->findOrFail($keyId);

        $data['breadcrumbs'] = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Data Lokasi', 'url' => route('admin.monitoring.index')],
            ['name' => 'Detail Lokasi', 'active' => true],
        ];
        $data['title'] = 'Detail Lokasi: ' . $location->name;
        $data['location'] = $location;
        $data['keyId'] = $id;

        return view('pages.admin.monitoring.detail', $data);
    }

    /**
     * Show form to edit species detail
     */
    public function editSpecies($id)
    {
        $keyId = decode_id($id);
        $location = MangroveLocation::with('details')->findOrFail($keyId);

        $data['title'] = 'Edit Spesies - ' . $location->name;
        $data['location'] = $location;
        $data['keyId'] = $id;

        return view('admin.monitoring.edit-species', $data);
    }

    /**
     * Update species detail
     */
    public function updateSpecies(Request $request, $id)
    {
        $keyId = decode_id($id);
        $location = MangroveLocation::findOrFail($keyId);

        $validated = $request->validate([
            'vegetasi' => 'nullable|array',
            'vegetasi.*' => 'string|max:255',
            'fauna' => 'nullable|array',
            'fauna.*' => 'string|max:255',
        ]);

        $updateData = [
            'vegetasi' => array_filter($validated['vegetasi'] ?? []),
            'fauna' => array_filter($validated['fauna'] ?? []),
        ];

        $location->details->update($updateData);

        LocationDetail::create([
            'mangrove_location_id' => $location->id,
            'vegetasi' => $updateData['vegetasi'],
            'fauna' => $updateData['fauna'],
        ]);

        return redirect()
            ->route('admin.monitoring.detail', $id)
            ->with(['message' => 'Data spesies berhasil diperbarui', 'type' => 'success']);
    }

    /**
     * Show form to edit activities
     */
    public function editActivities($id)
    {
        $keyId = decode_id($id);
        $location = MangroveLocation::with('details')->findOrFail($keyId);

        $data['title'] = 'Edit Aktivitas - ' . $location->name;
        $data['location'] = $location;
        $data['keyId'] = $id;

        return view('admin.monitoring.edit-activities', $data);
    }

    /**
     * Update activities
     */
    public function updateActivities(Request $request, $id)
    {
        $keyId = decode_id($id);
        $location = MangroveLocation::findOrFail($keyId);

        $validated = $request->validate([
            'description' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*' => 'string|max:255',
        ]);

        $activities = [
            'description' => $validated['description'] ?? '',
            'items' => array_filter($validated['items'] ?? []),
        ];

        if ($location->details) {
            $location->details->update(['activities' => $activities]);
        } else {
            LocationDetail::create([
                'mangrove_location_id' => $location->id,
                'activities' => $activities,
            ]);
        }

        return redirect()
            ->route('pages.admin.monitoring.detail', $id)
            ->with(['message' => 'Data aktivitas berhasil diperbarui', 'type' => 'success']);
    }

    /**
     * Update other details (utilization, programs, stakeholders)
     */
    public function updateOtherDetails(Request $request, $id)
    {
        $keyId = decode_id($id);
        $location = MangroveLocation::findOrFail($keyId);

        $validated = $request->validate([
            'forest_utilization' => 'nullable|array',
            'forest_utilization.*' => 'string|max:255',
            'programs' => 'nullable|array',
            'programs.*' => 'string|max:255',
            'stakeholders' => 'nullable|array',
            'stakeholders.*' => 'string|max:255',
        ]);

        $updateData = [
            'forest_utilization' => array_filter($validated['forest_utilization'] ?? []),
            'programs' => array_filter($validated['programs'] ?? []),
            'stakeholders' => array_filter($validated['stakeholders'] ?? []),
        ];

        if ($location->details) {
            $location->details->update($updateData);
        } else {
            LocationDetail::create(array_merge(
                ['mangrove_location_id' => $location->id],
                $updateData
            ));
        }

        return redirect()
            ->route('pages.admin.monitoring.detail', $id)
            ->with(['message' => 'Data detail berhasil diperbarui', 'type' => 'success']);
    }

    /**
     * Upload images
     */
    public function uploadImages(Request $request, $id)
    {
        $keyId = decode_id($id);
        $location = MangroveLocation::findOrFail($keyId);

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $uploaded = 0;

        foreach ($request->file('images') as $index => $image) {
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('locations', $filename, 'public');

            // Upload to cloudinary or other service if needed
            $imageUrl = asset('storage/' . $path);

            LocationImage::create([
                'mangrove_location_id' => $location->id,
                'image_url' => $imageUrl,
                'caption' => $request->captions[$index] ?? null,
                'order' => LocationImage::where('mangrove_location_id', $location->id)->max('order') + 1,
            ]);

            $uploaded++;
        }

        return redirect()
            ->route('pages.admin.monitoring.detail', $id)
            ->with(['message' => "{$uploaded} gambar berhasil diupload", 'type' => 'success']);
    }

    /**
     * Delete image
     */
    public function deleteImage($locationId, $imageId)
    {
        $keyId = decode_id($locationId);
        $location = MangroveLocation::findOrFail($keyId);

        $image = LocationImage::where('mangrove_location_id', $location->id)
            ->where('id', $imageId)
            ->firstOrFail();

        // Delete file if it's local storage
        if (str_contains($image->image_url, 'storage/')) {
            $path = str_replace(asset('storage/'), '', $image->image_url);
            Storage::disk('public')->delete($path);
        }

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gambar berhasil dihapus',
            'type' => 'success'
        ]);
    }

    /**
     * Add damage report
     */
    public function addDamage(Request $request, $id)
    {
        $keyId = decode_id($id);
        $location = MangroveLocation::findOrFail($keyId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,resolved',
        ]);

        $damage = LocationDamage::create(array_merge(
            ['mangrove_location_id' => $location->id],
            $validated
        ));

        return redirect()
            ->route('pages.admin.monitoring.detail', $id)
            ->with(['message' => 'Laporan kerusakan berhasil ditambahkan', 'type' => 'success']);
    }

    /**
     * Update damage report
     */
    public function updateDamage(Request $request, $id, $damageId)
    {
        $keyId = decode_id($id);
        $location = MangroveLocation::findOrFail($keyId);

        $damage = LocationDamage::where('mangrove_location_id', $location->id)
            ->where('id', $damageId)
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,resolved',
        ]);

        $damage->update($validated);

        return redirect()
            ->route('pages.admin.monitoring.detail', $id)
            ->with(['message' => 'Laporan kerusakan berhasil diperbarui', 'type' => 'success']);
    }

    /**
     * Delete damage report
     */
    public function deleteDamage($id, $damageId)
    {
        $keyId = decode_id($id);
        $location = MangroveLocation::findOrFail($keyId);

        $damage = LocationDamage::where('mangrove_location_id', $location->id)
            ->where('id', $damageId)
            ->firstOrFail();

        $damage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Laporan kerusakan berhasil dihapus',
            'type' => 'success'
        ]);
    }

    /**
     * Add action to damage
     */
    public function addAction(Request $request, $id, $damageId)
    {
        $keyId = decode_id($id);
        $location = MangroveLocation::findOrFail($keyId);

        $damage = LocationDamage::where('mangrove_location_id', $location->id)
            ->where('id', $damageId)
            ->firstOrFail();

        $validated = $request->validate([
            'action_description' => 'required|string',
            'action_date' => 'nullable|date',
        ]);

        LocationAction::create(array_merge(
            ['location_damage_id' => $damage->id],
            $validated
        ));

        return redirect()
            ->route('pages.admin.monitoring.detail', $id)
            ->with(['message' => 'Aksi penanganan berhasil ditambahkan', 'type' => 'success']);
    }
}

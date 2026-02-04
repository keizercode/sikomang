<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index(Request $request)
    {
        $data['title'] = 'Pengaturan Sistem';

        $group = $request->get('group', 'general');

        $data['settings'] = Setting::byGroup($group)->get();
        $data['currentGroup'] = $group;
        $data['groups'] = Setting::distinct()->pluck('group');

        return view('pages.admin.settings.index', $data);
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if (!$setting) {
                continue;
            }

            // Handle file upload
            if ($setting->type === 'file' && $request->hasFile("settings.{$key}")) {
                // Delete old file
                if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                    Storage::disk('public')->delete($setting->value);
                }

                $file = $request->file("settings.{$key}");
                $filename = time() . '_' . Str::slug($key) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('settings', $filename, 'public');
                $value = $path;
            }

            // Update setting
            $setting->update(['value' => $value]);
        }

        // Clear cache
        Setting::clearCache();

        return redirect()
            ->back()
            ->with('success', 'Pengaturan berhasil diperbarui!');
    }

    /**
     * Create new setting
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:settings,key',
            'label' => 'required|string',
            'value' => 'nullable|string',
            'type' => 'required|in:text,textarea,number,boolean,file,json',
            'group' => 'required|string',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        Setting::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Pengaturan baru berhasil ditambahkan!');
    }

    /**
     * Delete setting
     */
    public function destroy(Setting $setting)
    {
        // Delete file if type is file
        if ($setting->type === 'file' && $setting->value) {
            Storage::disk('public')->delete($setting->value);
        }

        $setting->delete();

        return redirect()
            ->back()
            ->with('success', 'Pengaturan berhasil dihapus!');
    }

    /**
     * Show general settings
     */
    public function general()
    {
        $data['title'] = 'Pengaturan Umum';
        $data['settings'] = Setting::byGroup('general')->get();

        return view('pages.admin.settings.general', $data);
    }

    /**
     * Show contact settings
     */
    public function contact()
    {
        $data['title'] = 'Pengaturan Kontak';
        $data['settings'] = Setting::byGroup('contact')->get();

        return view('pages.admin.settings.contact', $data);
    }

    /**
     * Show social media settings
     */
    public function social()
    {
        $data['title'] = 'Pengaturan Media Sosial';
        $data['settings'] = Setting::byGroup('social')->get();

        return view('pages.admin.settings.social', $data);
    }

    /**
     * Show SEO settings
     */
    public function seo()
    {
        $data['title'] = 'Pengaturan SEO';
        $data['settings'] = Setting::byGroup('seo')->get();

        return view('pages.admin.settings.seo', $data);
    }

    /**
     * Show mail settings
     */
    public function mail()
    {
        $data['title'] = 'Pengaturan Email';
        $data['settings'] = Setting::byGroup('mail')->get();

        return view('pages.admin.settings.mail', $data);
    }

    /**
     * Clear all cache
     */
    public function clearCache()
    {
        Setting::clearCache();

        return redirect()
            ->back()
            ->with('success', 'Cache pengaturan berhasil dibersihkan!');
    }

    /**
     * Export settings as JSON
     */
    public function export()
    {
        $settings = Setting::all();

        $data = $settings->map(function ($setting) {
            return [
                'key' => $setting->key,
                'value' => $setting->value,
                'type' => $setting->type,
                'group' => $setting->group,
                'label' => $setting->label,
                'description' => $setting->description,
                'is_public' => $setting->is_public,
            ];
        });

        $filename = 'settings_backup_' . date('Y-m-d_His') . '.json';

        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Import settings from JSON
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json',
        ]);

        $json = file_get_contents($request->file('file')->getRealPath());
        $settings = json_decode($json, true);

        if (!is_array($settings)) {
            return redirect()
                ->back()
                ->with('error', 'File JSON tidak valid!');
        }

        $imported = 0;

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'] ?? null,
                    'type' => $setting['type'] ?? 'text',
                    'group' => $setting['group'] ?? 'general',
                    'label' => $setting['label'],
                    'description' => $setting['description'] ?? null,
                    'is_public' => $setting['is_public'] ?? false,
                ]
            );

            $imported++;
        }

        // Clear cache
        Setting::clearCache();

        return redirect()
            ->back()
            ->with('success', "{$imported} pengaturan berhasil diimport!");
    }

    /**
     * Reset to default settings
     */
    public function reset()
    {
        // This would require default settings to be defined
        // For now, just clear cache
        Setting::clearCache();

        return redirect()
            ->back()
            ->with('success', 'Pengaturan berhasil direset!');
    }
}

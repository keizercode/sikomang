<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\PublicReport;

class CleanupPublicReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:cleanup {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up orphaned files and soft-deleted public reports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Starting Public Reports Cleanup...');
        $this->newLine();

        // 1. Clean soft deleted records
        $this->cleanSoftDeleted();

        // 2. Clean orphaned files
        $this->cleanOrphanedFiles();

        // 3. Fix photo URLs
        $this->fixPhotoUrls();

        // 4. Statistics
        $this->showStatistics();

        $this->newLine();
        $this->info('✅ Cleanup completed successfully!');

        return Command::SUCCESS;
    }

    /**
     * Clean soft deleted records
     */
    protected function cleanSoftDeleted()
    {
        $this->info('1️⃣  Cleaning soft deleted records...');

        $softDeleted = PublicReport::onlyTrashed()->get();
        $count = $softDeleted->count();

        if ($count === 0) {
            $this->line('   ℹ No soft deleted records found');
            return;
        }

        $this->warn("   Found {$count} soft deleted record(s)");

        $proceed = $this->option('force') || $this->confirm('   Permanently delete these records and their files?', true);

        if (!$proceed) {
            $this->line('   ⊘ Skipped');
            return;
        }

        $filesDeleted = 0;
        $recordsDeleted = 0;

        foreach ($softDeleted as $report) {
            // Delete files
            if ($report->photo_urls && is_array($report->photo_urls)) {
                foreach ($report->photo_urls as $url) {
                    $filename = basename(parse_url($url, PHP_URL_PATH));
                    $path = "public_reports/{$filename}";

                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                        $filesDeleted++;
                    }
                }
            }

            // Force delete
            $report->forceDelete();
            $recordsDeleted++;
        }

        $this->info("   ✓ Deleted {$recordsDeleted} record(s) and {$filesDeleted} file(s)");
    }

    /**
     * Clean orphaned files
     */
    protected function cleanOrphanedFiles()
    {
        $this->info('2️⃣  Cleaning orphaned files...');

        // Get all files in storage
        $storageFiles = Storage::disk('public')->files('public_reports');

        // Get all active photo URLs from database
        $activeUrls = DB::table('public_reports')
            ->whereNull('deleted_at')
            ->whereNotNull('photo_urls')
            ->pluck('photo_urls');

        $activeFiles = [];
        foreach ($activeUrls as $urls) {
            $photoUrls = json_decode($urls, true);
            if (is_array($photoUrls)) {
                foreach ($photoUrls as $url) {
                    $filename = basename(parse_url($url, PHP_URL_PATH));
                    $activeFiles[] = "public_reports/{$filename}";
                }
            }
        }

        $orphaned = array_diff($storageFiles, $activeFiles);
        $count = count($orphaned);

        if ($count === 0) {
            $this->line('   ℹ No orphaned files found');
            return;
        }

        $this->warn("   Found {$count} orphaned file(s)");

        $proceed = $this->option('force') || $this->confirm('   Delete orphaned files?', true);

        if (!$proceed) {
            $this->line('   ⊘ Skipped');
            return;
        }

        $deleted = 0;
        foreach ($orphaned as $file) {
            if (Storage::disk('public')->delete($file)) {
                $deleted++;
            }
        }

        $this->info("   ✓ Deleted {$deleted} orphaned file(s)");
    }

    /**
     * Fix photo URLs format
     */
    protected function fixPhotoUrls()
    {
        $this->info('3️⃣  Fixing photo URLs...');

        $reports = DB::table('public_reports')
            ->whereNull('deleted_at')
            ->whereNotNull('photo_urls')
            ->get();

        $fixed = 0;

        foreach ($reports as $report) {
            $photoUrls = json_decode($report->photo_urls, true);

            if (!is_array($photoUrls) || count($photoUrls) === 0) {
                continue;
            }

            $fixedUrls = [];
            $needsFix = false;

            foreach ($photoUrls as $url) {
                // Normalize URL to /storage/public_reports/filename.jpg
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    // Full URL - extract path
                    $path = parse_url($url, PHP_URL_PATH);
                    $fixedUrls[] = $path;
                    $needsFix = true;
                } elseif (str_starts_with($url, '/storage/public_reports/')) {
                    // Already correct format
                    $fixedUrls[] = $url;
                } elseif (str_starts_with($url, 'storage/public_reports/')) {
                    // Missing leading slash
                    $fixedUrls[] = '/' . $url;
                    $needsFix = true;
                } else {
                    // Just filename or other format
                    $filename = basename($url);
                    $fixedUrls[] = "/storage/public_reports/{$filename}";
                    $needsFix = true;
                }
            }

            if ($needsFix) {
                DB::table('public_reports')
                    ->where('id', $report->id)
                    ->update(['photo_urls' => json_encode($fixedUrls)]);
                $fixed++;
            }
        }

        if ($fixed > 0) {
            $this->info("   ✓ Fixed {$fixed} report(s) photo URLs");
        } else {
            $this->line('   ℹ All photo URLs are already correct');
        }
    }

    /**
     * Show statistics
     */
    protected function showStatistics()
    {
        $this->info('4️⃣  Statistics:');

        $stats = [
            'Active Reports' => DB::table('public_reports')->whereNull('deleted_at')->count(),
            'Soft Deleted' => DB::table('public_reports')->whereNotNull('deleted_at')->count(),
            'Total Files' => count(Storage::disk('public')->files('public_reports')),
            'Storage Size' => $this->formatBytes($this->getStorageSize()),
        ];

        foreach ($stats as $label => $value) {
            $this->line("   • {$label}: {$value}");
        }
    }

    /**
     * Get total storage size
     */
    protected function getStorageSize()
    {
        $files = Storage::disk('public')->files('public_reports');
        $totalSize = 0;

        foreach ($files as $file) {
            $totalSize += Storage::disk('public')->size($file);
        }

        return $totalSize;
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}

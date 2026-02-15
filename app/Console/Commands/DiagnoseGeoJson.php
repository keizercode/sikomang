<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DiagnoseGeoJson extends Command
{
    protected $signature = 'geojson:diagnose {url?}';
    protected $description = 'Diagnose GeoJSON structure from URL';

    protected array $defaultUrls = [
        'jarang' => 'https://asset.plovis.id/plovis/public/67f25022-a757-4f90-a114-16e3f3ad671c.geojson',
        'sedang' => 'https://asset.plovis.id/plovis/public/1c7b760f-7458-4353-bfd9-1ba6084cdce6.geojson',
        'lebat' => 'https://asset.plovis.id/plovis/public/cb7b89d7-2ac7-4fa4-a16c-02734432838e.geojson',
    ];

    public function handle()
    {
        $url = $this->argument('url');

        if (!$url) {
            $this->info('🔍 Testing all default URLs...');
            $this->newLine();

            foreach ($this->defaultUrls as $density => $defaultUrl) {
                $this->info("Testing {$density}: {$defaultUrl}");
                $this->diagnoseUrl($defaultUrl);
                $this->newLine();
            }
        } else {
            $this->diagnoseUrl($url);
        }

        return Command::SUCCESS;
    }

    protected function diagnoseUrl(string $url)
    {
        try {
            $this->line("📥 Fetching from: {$url}");

            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                $this->error("❌ HTTP Error: " . $response->status());
                return;
            }

            $this->info("✓ HTTP Status: " . $response->status());

            // Get raw body
            $body = $response->body();
            $bodySize = strlen($body);

            $this->info("✓ Response size: " . number_format($bodySize) . " bytes");

            // Show first 1000 chars
            $this->line("📝 First 1000 characters:");
            $this->line(str_repeat('-', 80));
            $this->line(substr($body, 0, 1000));
            $this->line(str_repeat('-', 80));
            $this->newLine();

            // Try to parse JSON
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("❌ JSON Parse Error: " . json_last_error_msg());
                $this->line("Saving raw response to: storage/logs/geojson_raw.txt");
                file_put_contents(storage_path('logs/geojson_raw.txt'), $body);
                return;
            }

            $this->info("✓ Valid JSON");
            $this->newLine();

            // Analyze structure
            $this->info("📊 JSON Structure:");
            $this->analyzeStructure($data);

            // Save full response
            $filename = 'geojson_' . time() . '.json';
            file_put_contents(storage_path('logs/' . $filename), json_encode($data, JSON_PRETTY_PRINT));
            $this->info("💾 Full response saved to: storage/logs/{$filename}");
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
    }

    protected function analyzeStructure(array $data, int $depth = 0)
    {
        $indent = str_repeat('  ', $depth);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $count = count($value);

                // Check if it's numeric array
                $isNumeric = array_keys($value) === range(0, count($value) - 1);

                if ($isNumeric && $count > 0) {
                    $this->line("{$indent}{$key}: Array[{$count}]");

                    // Show first item structure if it's an array
                    if (is_array($value[0])) {
                        $this->line("{$indent}  First item:");
                        $this->analyzeStructure($value[0], $depth + 2);
                    } else {
                        $this->line("{$indent}  First item: " . $this->formatValue($value[0]));
                    }
                } else {
                    $this->line("{$indent}{$key}: Object");
                    if ($depth < 3) { // Limit depth to avoid too much output
                        $this->analyzeStructure($value, $depth + 1);
                    }
                }
            } else {
                $this->line("{$indent}{$key}: " . $this->formatValue($value));
            }
        }
    }

    protected function formatValue($value): string
    {
        if (is_null($value)) {
            return 'null';
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_string($value)) {
            return '"' . (strlen($value) > 50 ? substr($value, 0, 50) . '..."' : $value . '"');
        } else {
            return (string) $value;
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Tool;

class SyncTools extends Command
{
    protected $signature = 'tools:sync {--dry-run}';
    protected $description = 'Sync categories and tools from files to database';

    protected string $basePath;

    public function __construct()
    {
        parent::__construct();

        $this->basePath = resource_path('views/tools');
    }

    public function handle(): int
    {
        $categoryDirs = glob($this->basePath . '/*', GLOB_ONLYDIR);

        $this->info('Found ' . count($categoryDirs) . ' categories');

        foreach ($categoryDirs as $categoryDir) {
            $categorySlug = basename($categoryDir);
            $categoryFile = $categoryDir . '/index.php';

            if (!file_exists($categoryFile)) {
                $this->warn("Skipped category $categorySlug (no index.php)");
                continue;
            }

            $categoryData = require $categoryFile;

            if (!is_array($categoryData)) {
                $this->warn("Invalid category format: $categorySlug");
                continue;
            }

            $categoryPayload = [
                'slug' => $categorySlug,
                'name' => $categoryData['name'] ?? null,
                'title' => $categoryData['title'] ?? null,
                'desc' => $categoryData['desc'] ?? null,
                'seo_title' => $categoryData['seo_title'] ?? null,
                'seo_desc' => $categoryData['seo_desc'] ?? null,
            ];

            if ($this->option('dry-run')) {
                $this->line("DRY category: $categorySlug");
                $category = null;
            } else {
                $category = Category::query()->updateOrCreate(
                    ['slug' => $categorySlug],
                    $categoryPayload
                );

                $this->info("Synced category: $categorySlug");
            }

            // --- TOOLS ---
            $toolFiles = glob($categoryDir . '/*/index.php');

            foreach ($toolFiles as $toolFile) {
                $toolSlug = basename(dirname($toolFile));

                $toolData = require $toolFile;

                if (!is_array($toolData)) {
                    $this->warn("Invalid tool: $toolSlug");
                    continue;
                }

                $toolPayload = [
                    'slug' => $toolSlug,
                    'category_id' => $category?->id,
                    'name' => $toolData['name'] ?? null,
                    'title' => $toolData['title'] ?? null,
                    'desc' => $toolData['desc'] ?? null,
                    'short_desc' => $toolData['short_desc'] ?? null,
                    'seo_title' => $toolData['seo_title'] ?? null,
                    'seo_desc' => $toolData['seo_desc'] ?? null,
                    'form' => $toolData['form'] ?? null,
                    'script' => $toolData['script'] ?? null,
                    'faq' => $toolData['faq'] ?? null,
                ];

                if ($this->option('dry-run')) {
                    $this->line("  DRY tool: $toolSlug");
                    continue;
                }

                Tool::query()->updateOrCreate(
                    ['slug' => $toolSlug],
                    $toolPayload
                );

                $this->line("  Synced tool: /$categorySlug/$toolSlug");
            }
        }

        $this->info('Done ✅');

        return self::SUCCESS;
    }
}

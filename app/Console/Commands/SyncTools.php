<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Tool;

class SyncTools extends Command
{
    protected $signature = 'tools:sync {--dry-run}';

    protected $description = 'Sync categories and tools from filesystem to database';

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

        $syncedCategorySlugs = [];
        $syncedToolIds = [];

        foreach ($categoryDirs as $categoryDir) {
            $categorySlug = basename($categoryDir);
            $categoryFile = $categoryDir . '/index.php';

            if (!file_exists($categoryFile)) {
                $this->warn("Skipped category {$categorySlug} (missing index.php)");
                continue;
            }

            $categoryData = require $categoryFile;

            if (!is_array($categoryData)) {
                $this->warn("Skipped category {$categorySlug} (invalid index.php)");
                continue;
            }

            $syncedCategorySlugs[] = $categorySlug;

            $categoryPayload = [
                'slug' => $categorySlug,
                'name' => $categoryData['name'] ?? null,
                'title' => $categoryData['title'] ?? null,
                'desc' => $categoryData['desc'] ?? null,
                'seo_title' => $categoryData['seo_title'] ?? null,
                'seo_desc' => $categoryData['seo_desc'] ?? null,
            ];

            if ($this->option('dry-run')) {
                $this->line("DRY category: {$categorySlug}");
                $category = Category::query()
                    ->where('slug', $categorySlug)
                    ->first();
            } else {
                $category = Category::query()->updateOrCreate(
                    ['slug' => $categorySlug],
                    $categoryPayload
                );

                $this->info("Synced category: {$categorySlug}");
            }

            $toolDirs = glob($categoryDir . '/*', GLOB_ONLYDIR);

            foreach ($toolDirs as $toolDir) {
                $toolSlug = basename($toolDir);

                if (!$this->isValidToolDirectory($toolDir)) {
                    $this->warn("   Invalid tool: /{$categorySlug}/{$toolSlug}");

                    if (!$this->option('dry-run')) {
                        Tool::query()
                            ->where('slug', $toolSlug)
                            ->delete();
                    }

                    continue;
                }

                $toolData = require $toolDir . '/index.php';

                if (!is_array($toolData)) {
                    $this->warn("Invalid tool config: {$toolSlug}");
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
                    $this->line("   DRY tool: {$toolSlug}");
                    continue;
                }

                $tool = Tool::query()->updateOrCreate(
                    ['slug' => $toolSlug],
                    $toolPayload
                );

                $syncedToolIds[] = $tool->id;

                $this->line("   Synced tool: /{$categorySlug}/{$toolSlug}");
            }
        }

        if (!$this->option('dry-run')) {
            $deletedTools = Tool::query()
                ->when(
                    !empty($syncedToolIds),
                    fn($q) => $q->whereNotIn('id', $syncedToolIds)
                )
                ->delete();

            if ($deletedTools > 0) {
                $this->warn("Deleted orphan tools: {$deletedTools}");
            }

            $deletedCategories = Category::query()
                ->when(
                    !empty($syncedCategorySlugs),
                    fn($q) => $q->whereNotIn('slug', $syncedCategorySlugs)
                )
                ->delete();

            if ($deletedCategories > 0) {
                $this->warn("Deleted orphan categories: {$deletedCategories}");
            }
        }

        $this->info('Done ✅');

        return self::SUCCESS;
    }

    private function isValidToolDirectory(string $toolDir): bool
    {
        return file_exists($toolDir . '/index.php')
            && file_exists($toolDir . '/core.blade.php')
            && file_exists($toolDir . '/seo.blade.php');
    }
}

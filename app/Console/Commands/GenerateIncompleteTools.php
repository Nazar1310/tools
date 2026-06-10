<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateIncompleteTools extends Command
{
    protected $signature = 'tools:generate-missing';

    protected $description = 'Generate missing tool templates for incomplete tools';

    protected string $basePath;

    public function __construct()
    {
        parent::__construct();

        $this->basePath = resource_path('views/tools');
    }

    public function handle(): int
    {
        $tools = [];

        $categoryDirs = glob($this->basePath . '/*', GLOB_ONLYDIR);

        foreach ($categoryDirs as $categoryDir) {
            $categorySlug = basename($categoryDir);

            $toolDirs = glob($categoryDir . '/*', GLOB_ONLYDIR);

            foreach ($toolDirs as $toolDir) {
                if (!$this->isIncompleteTool($toolDir)) {
                    continue;
                }

                $tools[] = [
                    'category' => $categorySlug,
                    'tool' => basename($toolDir),
                ];
            }
        }

        $total = count($tools);

        if ($total === 0) {
            $this->info('No incomplete tools found.');

            return self::SUCCESS;
        }

        $this->info("Found {$total} incomplete tools");

        foreach ($tools as $index => $tool) {
            $current = $index + 1;

            $path = "{$tool['category']}/{$tool['tool']}";

            $this->line("[{$current}/{$total}] Generating {$path}");

            $exitCode = $this->call(
                'tools:generate',
                [
                    'slug' => $path,
                ]
            );

            if ($exitCode !== self::SUCCESS) {
                $this->error("Failed: {$path}");
            }
        }

        $this->info('Done ✅');

        return self::SUCCESS;
    }

    private function isIncompleteTool(string $toolDir): bool
    {
        return file_exists($toolDir . '/index.php')
            && (
                !file_exists($toolDir . '/core.blade.php')
                || !file_exists($toolDir . '/seo.blade.php')
            );
    }
}

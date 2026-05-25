<?php

namespace App\Console\Commands;

use App\Models\Tool;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {--dry-run}';

    protected $description = 'Generate sitemap.xml for tools';

    protected string $baseUrl;
    protected string $sitemapPath;

    public function __construct()
    {
        parent::__construct();

        $this->baseUrl = rtrim(config('app.url'), '/');
        $this->sitemapPath = public_path('sitemap.xml');
    }

    public function handle(): int
    {
        if (empty($this->baseUrl)) {
            $this->error('APP_URL is not configured');

            return self::FAILURE;
        }

        $toolsCount = Tool::query()->count();

        if ($toolsCount === 0) {
            $this->warn('No tools found');

            return self::SUCCESS;
        }

        $this->info("Found {$toolsCount} tools");

        $urls = [];

        Tool::query()
            ->with('category:id,slug')
            ->chunk(500, function ($tools) use (&$urls) {
                foreach ($tools as $tool) {
                    if (!$tool->category?->slug || !$tool->slug) {
                        $this->warn("Skipped invalid tool ID {$tool->id}");
                        continue;
                    }

                    $urls[] = [
                        'loc' => "{$this->baseUrl}/{$tool->category->slug}/{$tool->slug}",
                        'lastmod' => $tool->updated_at?->toAtomString(),
                    ];
                }
            });

        $this->info('Prepared ' . count($urls) . ' sitemap URLs');

        if ($this->option('dry-run')) {
            $this->line("DRY RUN: sitemap would be generated at {$this->sitemapPath}");

            return self::SUCCESS;
        }

        $xml = $this->buildSitemapXml($urls);

        file_put_contents($this->sitemapPath, $xml);

        $this->info("Sitemap generated: {$this->sitemapPath}");
        $this->info('Done ✅');

        return self::SUCCESS;
    }

    private function buildSitemapXml(array $urls): string
    {
        $xml = [];

        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml[] = '  <url>';
            $xml[] = '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1) . '</loc>';

            if (!empty($url['lastmod'])) {
                $xml[] = '    <lastmod>' . $url['lastmod'] . '</lastmod>';
            }

            $xml[] = '  </url>';
        }

        $xml[] = '</urlset>';

        return implode(PHP_EOL, $xml);
    }
}

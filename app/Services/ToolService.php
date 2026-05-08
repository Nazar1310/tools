<?php

namespace App\Services;
use Illuminate\Pagination\LengthAwarePaginator;

class ToolService
{
    private string $basePath;

    public function __construct()
    {
        $this->basePath = resource_path('views/tools');
    }

    public function getCategories(int $perPage = 24, ?int $page = null): LengthAwarePaginator
    {
        $categories = [];

        foreach (glob($this->basePath . '/*/index.php') as $file) {
            $slug = basename(dirname($file));

            $category = require $file;

            if ($category) {
                $categories[] = $this->withSlug($category, $slug);
            }
        }

        return $this->paginate($categories, $perPage, $page);
    }

    public function getCategory(string $slug): ?array
    {
        $file = $this->basePath . "/$slug/index.php";

        if (!file_exists($file)) {
            return null;
        }

        $category = require $file;

        return $category ? $this->withSlug($category, $slug) : null;
    }

    public function getToolsByCategory(string $categorySlug, int $perPage = 24, ?int $page = null): LengthAwarePaginator
    {
        $path = $this->basePath . '/' . $categorySlug;

        if (!is_dir($path)) {
            return $this->paginate([], $perPage, $page);
        }

        $tools = [];

        foreach (glob($path . '/*/index.php') as $file) {
            $toolSlug = basename(dirname($file));

            $tool = require $file;

            if ($tool) {
                $tools[] = $this->withToolMeta($tool, $toolSlug, $categorySlug);
            }
        }

        return $this->paginate($tools, $perPage, $page);
    }

    public function getSimilarTools(string $categorySlug, string $toolSlug, int $limit = 4): array
    {
        $path = $this->basePath . '/' . $categorySlug;

        if (!is_dir($path)) {
            return [];
        }

        $tools = [];

        foreach (glob($path . '/*/index.php') as $file) {
            $currentSlug = basename(dirname($file));

            if ($currentSlug === $toolSlug) {
                continue;
            }

            $tool = require $file;

            if ($tool) {
                $tools[] = $this->withToolMeta($tool, $currentSlug, $categorySlug);
            }
        }

        shuffle($tools);

        return array_slice($tools, 0, $limit);
    }

    public function getTool(string $categorySlug, string $toolSlug): ?array
    {
        $file = $this->basePath . "/$categorySlug/$toolSlug/index.php";

        if (!file_exists($file)) {
            return null;
        }

        $tool = require $file;

        if (!$tool) {
            return null;
        }

        $tool = $this->withSlug($tool, $toolSlug);
        $tool['category_slug'] = $categorySlug;

        return $tool;
    }

    public function getAllTools(int $perPage = 24, ?int $page = null): LengthAwarePaginator
    {
        $tools = $this->getAllToolsRaw();
        return $this->paginate($tools, $perPage, $page);
    }

    public function getPopularTools(int $limit = 8): array
    {
        $tools = $this->getAllToolsRaw();

        if (empty($tools)) {
            return [];
        }

        srand(date('Ymd'));
        shuffle($tools);

        return array_slice($tools, 0, $limit);
    }

    public function getAllToolsRaw(): array
    {
        $tools = [];

        foreach (glob($this->basePath . '/*') as $categoryDir) {
            if (!is_dir($categoryDir)) continue;

            $categorySlug = basename($categoryDir);

            foreach (glob($categoryDir . '/*/index.php') as $file) {
                $toolSlug = basename(dirname($file));

                $tool = require $file;

                if ($tool) {
                    $tools[] = $this->withToolMeta($tool, $toolSlug, $categorySlug);
                }
            }
        }

        return $tools;
    }

    private function withSlug(array $data, string $slug): array
    {
        $data['slug'] = $slug;
        return $data;
    }

    private function withToolMeta(array $tool, string $toolSlug, string $categorySlug): array
    {
        $tool['slug'] = $toolSlug;
        $tool['category_slug'] = $categorySlug;

        return $tool;
    }

    private function paginate(array $items, int $perPage, ?int $page = null): LengthAwarePaginator
    {
        $collection = collect($items);

        $page = $page ?? LengthAwarePaginator::resolveCurrentPage();

        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $results,
            $collection->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }
}

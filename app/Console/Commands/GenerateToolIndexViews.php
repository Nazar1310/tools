<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class GenerateToolIndexViews extends Command
{
    private string $systemPrompt;
    private string $userPromptTemplate;

    protected $signature = '
        tools:generate-index
        {category_slug}
        {--tools_path=}
        {--tool=}
    ';

    protected $description = 'Generate tool index.php metadata files using LLM';

    /**
     * @throws ConnectionException
     */
    public function handle(): int
    {
        $categorySlug = $this->argument('category_slug');
        $categoryPath = resource_path("views/tools/{$categorySlug}");

        if (!File::isDirectory($categoryPath) || !File::exists("$categoryPath/index.php")) {
            $this->error("Category not found: {$categorySlug}");
            return self::FAILURE;
        }

        $tools = $this->resolveTools();

        if (empty($tools)) {
            $this->error('No tools provided.');
            return self::FAILURE;
        }

        $this->loadPrompts();

        foreach ($tools as $toolName) {
            $toolName = trim($toolName);

            if ($toolName === '') {
                continue;
            }

            $this->info("Processing: {$toolName}");

            $metadata = $this->generateMetadata($toolName, $categorySlug);

            if (!$metadata) {
                $this->error("Failed to generate metadata for: {$toolName}");
                continue;
            }

            if (!$this->validateMetadata($metadata)) {
                $this->error("Invalid metadata received for: {$toolName}");
                continue;
            }

            $toolSlug = $metadata['tool_slug'];
            $toolPath = "{$categoryPath}/{$toolSlug}";
            $indexPath = "{$toolPath}/index.php";

            if (File::exists($indexPath)) {
                $this->warn("Skipped (already exists): {$toolSlug}");
                continue;
            }

            File::ensureDirectoryExists($toolPath);

            File::put(
                $indexPath,
                $this->buildIndexFile($metadata)
            );

            $this->info("Generated: /{$categorySlug}/{$toolSlug}");
        }

        $this->info('Done ✅');

        return self::SUCCESS;
    }

    private function resolveTools(): array
    {
        $tool = $this->option('tool');
        $toolsPath = $this->option('tools_path');

        if ($tool && $toolsPath) {
            $this->error('Use either --tool or --tools_path, not both.');
            return [];
        }

        if (!$tool && !$toolsPath) {
            $this->error('Either --tool or --tools_path must be provided.');
            return [];
        }

        if ($tool) {
            return [$tool];
        }

        if (!File::exists($toolsPath)) {
            $this->error("CSV file not found: {$toolsPath}");
            return [];
        }

        $rows = file($toolsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return array_map(
            static fn(string $row) => trim($row),
            $rows
        );
    }

    /**
     * @throws ConnectionException
     */
    private function generateMetadata(
        string $toolName,
        string $categorySlug
    ): ?array {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-5.4-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => $this->userPrompt($toolName, $categorySlug),
                ],
            ],
            'max_completion_tokens' => 1200,
            'temperature' => 0.2,
            'top_p' => 1.0,
        ]);

        if (!$response->ok()) {
            $this->error($response->body());
            return null;
        }

        $content = $this->cleanResponse(
            $response->json('choices.0.message.content')
        );

        if (!$content) {
            return null;
        }

        $decoded = json_decode($content, true);

        if (!is_array($decoded)) {
            $this->error("Invalid JSON response: {$content}");
            return null;
        }

        return $decoded;
    }

    private function validateMetadata(array $metadata): bool
    {
        $required = [
            'tool_slug',
            'name',
            'title',
            'desc',
            'seo_title',
            'seo_desc',
        ];

        foreach ($required as $field) {
            if (
                !array_key_exists($field, $metadata)
                || !is_string($metadata[$field])
                || trim($metadata[$field]) === ''
            ) {
                return false;
            }
        }

        return true;
    }

    private function buildIndexFile(array $metadata): string
    {
        return "<?php\n\nreturn " . var_export([
                'name' => $metadata['name'],
                'title' => $metadata['title'],
                'desc' => $metadata['desc'],
                'seo_title' => $metadata['seo_title'],
                'seo_desc' => $metadata['seo_desc'],
            ], true) . ";\n";
    }

    private function loadPrompts(): void
    {
        $this->systemPrompt = file_get_contents(
            resource_path('prompts/metadata/tool_metadata_system.txt')
        );

        $this->userPromptTemplate = file_get_contents(
            resource_path('prompts/metadata/tool_metadata_prompt.txt')
        );
    }

    private function userPrompt(
        string $toolName,
        string $categorySlug
    ): string {
        return str_replace(
            ['{{tool_name}}', '{{category_slug}}'],
            [$toolName, $categorySlug],
            $this->userPromptTemplate
        );
    }

    private function cleanResponse(?string $content): ?string
    {
        if (!$content) {
            return null;
        }

        $content = preg_replace('/```json\s*/i', '', $content);
        $content = preg_replace('/```/', '', $content);

        return trim($content);
    }
}

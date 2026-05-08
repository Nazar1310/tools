<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GenerateToolViews extends Command
{
    protected $signature = 'tools:generate {slug}';
    protected $description = 'Generate core.blade.php and seo.blade.php using GPT';

    /**
     * @throws ConnectionException
     */
    public function handle(): int
    {
        $slug = $this->argument('slug');

        $toolPath = resource_path("views/tools/$slug");
        $indexPath = $toolPath . '/index.php';

        if (!file_exists($indexPath)) {
            $this->error("Tool not found: $slug");
            return self::FAILURE;
        }

        $toolData = require $indexPath;

        // --- LOAD PROMPTS ---
        $promptPlan = file_get_contents(resource_path('prompts/prompt_core_plan.txt'));
        $promptPlanSystem = file_get_contents(resource_path('prompts/prompt_core_plan_system.txt'));

        $promptCore = file_get_contents(resource_path('prompts/prompt_core.txt'));
        $promptCoreSystem = file_get_contents(resource_path('prompts/prompt_core_system.txt'));

        $promptSeo = file_get_contents(resource_path('prompts/prompt_seo.txt'));
        $promptSeoSystem = file_get_contents(resource_path('prompts/prompt_seo_system.txt'));

        $instructions = file_get_contents(resource_path('prompts/instructions.txt'));

        // --- LOAD CSS ---
        $toolCss = file_get_contents(public_path('css/tool.css'));
        $seoCss = file_get_contents(public_path('css/seo.css'));

        // --- CONTEXT ---
        $context = json_encode($toolData, JSON_PRETTY_PRINT);

        $this->info('Generating plan...');

        $planResponse = $this->askGPT(
            $promptPlan,
            $promptPlanSystem,
            $context,
            $instructions,
            ''
        );

        if (!$planResponse) {
            $this->error('Plan generation failed.');
            return self::FAILURE;
        }

        $this->info('Generating core...');

        $coreResponse = $this->askGPT(
            $promptCore,
            $promptCoreSystem,
            $context,
            $instructions,
            $toolCss,
            $planResponse
        );

        if (!$coreResponse) {
            $this->error('Core response is not valid.');
            return self::FAILURE;
        }

        file_put_contents($toolPath . '/core.blade.php', $coreResponse);

        $this->info('Generating SEO...');

        $seoResponse = $this->askGPT(
            $promptSeo,
            $promptSeoSystem,
            $context,
            $instructions,
            $seoCss
        );

        if (!$seoResponse) {
            $this->error('Seo response is not valid.');
            return self::FAILURE;
        }

        file_put_contents($toolPath . '/seo.blade.php', $seoResponse);

        $this->info('Done ✅');

        return self::SUCCESS;
    }

    /**
     * @throws ConnectionException
     */
    private function askGPT(
        string $prompt,
        string $system,
        string $context,
        string $instructions,
        string $css,
        ?string $plan = null
    ): ?string {
        $planBlock = $plan ? "\n=== IMPLEMENTATION PLAN ===\n$plan\n" : '';

        $fullPrompt = <<<PROMPT
{$prompt}

=== TOOL DATA ===
{$context}
{$planBlock}
=== CSS STYLES DATA ===
{$this->compressCss($css)}
PROMPT;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-5.4-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $system,
                ],
                [
                    'role' => 'system',
                    'content' => $instructions,
                ],
                [
                    'role' => 'user',
                    'content' => $fullPrompt,
                ],
            ],
            'max_completion_tokens' => 12000,
            'temperature' => 0.2,
            'top_p' => 1.0,
        ]);

        if (!$response->ok()) {
            return null;
        }

        return $this->cleanResponse($response->json('choices.0.message.content'));
    }

    private function compressCss(string $css): string
    {
        $css = preg_replace('!/\*.*?\*/!s', '', $css);
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/\s*([{}|:;,])\s+/', '$1', $css);
        $css = preg_replace('/\s*([{}|:;,])\s*/', '$1', $css);
        $css = str_replace(';}', '}', $css);
        return trim($css);
    }

    private function cleanResponse(?string $content): ?string
    {
        if (!$content) return null;

        $content = preg_replace('/```[a-z]*\n?/i', '', $content);
        $content = str_replace('```', '', $content);

        return trim($content);
    }
}

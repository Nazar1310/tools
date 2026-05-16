<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GenerateToolViews extends Command
{
    protected $signature = 'tools:generate {slug}';
    protected $description = 'Generate tool views using multi-agent GPT pipeline';

    private array $prompts = [];
    private string $instructions = '';

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
        $context = json_encode($toolData, JSON_PRETTY_PRINT);

        $this->loadPrompts();

        $toolCss = file_get_contents(public_path('css/tool.css'));
        $seoCss = file_get_contents(public_path('css/seo.css'));

        $plan = $this->generatePlan($context);

        if (!$plan) {
            $this->error('Plan generation failed.');
            return self::FAILURE;
        }

        $core = $this->generateCore($context, $toolCss, $plan);

        if (!$core) {
            $this->error('Core generation failed.');
            return self::FAILURE;
        }

        $reviewedCore = $this->reviewCore($context, $toolCss, $plan, $core);

        if (!$reviewedCore) {
            $this->error('Core review failed.');
            return self::FAILURE;
        }

        file_put_contents($toolPath . '/core.blade.php', $reviewedCore);

        $seo = $this->generateSeo($context, $seoCss);

        if (!$seo) {
            $this->error('SEO generation failed.');
            return self::FAILURE;
        }

        file_put_contents($toolPath . '/seo.blade.php', $seo);

        $this->info('Done ✅');

        return self::SUCCESS;
    }

    private function loadPrompts(): void
    {
        $this->prompts = [
            'plan' => [
                'prompt' => file_get_contents(resource_path('prompts/plan/prompt_core_plan.txt')),
                'system' => file_get_contents(resource_path('prompts/plan/prompt_core_plan_system.txt')),
                'temperature' => 0.4,
            ],
            'core' => [
                'prompt' => file_get_contents(resource_path('prompts/develop/prompt_core.txt')),
                'system' => file_get_contents(resource_path('prompts/develop/prompt_core_system.txt')),
                'temperature' => 0.15,
            ],
            'core_review' => [
                'prompt' => file_get_contents(resource_path('prompts/review/prompt_core_review.txt')),
                'system' => file_get_contents(resource_path('prompts/review/prompt_core_review_system.txt')),
                'temperature' => 0.0,
            ],
            'seo' => [
                'prompt' => file_get_contents(resource_path('prompts/seo/prompt_seo.txt')),
                'system' => file_get_contents(resource_path('prompts/seo/prompt_seo_system.txt')),
                'temperature' => 0.4,
            ],
        ];

        $this->instructions = file_get_contents(resource_path('prompts/instructions.txt'));
    }

    /**
     * @throws ConnectionException
     */
    private function generatePlan(string $context): ?string
    {
        $this->info('Generating plan...');

        return $this->callAgent(
            agent: 'plan',
            sections: [
                'TOOL DATA' => $context,
            ]
        );
    }

    /**
     * @throws ConnectionException
     */
    private function generateCore(
        string $context,
        string $css,
        string $plan
    ): ?string {
        $this->info('Generating core...');

        return $this->callAgent(
            agent: 'core',
            sections: [
                'TOOL DATA' => $context,
                'IMPLEMENTATION PLAN' => $plan,
                'CSS STYLES DATA' => $this->compressCss($css),
            ]
        );
    }

    /**
     * @throws ConnectionException
     */
    private function reviewCore(
        string $context,
        string $css,
        string $plan,
        string $core
    ): ?string {
        $this->info('Reviewing core...');

        return $this->callAgent(
            agent: 'core_review',
            sections: [
                'TOOL DATA' => $context,
                'IMPLEMENTATION PLAN' => $plan,
                'GENERATED CORE' => $core,
                'CSS STYLES DATA' => $this->compressCss($css),
            ],
        );
    }

    /**
     * @throws ConnectionException
     */
    private function generateSeo(
        string $context,
        string $css
    ): ?string {
        $this->info('Generating SEO...');

        return $this->callAgent(
            agent: 'seo',
            sections: [
                'TOOL DATA' => $context,
                'CSS STYLES DATA' => $this->compressCss($css),
            ]
        );
    }

    /**
     * @throws ConnectionException
     */
    private function callAgent(
        string $agent,
        array $sections,
    ): ?string {
        $promptData = $this->prompts[$agent];

        $content = $promptData['prompt'] . "\n\n";

        foreach ($sections as $title => $body) {
            $content .= "=== {$title} ===\n{$body}\n\n";
        }

        $response = Http::timeout(180)
            ->connectTimeout(15)
            ->withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-5.4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $promptData['system'],
                ],
                [
                    'role' => 'system',
                    'content' => $this->instructions,
                ],
                [
                    'role' => 'user',
                    'content' => trim($content),
                ],
            ],
            'max_completion_tokens' => 12000,
            'temperature' => $promptData['temperature'],
            'top_p' => 1.0,
        ]);

        if (!$response->ok()) {
            $this->error($response->body());
            return null;
        }

        return $this->cleanResponse(
            $response->json('choices.0.message.content')
        );
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

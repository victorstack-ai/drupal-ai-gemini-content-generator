<?php

declare(strict_types=1);

namespace Drupal\ai_gemini_content_generator\Service;

use Drupal\ai_gemini_content_generator\Contracts\ChatClientInterface;
use Drupal\ai_gemini_content_generator\ValueObject\GeneratedContent;
use Drupal\ai_gemini_content_generator\ValueObject\GenerationRequest;

final class ContentGenerator
{
    public function __construct(
        private readonly ChatClientInterface $chatClient,
        private readonly PromptBuilder $promptBuilder,
    ) {
    }

    public function generate(GenerationRequest $request): GeneratedContent
    {
        $prompt = $this->promptBuilder->build($request);
        $response = $this->chatClient->generate($prompt);

        [$title, $body] = $this->extractTitleAndBody($response);

        return new GeneratedContent($title, $body, $prompt);
    }

  /**
   * @return array{0:string,1:string}
   */
    private function extractTitleAndBody(string $response): array
    {
        $lines = preg_split('/\r?\n/', trim($response)) ?: [];
        $title = '';
        $body_lines = $lines;

        if ($lines !== []) {
            $first = trim((string) $lines[0]);
            if (stripos($first, 'title:') === 0) {
                $title = trim(substr($first, 6));
                $body_lines = array_slice($lines, 1);
            }
        }

        if ($title === '') {
            $title = 'AI Draft';
        }

        $body = trim(implode("\n", $body_lines));
        if ($body === '') {
            $body = $response;
        }

        return [$title, $body];
    }
}

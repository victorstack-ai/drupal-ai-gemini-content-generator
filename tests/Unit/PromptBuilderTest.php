<?php

declare(strict_types=1);

namespace Drupal\ai_gemini_content_generator\Tests\Unit;

use Drupal\ai_gemini_content_generator\Service\PromptBuilder;
use Drupal\ai_gemini_content_generator\ValueObject\GenerationRequest;
use PHPUnit\Framework\TestCase;

final class PromptBuilderTest extends TestCase
{
    public function testBuildsPromptWithInputs(): void
    {
        $builder = new PromptBuilder();
        $request = new GenerationRequest('Drupal AI', 'content editors', 'professional', 500, ['Gemini', 'AI']);

        $prompt = $builder->build($request);

        self::assertStringContainsString('Drupal AI', $prompt);
        self::assertStringContainsString('content editors', $prompt);
        self::assertStringContainsString('professional', $prompt);
        self::assertStringContainsString('500', $prompt);
        self::assertStringContainsString('Gemini', $prompt);
    }
}

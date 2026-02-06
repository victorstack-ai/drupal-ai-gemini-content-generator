<?php

declare(strict_types=1);

namespace Drupal\ai_gemini_content_generator\Tests\Unit;

use Drupal\ai_gemini_content_generator\Contracts\ChatClientInterface;
use Drupal\ai_gemini_content_generator\Service\ContentGenerator;
use Drupal\ai_gemini_content_generator\Service\PromptBuilder;
use Drupal\ai_gemini_content_generator\ValueObject\GenerationRequest;
use PHPUnit\Framework\TestCase;

final class ContentGeneratorTest extends TestCase
{
    public function testGeneratesContentAndExtractsTitle(): void
    {
        $chat_client = new class () implements ChatClientInterface {
            public function generate(string $prompt, string $model = ''): string
            {
                return "Title: AI Draft\nThis is the body.";
            }
        };

        $generator = new ContentGenerator($chat_client, new PromptBuilder());
        $request = new GenerationRequest('Drupal AI', '', '', 0, []);

        $result = $generator->generate($request);

        self::assertSame('AI Draft', $result->title());
        self::assertSame('This is the body.', $result->body());
    }

    public function testFallsBackWhenNoTitleProvided(): void
    {
        $chat_client = new class () implements ChatClientInterface {
            public function generate(string $prompt, string $model = ''): string
            {
                return 'Plain body without title.';
            }
        };

        $generator = new ContentGenerator($chat_client, new PromptBuilder());
        $request = new GenerationRequest('Drupal AI', '', '', 0, []);

        $result = $generator->generate($request);

        self::assertSame('AI Draft', $result->title());
        self::assertSame('Plain body without title.', $result->body());
    }
}

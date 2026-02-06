<?php

declare(strict_types=1);

namespace Drupal\ai_gemini_content_generator\Service;

use Drupal\ai_gemini_content_generator\ValueObject\GenerationRequest;

final class PromptBuilder
{
    public function build(GenerationRequest $request): string
    {
        $parts = [];
        $parts[] = sprintf('Write a Drupal-ready article about "%s".', $request->topic());

        if ($request->audience() !== '') {
            $parts[] = sprintf('Audience: %s.', $request->audience());
        }

        if ($request->tone() !== '') {
            $parts[] = sprintf('Tone: %s.', $request->tone());
        }

        if ($request->length() > 0) {
            $parts[] = sprintf('Target length: %d words.', $request->length());
        }

        if ($request->keywords() !== []) {
            $parts[] = sprintf('Include keywords: %s.', implode(', ', $request->keywords()));
        }

        $parts[] = 'Provide a title and body formatted for Drupal editors.';

        return implode(' ', $parts);
    }
}

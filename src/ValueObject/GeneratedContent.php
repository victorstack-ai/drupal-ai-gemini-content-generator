<?php

declare(strict_types=1);

namespace Drupal\ai_gemini_content_generator\ValueObject;

final class GeneratedContent
{
    public function __construct(
        private readonly string $title,
        private readonly string $body,
        private readonly string $prompt,
    ) {
    }

    public function title(): string
    {
        return $this->title;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function prompt(): string
    {
        return $this->prompt;
    }
}

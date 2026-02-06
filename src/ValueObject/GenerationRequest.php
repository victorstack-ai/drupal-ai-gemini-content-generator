<?php

declare(strict_types=1);

namespace Drupal\ai_gemini_content_generator\ValueObject;

final class GenerationRequest
{
  /**
   * @param string[] $keywords
   */
    public function __construct(
        private readonly string $topic,
        private readonly string $audience,
        private readonly string $tone,
        private readonly int $length,
        private readonly array $keywords = [],
        private readonly string $model = '',
    ) {
    }

    public static function fromFormValues(array $values): self
    {
        $keywords = [];
        if (!empty($values['keywords']) && is_string($values['keywords'])) {
            $keywords = array_filter(array_map('trim', explode(',', $values['keywords'])));
        }

        return new self(
            (string) ($values['topic'] ?? ''),
            (string) ($values['audience'] ?? ''),
            (string) ($values['tone'] ?? ''),
            (int) ($values['length'] ?? 0),
            $keywords,
            (string) ($values['model'] ?? ''),
        );
    }

    public function topic(): string
    {
        return $this->topic;
    }

    public function audience(): string
    {
        return $this->audience;
    }

    public function tone(): string
    {
        return $this->tone;
    }

    public function length(): int
    {
        return $this->length;
    }

  /**
   * @return string[]
   */
    public function keywords(): array
    {
        return $this->keywords;
    }

    public function model(): string
    {
        return $this->model;
    }
}

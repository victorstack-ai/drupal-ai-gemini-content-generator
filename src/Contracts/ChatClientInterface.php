<?php

declare(strict_types=1);

namespace Drupal\ai_gemini_content_generator\Contracts;

interface ChatClientInterface
{
  /**
   * Generates content from a prompt.
   */
    public function generate(string $prompt, string $model = ''): string;
}

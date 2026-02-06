<?php

declare(strict_types=1);

namespace Drupal\ai_gemini_content_generator\Client;

use Drupal\ai\AiProviderPluginManager;
use Drupal\ai\OperationType\Chat\ChatInput;
use Drupal\ai\OperationType\Chat\ChatInterface;
use Drupal\ai\OperationType\Chat\ChatMessage;
use Drupal\ai_gemini_content_generator\Contracts\ChatClientInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

final class AiModuleChatClient implements ChatClientInterface
{
    private const SETTINGS_KEY = 'ai_gemini_content_generator.settings';

    public function __construct(
        private readonly AiProviderPluginManager $providerManager,
        private readonly ConfigFactoryInterface $configFactory,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function generate(string $prompt, string $model = ''): string
    {
        $config = $this->configFactory->get(self::SETTINGS_KEY);
        $provider_id = (string) ($config->get('provider_id') ?? 'gemini_ai_studio');
        $model = $model !== '' ? $model : (string) ($config->get('default_model') ?? '');

        try {
            $provider = $this->providerManager->createInstance($provider_id);
            if (!$provider instanceof ChatInterface) {
                throw new RuntimeException(sprintf('Provider "%s" does not support chat operations.', $provider_id));
            }

            $input = new ChatInput([
            new ChatMessage('user', $prompt),
            ]);

            $output = $provider->chat($input, $model, ['ai-gemini-content-generator']);
            if (method_exists($output, 'getText')) {
                  $text = $output->getText();
            } else {
                $text = (string) $output;
            }

            if (!is_string($text) || $text === '') {
                throw new RuntimeException('Gemini response did not include text content.');
            }

            return $text;
        } catch (Throwable $exception) {
            $this->logger->error('AI content generation failed: @message', ['@message' => $exception->getMessage()]);
            throw new RuntimeException('Unable to generate content at this time.');
        }
    }
}

<?php

declare(strict_types=1);

namespace Drupal\ai_gemini_content_generator\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

final class ContentGeneratorSettingsForm extends ConfigFormBase
{
    private const SETTINGS_KEY = 'ai_gemini_content_generator.settings';

    public function getFormId(): string
    {
        return 'ai_gemini_content_generator_settings';
    }

    protected function getEditableConfigNames(): array
    {
        return [self::SETTINGS_KEY];
    }

    public function buildForm(array $form, FormStateInterface $form_state): array
    {
        $config = $this->config(self::SETTINGS_KEY);

        $form['provider_id'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Provider ID'),
        '#default_value' => $config->get('provider_id') ?? 'gemini_ai_studio',
        '#description' => $this->t('AI provider plugin ID to use for chat (for example: gemini_ai_studio).'),
        '#required' => true,
        ];

        $form['default_model'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Default model'),
        '#default_value' => $config->get('default_model') ?? 'gemini-1.5-flash',
        '#description' => $this->t('Model ID used when the generator does not specify one.'),
        '#required' => true,
        ];

        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
        parent::submitForm($form, $form_state);

        $this->configFactory->getEditable(self::SETTINGS_KEY)
        ->set('provider_id', $form_state->getValue('provider_id'))
        ->set('default_model', $form_state->getValue('default_model'))
        ->save();
    }
}

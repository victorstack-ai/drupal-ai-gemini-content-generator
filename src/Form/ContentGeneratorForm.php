<?php

declare(strict_types=1);

namespace Drupal\ai_gemini_content_generator\Form;

use Drupal\ai_gemini_content_generator\Service\ContentGenerator;
use Drupal\ai_gemini_content_generator\ValueObject\GeneratedContent;
use Drupal\ai_gemini_content_generator\ValueObject\GenerationRequest;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Throwable;

final class ContentGeneratorForm extends FormBase
{
    public function __construct(
        private readonly ContentGenerator $contentGenerator,
    ) {
    }

    public static function create(ContainerInterface $container): self
    {
        return new self(
            $container->get('ai_gemini_content_generator.content_generator'),
        );
    }

    public function getFormId(): string
    {
        return 'ai_gemini_content_generator_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state): array
    {
        $form['topic'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Topic'),
        '#required' => true,
        ];

        $form['audience'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Audience'),
        '#description' => $this->t('Example: developers, content editors, marketers.'),
        ];

        $form['tone'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Tone'),
        '#description' => $this->t('Example: friendly, professional, direct.'),
        ];

        $form['length'] = [
        '#type' => 'number',
        '#title' => $this->t('Target word count'),
        '#min' => 150,
        '#max' => 2000,
        '#default_value' => 600,
        ];

        $form['keywords'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Keywords'),
        '#description' => $this->t('Comma-separated keywords to include.'),
        ];

        $form['actions'] = [
        '#type' => 'actions',
        ];
        $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Generate draft'),
        ];

      /** @var \Drupal\ai_gemini_content_generator\ValueObject\GeneratedContent|null $generated */
        $generated = $form_state->get('generated_content');
        if ($generated instanceof GeneratedContent) {
            $form['result'] = [
            '#type' => 'details',
            '#title' => $this->t('Generated Draft'),
            '#open' => true,
            ];
            $form['result']['title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Suggested title'),
            '#default_value' => $generated->title(),
            '#attributes' => ['readonly' => 'readonly'],
            ];
            $form['result']['body'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Draft body'),
            '#default_value' => $generated->body(),
            '#rows' => 16,
            '#attributes' => ['readonly' => 'readonly'],
            ];
        }

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
        $request = GenerationRequest::fromFormValues($form_state->getValues());

        try {
            $generated = $this->contentGenerator->generate($request);
            $form_state->set('generated_content', $generated);
            $this->messenger()->addStatus($this->t('Draft generated successfully.'));
        } catch (Throwable $exception) {
            $this->messenger()->addError($this->t('Draft generation failed. Check logs for details.'));
        }

        $form_state->setRebuild(true);
    }
}

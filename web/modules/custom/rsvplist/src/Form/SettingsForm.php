<?php

declare(strict_types=1);

namespace Drupal\rsvplist\Form;

use Drupal\node\Entity\Node;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure rsvplist settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'rsvplist_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['rsvplist.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('rsvplist.settings');
    $allow = $config->get('allow_type');
    if (is_null($allow)) {
      $allow = [];
    }
    kpr($allow);
    dump($allow);
    kint($allow);

    $node = Node::load(61);
    kint($node->toArray());
    kint($node);

    // $this->messenger()->addError('');
    $types = node_type_get_names();
    $form['allowed_types'] = [
      '#type' => 'checkboxes',
      '#title' => t('The content types that can be enabled for RSVP collection'),
      '#description' => t('selct the content types that can be enabled for RSVP collection'),
      '#required' => TRUE,
      '#default_value' => $allow,
      '#options' => $types,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if ($form_state->getValue('example') === 'wrong') {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('The value is not correct.'),
    //     );
    //   }
    // @endcode
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $sendtype = array_filter($form_state->getValue('allowed_types'));
    sort($sendtype);

    $this->config('rsvplist.settings')
      ->set('allow_type', $sendtype)
      ->save();
    parent::submitForm($form, $form_state);
  }

}

<?php

declare(strict_types=1);

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use function PHPUnit\Framework\isNull;

/**
 * Provides a rsvplist form.
 */
final class RSVPForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'rsvplist_email_form';
    // Return 'rsvplist_r_s_v_p';.
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $nid = 1;
    $node1 = \Drupal::routeMatch()->getParameter('node');
    if (isNull($node1)) {
      $nid = 0;
    }
    else {
      $nid = $node1->id();
    }

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#title_display' => 'before',
      '#description' => $this->t('Input Email Address'),
      '#required' => TRUE,
      '#default_value' => '',
      '#size' => '',
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Send'),
      ],
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    if (mb_strlen($form_state->getValue('email')) < 10) {
      $form_state->setErrorByName(
        'email',
        $this->t('email address should be at least 10 characters.'),
      );
    }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $sendmail = $form_state->getValue("email");
    $sendnid = $form_state->getValue("nid");
    $this->messenger()->addStatus($this->t("you  message is @sendmail", ['@sendmail' => $sendmail]));
    $this->messenger()->addStatus($this->t("you  nid is @sendnid", ['@sendnid' => $sendnid]));

    // $form_state->setRedirect('<front>');
  }

}

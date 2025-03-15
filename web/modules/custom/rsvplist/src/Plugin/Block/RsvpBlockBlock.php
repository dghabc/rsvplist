<?php

declare(strict_types=1);

namespace Drupal\rsvplist\Plugin\Block;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Access\AccessResult;

/**
 * Provides a rsvp block block.
 */
#[Block(
  id: 'rsvplist_rsvp_block',
  admin_label: new TranslatableMarkup('rsvp block'),
  category: new TranslatableMarkup('Custom'),
)]
final class RsvpBlockBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {

    return \Drupal::formBuilder()->getForm('Drupal\rsvplist\Form\RSVPForm');
  }

  /**
   *
   */
  public function blockAccess(AccountInterface $account) {
    $node1 = \Drupal::routeMatch()->getParameter('node');

    if (!(is_null($node1))) {
      $type = $node1->gettype();
      $config = \Drupal::config('rsvplist.settings');
      $allow = $config->get('allow_type');
      if (in_array($type, $allow)) {
        $enable = \Drupal::service('rsvplist.enable');

        if ($enable->isEnable($node1)) {
          return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
          # code...
        }
      }
    }

    return AccessResult::forbidden();
  }

}

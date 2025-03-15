<?php

declare(strict_types=1);

namespace Drupal\rsvplist\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for rsvplist routes.
 */
final class RsvpReportController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {

    $content = [];
    $content['message'] = [
      '#markup' => '这是显示报表',
    ];
    $headers = [
      t('name'),
      t('mail'),
      t('title'),
    ];
    $tablerows = $this->load();

    $content['table'] = [
      '#type' => 'table',
      '#title' => t(''),
      '#title_display' => 'before',
      '#description' => t(''),
      '#required' => TRUE,
      '#header' => $headers,
      '#rows' => $tablerows,
      '#empty' => '没有数据',
      '#responsive' => '',
      '#sticky' => '',
      '#footer' => '',
      '#caption' => '',
    ];

    return $content;
  }

  /**
   *
   */
  public function load() {
    try {
      /**
      * @var Drupal\Core\Database\Connection
      */
      $database_service = \Drupal::service('database');
      $query = $database_service->select('rsvplist', 'r');
      $query->join('users_field_data', 'u', 'r.uid=u.uid');
      $query->join('node_field_data', 'n', 'r.nid=n.nid');
      $query->addfield('u', 'name', 'username');
      $query->addfield('n', 'title');
      $query->addfield('r', 'mail');
      $entries = $query->execute()->fetchall(\PDO::FETCH_ASSOC);
      return $entries;
    }
    catch (\Exception $e) {
      // Throw $th;.
      $this->messenger()->addError($e);
      return NULL;
    }

  }

}

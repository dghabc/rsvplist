<?php

declare(strict_types=1);

namespace Drupal\rsvplist;

use Drupal\Core\Database\Connection;
use Drupal\node\Entity\Node;

/**
 * @todo Add class description.
 */
final class Enable {

  /**
   * Constructs an Enable object.
   *
   * @var Drupal\Core\Database\Connection
   */
  protected $dbconnection;

  public function __construct(
    private readonly Connection $connection,
  ) {
    $this->dbconnection = $connection;
  }

  /**
   * @todo Add method description.
   */
  public function isEnable(Node &$node) {
    if ($node->isNew()) {
      return FALSE;
    }
    try {
      $select = $this->dbconnection->select('rsvplist_enable', 're');
      $select->fields('re', ['nid']);
      $select->condition('nid', $node->id());
      $result = $select->execute();
      return !(empty($result->fetchCol()));

    }
    catch (\Exception $th) {
      /**
        * @var Drupal\Core\Messenger\Messenger
        */
      $messenger_service = \Drupal::service('messenger');
      $messenger_service->addError($th);
      return NULL;
    }
  }

  /**
   *
   */
  public function setEnable(Node $node) {
    $aa = $this->isEnable($node);

    try {
      if (!($this->isEnable($node))) {
        $insert = $this->dbconnection->insert('rsvplist_enable');
        $insert->fields(['nid']);
        $insert->values([$node->id()]);
        $insert->execute();
        \Drupal::messenger()->addStatus('加入 RSVP');
      }
    }
    catch (\Exception $e) {
      \Drupal::service('messenger')->addError($e);
    }
  }

  /**
   *
   */
  public function delEnable(Node $node) {
    try {

      $delete = $this->dbconnection->delete('rsvplist_enable');
      $delete->condition('nid', $node->id());
      $delete->execute();
      \Drupal::messenger()->addError('删除 RSVP');
    }
    catch (\Exception $e) {
      \Drupal::service('messenger')->addError($e);
    }

  }

}

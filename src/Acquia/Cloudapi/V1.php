<?php

namespace Drupal\cloudhooks\Acquia\Cloudapi;

use Drupal\cloudhooks\Acquia\Cloudapi\V1\Database;
use Drupal\cloudhooks\Acquia\Cloudapi\V1\Varnish;
use Drupal\cloudhooks\Acquia\CloudapiInterface;

/**
 * Class V1.
 *
 * Partial implementation of the CloudAPI v1 RESTful web service.
 *
 * @package Drupal\cloudhooks\Acquia\Api
 */
class V1 implements CloudapiInterface {

  /**
   * A partial implementation of the Varnish component of the CloudAPI (v1).
   *
   * @var \Drupal\cloudhooks\Acquia\Cloudapi\V1\Varnish
   */
  protected $varnish;

  /**
   * A partial implementation of the Database component of the ClouAPI (v1).
   *
   * @var \Drupal\cloudhooks\Acquia\Cloudapi\V1\Database
   */
  protected $database;

  public function __construct(Database $database, Varnish $varnish) {
    $this->varnish = $varnish;
    $this->database = $database;
  }

  public function getVarnish() {
    return $this->varnish;
  }

  public function getDatabase() {
    return $this->database;
  }

  /**
   * {@inheritdoc}
   */
  public function clearVarnish($application, $environment) {
    $this->varnish->clear($application, $environment);
  }

  /**
   * {@inheritdoc}
   */
  public function backupDatabases($application, $environment) {
    $this->database->backup($application, $environment);
  }
}

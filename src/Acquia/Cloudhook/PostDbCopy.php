<?php

namespace Drupal\cloudhooks\Acquia\Cloudhook;

class PostDbCopy extends CloudhookBase {

  protected $dbName;
  protected $sourceEvn;

  /**
   * {@inheritdoc}
   */
  public function getHook() {
    return static::POST_DB_COPY;
  }

  protected function detectEnvironment() {
    parent::detectEnvironment();

    global $argv;

    $this->dbName = $argv[3];
    $this->sourceEnv = $argv[4];
  }

  public function getDbName() {
    return $this->dbName;
  }

  public function getSourceEnv() {
    return $this->sourceEvn;
  }
}
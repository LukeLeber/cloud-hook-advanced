<?php

namespace Drupal\cloudhooks\Acquia\Cloudhook;

class PostFilesCopy extends CloudhookBase {

  protected $sourceEnv;

  /**
   * {@inheritdoc}
   */
  public function getHook() {
    return static::POST_FILES_COPY;
  }

  protected function detectEnvironment() {
    parent::detectEnvironment();

    global $argv;

    $this->sourceEnv = $argv[3];
  }

  public function getSourceEnv() {
    return $this->sourceEnv;
  }
}
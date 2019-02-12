<?php

namespace Drupal\cloudhooks\Acquia\Cloudhook;

use Drupal\cloudhooks\Acquia\CloudhookInterface;

abstract class CloudhookBase implements CloudhookInterface {

  protected $application;
  protected $environment;

  protected function detectEnvironment() {
    global $argv;

    $this->application = $argv[1];
    $this->environment = $argv[2];
  }

  public function __construct() {
    $this->detectEnvironment();
  }

  /**
   * Retrieves the application that is being targeted by this hook.
   *
   * @return string
   *   The application name.
   */
  public function getApplication() {
    return $this->application;
  }

  /**
   * Retrieves the environment that is being targeted by this hook.
   *
   * @return string
   *   The environment name.
   */
  public function getEnvironment() {
    return $this->environment;
  }
}
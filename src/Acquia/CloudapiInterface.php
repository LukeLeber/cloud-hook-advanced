<?php

namespace Drupal\cloudhooks\Acquia;

/**
 * Interface Cloudapi.
 *
 * Acts as an adapter between the Acquia CloudAPI and the hooks that use it.
 *
 * @TODO: Implement the CloudAPI v2 when it supports all required features.
 * Currently, the v2 API lacks the ability to query task records.
 *
 * @package Drupal\cloudhooks\Acquia\Api
 */
interface CloudapiInterface {

  /**
   * Clears the Varnish cache for all domains on the target environment.
   *
   * @param $application
   *   The application that is being targeted.
   * @param $environment
   *   The environment that is being targeted.
   *
   * @throws \Exception
   *   If any error occurs while clearing the caches.
   */
  public function clearVarnish($application, $environment);

  /**
   * Creates user backups of all databases on the target environment.
   *
   * @param $application
   *   The application that is being targeted.
   * @param $environment
   *   The environment that is being targeted.
   *
   * @throws \Exception
   *   If any error occurs while clearing the caches.
   */
  public function backupDatabases($application, $environment);
}
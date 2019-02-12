<?php

namespace Drupal\cloudhooks\Acquia\Cloudapi\V1;

use Exception;

class Varnish extends RequestBase {

  const PURGE_TASK_REFRESH_SECONDS_DEFAULT = 1;
  const PURGE_TASK_TIMEOUT_SECONDS_DEFAULT = 60 * 5;

  /**
   * Attempts to retrieve the domains from the application and environment.
   *
   * @param $application
   *   The application that is being targeted.
   * @param $environment
   *   The environment that is being targeted.
   *
   * @return array|string[]
   *   An array of all domains on the application and environment.
   *
   * @throws \Exception
   *   If any error occurs while fetching the domain list.
   *
   * @see https://cloudapi.acquia.com/#GET__sites__site_envs__env_domains-instance_route
   */
  protected function getDomains($application, $environment) {

    $endpoint = sprintf(
      'https://cloudapi.acquia.com/v1/sites/prod:%s/envs/%s/domains.json',
      $application,
      $environment
    );
    $method = static::HTTP_METHOD_GET;

    $response = $this->call($endpoint, $method);

    return array_map(function($record) {
      return $record['name'];
    }, $response);
  }

  /**
   * Attempts to purge the Varnish cache for the specified domain.
   *
   * @param string $application
   *   The application that is being targeted.
   * @param string $environment
   *   The environment that is being targeted.
   * @param string $domain
   *   The domain that is being targeted.
   *
   * @return array
   *   An associative array containing the task record for the request.
   *
   * @throws \Exception
   *   If any error occurs while purging the provided domain.
   */
  protected function clearDomain($application, $environment, $domain) {
    $endpoint = sprintf(
      'https://cloudapi.acquia.com/v1/sites/prod:%s/envs/%s/domains/%s/cache.json',
      $application,
      $environment,
      $domain
    );
    $method = static::HTTP_METHOD_DELETE;

    return $this->call($endpoint, $method);
  }

  /**
   * @param $application
   * @param $environment
   *
   * @throws \Exception
   */
  public function clear($application, $environment) {
    $domains = $this->getDomains($application, $environment);

    $errors = [];

    foreach($domains as $domain) {
      try {
        $task = $this->clearDomain($application, $environment, $domain);
        $this->blockUntilCompleteOrTimeout($application, $task, static::PURGE_TASK_REFRESH_SECONDS_DEFAULT, static::PURGE_TASK_TIMEOUT_SECONDS_DEFAULT);
      }
      catch(\Exception $e) {
        $i18n = \_('Failed to purge Varnish cache for "%s": %s');
        $errors[] = sprintf($i18n, $domain, $e->getMessage());
      }
    }

    if($errors) {
      throw new Exception(join(PHP_EOL, $errors));
    }
  }
}

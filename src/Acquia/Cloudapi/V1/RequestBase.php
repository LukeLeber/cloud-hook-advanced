<?php

namespace Drupal\cloudhooks\Acquia\Cloudapi\V1;

use Drupal\cloudhooks\Acquia\Cloudapi\Configuration;

abstract class RequestBase {

  const HTTP_METHOD_GET = 'GET';
  const HTTP_METHOD_DELETE = 'DELETE';
  const HTTP_METHOD_POST = 'POST';

  /**
   * The configuration for the CloudAPI REST service.
   *
   * @var \Drupal\cloudhooks\Acquia\Cloudapi\Configuration
   */
  protected $configuration;

  public function __construct() {
    $this->configuration = new Configuration();
  }

  /**
   * Attempts to create a new cURL resource object.
   *
   * @return resource
   *   A resource handle.
   *
   * @throws \Exception
   *   If the resource could not be created for any reason.
   */
  protected function getCurlResource() {
    $handle = \curl_init();

    if (!$handle) {
      $i18n = \_('Unable to initialize cURL resource.');
      throw new \Exception($i18n);
    }

    return $handle;
  }

  /**
   * Sets the cURL options for the provided API call.
   *
   * @param $handle
   *   The cURL resource handle to configure.
   * @param $endpoint
   *   The endpoint of the REST call.
   * @param $method
   *   The HTTP method of the REST call.
   *
   * @return array
   *   An associative array of cURL options.
   *
   * @throws \Exception
   *   If any error occurs with forming the options.
   */
  protected function setCurlOptions($handle, $endpoint, $method) {

    $options = [
      CURLOPT_URL => $endpoint,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_USERPWD => "{$this->configuration->getEmail()}:{$this->configuration->getKey()}",
    ];

    if (!\curl_setopt_array($handle, $options)) {
      $i18n = \_('Unable to set cURL request options.');
      throw new \Exception($i18n);
    }

    return $options;
  }

  /**
   * Executes the cURL request as configured in the provided resource.
   *
   * @param $handle
   *   The cURL resource handle.
   *
   * @return string
   *   The raw response received from the remote endpoint.
   *
   * @throws \Exception
   *   If any HTTP error occurs.
   */
  protected function executeCurl($handle) {

    $raw_response = curl_exec($handle);

    if (curl_errno($handle)) {
      $i18n = 'Call to Acquia CloudAPI failed with: "%s".';
      $diagnostic = sprintf($i18n, curl_error($handle));
      throw new \Exception($diagnostic);
    }

    return $raw_response;
  }

  /**
   * Performs an API call to the Acquia Cloudapi V1 REST service.
   *
   * @param $endpoint
   *   The REST endpoint that is being requested.
   * @param $method
   *   The REST method that is being requested.
   *
   * @return array
   *   An associative array that contains the response data.
   *
   * @throws \Exception
   *   If any error occurs while making the REST request.
   */
  protected function call($endpoint, $method) {

    $response = NULL;
    $handle = $this->getCurlResource();

    try {
      $this->setCurlOptions($handle, $endpoint, $method);
      $raw_response = $this->executeCurl($handle);
      $response = \json_decode($raw_response, TRUE);

      if (NULL === $response) {
        throw new \Exception(\_('Response was not in the correct format.'));
      }
    } finally {
      \curl_close($handle);
    }

    return $response;
  }

  /**
   * @param $application
   * @param $task_id
   *
   * @return array
   * @throws \Exception
   */
  public function getTaskInfo($application, $task_id) {

    $endpoint = sprintf(
      'https://cloudapi.acquia.com/v1/sites/prod:%s/tasks/%s.json',
      $application,
      $task_id
    );
    $method = static::HTTP_METHOD_GET;

    return $this->call($endpoint, $method);
  }

  /**
   * Blocks the current thread until the provided task completes or times out.
   *
   * @param $application
   *   The application that is being targeted.
   * @param $task
   *   The task that is being waited for.
   * @param int $refresh_rate
   *   The number of seconds to wait between task refreshes.
   * @param int $timeout
   *   The number of seconds that must elapse before forcing a failure.
   *
   * @throws \Exception
   *   If the task fails or does not complete within the specified timeout.
   */
  public function blockUntilCompleteOrTimeout($application, $task, $refresh_rate, $timeout) {

    $limit = \time() + $timeout;

    while($task['completed'] === NULL && \time() < $limit) {
      // Request a task update at most once per second.
      sleep($refresh_rate);
      $task = $this->getTaskInfo($application, $task['id']);
    }

    if($task['completed'] === NULL) {
      $i18n = \_('Blocking task did not complete within "%s" seconds.');
      $diagnostic = \sprintf($i18n, $timeout);
      throw new \Exception($diagnostic);
    }

    if($task['state'] !== 'done') {
      $i18n = \_('Blocking task completed with a state of "%s".');
      $diagnostic = \sprintf($i18n, $task['state']);
      throw new \Exception($diagnostic);
    }
  }
}
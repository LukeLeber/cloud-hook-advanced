<?php

/**
 * @file
 *
 * Contains convenience wrappers around Acquia Cloud API calls.
 */

/**
 * Retrieves a list of databases from the provided application and environment.
 *
 * @param string $application
 *   The name of the application that houses the provided environment.
 * @param string $environment
 *   The name of the environment to look up databases for.
 *
 * @return array
 *   The list of databases on the provided application and environment.
 */
function acapi_get_databases($application, $environment) {

  return acapi_call(
    "https://cloudapi.acquia.com/v1/sites/prod:{$application}/envs/{$environment}/dbs.json",
    'GET'
  );
}

/**
 * Begins backing up the specified database.
 *
 * @param string $application
 *   The name of the application that houses the target environment.
 * @param string $environment
 *   The name of the environment that the backup request should affect.
 * @param string $database
 *   The name of the database to back up.
 *
 * @return array
 *   An array that holds task information.
 */
function acapi_backup_database($application, $environment, $database) {

  return acapi_call(
    "https://cloudapi.acquia.com/v1/sites/prod:{$application}/envs/{$environment}/dbs/{$database}/backups.json",
    'POST'
  );
}

/**
 * Retrieves a list of domains from the provided application and environment.
 *
 * @param string $application
 *   The name of the application that houses the provided environment.
 * @param string $environment
 *   The name of the environment to look up domains for.
 *
 * @return array
 *   The list of domains on the provided application and environment.
 */
function acapi_get_domains($application, $environment) {

  return acapi_call(
    "https://cloudapi.acquia.com/v1/sites/prod:{$application}/envs/{$environment}/domains.json",
    'GET'
  );
}

/**
 * Begins purging the Varnish cache for the provided domain.
 *
 * Varnish is not necessarily cleared when this function returns.  The returned
 * task object should be used to periodically query the Acquia task queue to
 * determine when the cache has been fully purged.
 *
 * @param string $application
 *   The name of the application that houses the target environment.
 * @param string $environment
 *   The name of the environment that the purge request should affect.
 * @param string $domain
 *   The domain to purge.
 *
 * @return array
 *   An array that holds task information.
 */
function acapi_clear_varnish($application, $environment, $domain) {

  return acapi_call(
    "https://cloudapi.acquia.com/v1/sites/prod:{$application}/envs/{$environment}/domains/{$domain}/cache.json",
    'DELETE'
  );
}

/**
 * Retrieves information regarding a specific task with an application.
 *
 * @param string $application
 *   The name of the application that the task exists in.
 * @param string $task_id
 *   The ID of the task to look up.
 *
 * @return array
 *   An array that holds task information.
 */
function acapi_task_info($application, $task_id) {

  return acapi_call(
    "https://cloudapi.acquia.com/v1/sites/prod:{$application}/tasks/{$task_id}.json",
    'GET'
  );
}

/**
 * Makes a REST request to the Acquia Cloud API.
 *
 * If the request fails, then this script is immediately terminated.
 *
 * @param string $url
 *   The REST endpoint URL.
 * @param string $method
 *   The HTTP method to use.
 * @return array
 *   The JSON response from the cloud API.
 */
function acapi_call($url, $method) {

  $response = NULL;
  $error = TRUE;

  if ($ch = curl_init()) {

    // Load the API credentials from a file within the environment.
    $conf = loadConfiguration();

    $options = [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_USERPWD => "{$conf['email']}:{$conf['key']}",
    ];

    if (curl_setopt_array($ch, $options)) {
      $response = curl_exec($ch);
      $error = curl_errno($ch);
    }
    curl_close($ch);
  }

  if($error) {
    // There is no recovering from this.
    exit(1);
  }

  return json_decode($response, TRUE);
}

/**
 * Attempts to load the Acquia CloudAPI configuration from the environment.
 *
 * If the configuration fails to load, this script terminates immediately.
 *
 * @return array
 *   A configuration array that contains 'email' and 'key' elements.
 */
function loadConfiguration() {

  $conf_file = $_SERVER['HOME'] . DIRECTORY_SEPARATOR . '.acquia' . DIRECTORY_SEPARATOR . 'cloudapi.conf';

  if(!file_exists($conf_file)) {
    // There is no recovering from this.
    echo "CloudAPI configuration was not found at '{$conf_file}'.";
    exit(1);
  }

  $conf = json_decode(
    file_get_contents($conf_file),
    TRUE
  );

  if(!$conf || !isset($conf['email']) && !isset($conf['key'])) {
    echo "CloudAPI configuration file at '{$conf_file}' is corrupt.";
    exit(1);
  }

  return $conf;
}

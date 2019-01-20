<?php

/**
 * @file
 *
 * Contains convenience wrappers around Drush calls.
 */

/**
 * Runs database updates on the provided application and environment.
 *
 * @param string $application
 *   The name of the application that houses the target environment.
 * @param string $environment
 *   The name of the environment that should have its Drupal database updated.
 *
 * @return array
 *   The result of the Drush call.
 */
function drush_update_database($application, $environment) {

  return drush_call(
    $application,
    $environment,
    'updatedb',
    '--yes'
  );
}

/**
 * Runs entity schema updates on the provided application and environment.
 *
 * @param string $application
 *   The name of the application that houses the target environment.
 * @param string $environment
 *   The name of the environment that should have its Drupal database updated.
 *
 * @return array
 *   The result of the Drush call.
 */
function drush_update_entities($application, $environment) {

  return drush_call(
    $application,
    $environment,
    'entup',
    '--yes'
  );
}

/**
 * Runs configuration import on the provided application and environment.
 *
 * @param string $application
 *   The name of the application that houses the target environment.
 * @param string $environment
 *   The name of the environment that should have its configuration imported.
 * @param string $source
 *   The configuration source to import.
 *
 * @return array
 *   The result of the Drush call.
 */
function drush_import_configuration($application, $environment, $source = 'sync') {

  return drush_call(
    $application,
    $environment,
    'config-import',
    $source,
    '--yes'
  );
}

/**
 * Clears the Drush cache for the provided application and environment.
 *
 * @param string $application
 *   The name of the application that houses the target environment.
 * @param string $environment
 *   The name of the environment that should have its Drush cache cleared.
 *
 * @return array
 *   The result of the Drush call.
 */
function drush_clear_drush($application, $environment) {
  return drush_call(
    $application,
    $environment,
    'cache-rebuild',
    'drush'
  );
}

/**
 * Clears the Drupal cache for the provided application and environment.
 *
 * @param string $application
 *   The name of the application that houses the target environment.
 * @param string $environment
 *   The name of the environment that should have its Drupal cache cleared.
 *
 * @return array
 *   The result of the Drush call.
 */
function drush_clear_cache($application, $environment) {

  return drush_call(
    $application,
    $environment,
    'cache-rebuild',
    'all'
  );
}

/**
 * Gets the provided setting from the provided application and environment.
 *
 * @param string $application
 *   The name of the application that houses the target environment.
 * @param string $environment
 *   The name of the environment to look up the setting in.
 * @param string $setting
 *   The name of the setting to look up.
 *
 * @return array
 *   The result of the Drush call.
 */
function drush_config_get($application, $environment, $setting) {

  return drush_call(
    $application,
    $environment,
    'config-get',
    $setting,
    '--format=json'
  );
}

/**
 * Runs a Drush command in an external process.
 *
 * If the command fails in any way, this script is immediately terminated.
 *
 * @param string $application
 *   The name of the application that houses the target environment.
 * @param string $environment
 *   The environment to run the command against.
 * @param string $command_name
 *   The name of the Drush command to execute.
 * @param string ...$arguments
 *   Any number of arguments to pass to the Drush command.
 *
 * @return array
 *   The result of the Drush call.
 */
function drush_call($application, $environment, $command_name, ...$arguments) {

  // Sanitize all inputs.
  $drush_alias = escapeshellarg("@{$application}.{$environment}");
  $command_name = escapeshellarg($command_name);
  $args_string = array_reduce($arguments, function ($carry, $arg) {
    return $carry ? ($carry . ' ' . escapeshellarg($arg)) : escapeshellarg($arg);
  }, '');

  // Sanitize the command itself.
  $command = escapeshellcmd("drush9 {$drush_alias} {$command_name} {$args_string}");

  $output = NULL;
  $return_value = NULL;

  exec($command, $output, $return_value);

  if($return_value !== 0) {
    exit(1);
  }

  $json = implode('', $output);
  return json_decode($json, TRUE);
}
<?php

namespace Drupal\cloudhooks\Acquia\Cloudapi;

class Configuration {

  const ACAPI_CONF_DIR = '.acquia';
  const ACAPI_CONF_FILE = 'cloudapi.conf';

  /**
   * A cached copy of the environment configuration.
   *
   * @var array
   */
  protected $configuration;

  protected function getConfigFilePath() {
    $config_file = join(\DIRECTORY_SEPARATOR, [
      getenv('HOME'),
      static::ACAPI_CONF_DIR,
      static::ACAPI_CONF_FILE,
    ]);

    return $config_file;
  }

  /**
   * Reads the provided configuration file from the current environment.
   *
   * @param $config_file
   *   The configuration file to read.
   *
   * @return array
   *   The parsed configuration file.
   *
   * @throws \Exception
   *   If the file does not exist, is not readable, or contains invalid JSON.
   */
  protected function readConfiguration($config_file) {

    $configuration = FALSE;

    if (\is_readable($config_file)) {
      $configuration = \file_get_contents($config_file);
    }

    if (FALSE === $configuration) {
      $i18n = \_('CloudAPI configuration could not be read from "%s".');
      $diagnostic = \sprintf($i18n, $config_file);
      throw new \Exception($diagnostic);
    }

    $json = \json_decode($configuration);

    if (NULL === $json) {
      $i18n = \_('CloudAPI configuration at "%s" did not contain valid JSON.');
      $diagnostic = \sprintf($i18n, $config_file);
      throw new \Exception($diagnostic);
    }

    return $configuration;
  }

  protected function loadConfig() {

    $config_file = $this->getConfigFilePath();

    $configuration = $this->readConfiguration($config_file);

    $required_keys = ['email', 'key'];

    foreach ($required_keys as $required_key) {
      if (!\array_key_exists($required_key, $configuration)) {
        $i18n = \_('CloudAPI configuration file at "%s" does not contain required key "%s".');
        $diagnostic = \sprintf($i18n, $config_file, $required_key);
        throw new \Exception($diagnostic);
      }
    }

    $this->configuration = $configuration;
  }

  /**
   * Attempt to create a new configuration object.
   *
   * @return array
   *   A configuration array that contains 'email' and 'key' elements.
   *
   * @throws \Exception
   *   If the configuration cannot be loaded from the environment.
   */
  public function __construct() {
    $this->loadConfig();
  }

  public function getEmail() {
    return $this->configuration['email'];
  }

  public function getKey() {
    return $this->configuration['key'];
  }
}
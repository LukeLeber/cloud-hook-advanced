<?php

namespace Drupal\cloudhooks\Acquia\Cloudapi\V1;

/**
 * Class DatabaseBackup.
 *
 * Provides a mechanism to back up all databases on a particular environment.
 *
 * This mechanism will block subsequent script execution until all databases
 * have either been backed up, or one or more errors occur.
 *
 * @package Drupal\cloudhooks\Acquia\Api\V1
 */
class Database extends RequestBase {

  /**
   * The number of seconds to wait between querying a backup task state.
   *
   * @var int
   */
  const BACKUP_TASK_REFRESH_SECONDS_DEFAULT = 5;

  /**
   * The number of seconds to wait before marking the backup as having failed.
   *
   * @var int
   */
  const BACKUP_TASK_TIMEOUT_SECONDS_DEFAULT = 60 * 15;

  /**
   * Attempts to retrieve all databases on the target environment.
   *
   * @param $application
   *   The application that is being targeted.
   * @param $environment
   *   The environment that is being targeted.
   *
   * @return array
   *   The list of domains on the target environment.
   *
   * @throws \Exception
   *   If any error occurs while retrieving the domain list.
   */
  protected function getDatabases($application, $environment) {
    $endpoint = sprintf(
      'https://cloudapi.acquia.com/v1/sites/prod:%s/envs/%s/dbs.json',
      $application,
      $environment
    );
    $method = static::HTTP_METHOD_GET;

    $databases = $this->call($endpoint, $method);

    return array_map(function($database) {
      return $database['name'];
    }, $databases);
  }

  /**
   * Attempts to create a user backup the target database.
   *
   * @param $application
   *   The application that is being targeted.
   * @param $environment
   *   The environment that is being targeted.
   * @param $database
   *   The database that is being targeted.
   *
   * @return array
   *   The task record for the backup operation.
   *
   * @throws \Exception
   *   If any error occurs while starting the backup operation.
   */
  protected function backupDatabase($application, $environment, $database) {
    $endpoint = \sprintf(
      'https://cloudapi.acquia.com/v1/sites/prod:%s/envs/%s/dbs/%s/backups.json',
      $application,
      $environment
    );
    $method = static::HTTP_METHOD_POST;

    return $this->call($endpoint, $method);
  }

  /**
   * Attempts to back up all databases on the target environment.
   *
   * @param $application
   *   The application that is being targeted.
   * @param $environment
   *   The environment that is being targeted.
   *
   * @throws \Exception
   *   If any database backup fails to complete successfully.
   */
  public function backup($application, $environment) {
    $errors = [];
    $databases = $this->getDatabases($application, $environment);

    foreach($databases as $database) {
      try {
        $task = $this->backupDatabase($application, $environment, $database);
        $this->blockUntilCompleteOrTimeout(
          $application,
          $task,
          static::BACKUP_TASK_REFRESH_SECONDS_DEFAULT,
          static::BACKUP_TASK_TIMEOUT_SECONDS_DEFAULT
        );
      }
      catch(\Exception $e) {
        $i18n = \_('Failed to backup database "%s": %s');
        $errors[] = sprintf($i18n, $database, $e->getMessage());
      }
    }

    if($errors) {
      throw new \Exception(join(PHP_EOL, $errors));
    }

  }
}

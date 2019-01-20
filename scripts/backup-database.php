#!/usr/bin/php
<?php
#
# Cloud Hook: backup-database
#
# Backs up all databases on the current environment.
#
# This hook blocks until all backup tasks have completed or any task fails.
#
# The task must complete before the downstream update database tasks fires.
#

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'acapi.php');

$application = $argv[1];
$environment = $argv[2];

$databases = acapi_get_databases($application, $environment);

foreach($databases as $database) {
  $task = acapi_backup_database($application, $environment, $database['name']);
  $waited_for = 0;
  do {
    sleep(5);
    $task = acapi_task_info($application, $task['id']);
  } while(++$waited_for < 180 && $task['completed'] === NULL);

  if($task['state'] !== 'done') {
    // The task failed to complete within 15 minutes or has otherwise failed.
    exit(2);
  }
}

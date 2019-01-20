#!/usr/bin/php
<?php
#
# Cloud Hook: varnish-cache-clear
#
# Purges the Varnish cache for all domains on the current environment.
#
# This hook blocks until all purge tasks have completed or any task fails.
#
# The task must complete before the downstream Cloudflare purger runs.
#

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'acapi.php');

$application = $argv[1];
$environment = $argv[2];

$domains = acapi_get_domains($application, $environment);

foreach ($domains as $domain) {
  $task = acapi_clear_varnish($application, $environment, $domain['name']);
  $waited_for = 0;
  do {
    sleep(1);
    $task = acapi_task_info($application, $task['id']);
  } while(++$waited_for < 30 && $task['completed'] === NULL);

  if($task['state'] !== 'done') {
    // The task failed to complete within 30 seconds or has otherwise failed.
    exit(2);
  }
}

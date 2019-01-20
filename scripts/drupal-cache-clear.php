#!/usr/bin/php
<?php
#
# Cloud Hook: drupal-cache-clear
#
# Purges the Drupal caches and blocks until completion.
#
# Exit Codes
# 0 - Success
# 1 - Error: A drush command has failed to execute.
#

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'drush.php');

$application = $argv[1];
$environment = $argv[2];

drush_clear_drush($application, $environment);
drush_clear_cache($application, $environment);

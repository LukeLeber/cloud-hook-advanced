#!/usr/bin/php
<?php
#
# Cloud Hook: update-database
#
# Run drush updatedb in the target environment.

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'drush.php');

$application = $argv[1];
$environment = $argv[2];

drush_update_database($application, $environment);
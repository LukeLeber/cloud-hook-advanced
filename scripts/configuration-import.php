#!/usr/bin/php
<?php
#
# Cloud Hook: configuration-import
#
# Run drush config-import in the target environment.

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'drush.php');

$application = $argv[1];
$environment = $argv[2];

drush_import_configuration($application, $environment);

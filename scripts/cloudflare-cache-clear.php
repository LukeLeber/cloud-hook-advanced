#!/usr/bin/php
<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'acapi.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'cfapi.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'drush.php');

$application = $argv[1];
$environment = $argv[2];

// Pull the Cloudflare settings from the Drupal environment.
$cf_settings = drush_config_get(
  $application,
  $environment,
  'cloudflare.settings'
);

// Parse out the domain list from the current environment.
$domains = array_map(
  function($domain) {
    return $domain['name'];
  },
  acapi_get_domains($application, $environment)
);

// Purge the Cloudflare zone of all domains.
cfapi_purge_domains(
  $cf_settings['email'],
  $cf_settings['apikey'],
  $cf_settings['zone_id'],
  $domains
);

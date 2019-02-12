<?php

// Form a path to the composer autoloader (may or may not exist).
$vendor_autoloader = join(DIRECTORY_SEPARATOR, [
  'vendor',
  'autoload.php',
]);

// Autoload as usual if we can, otherwise exit with a useful diagnostic.
if (is_readable($vendor_autoloader)) {

  /* @noinspection PhpIncludeInspection */
  require_once $vendor_autoloader;
}
else {
  $i18n = _('Unable to find dependencies. Did you forget to run "%s"?');
  $fatal_error = NULL;
  if (defined(DEV_DEPENDENCIES_REQUIRED) && DEV_DEPENDENCIES_REQUIRED === TRUE) {
    $fatal_error = sprintf($i18n, 'composer install');
  }
  else {
    $fatal_error = sprintf($i18n, 'composer install --no-dev --optimize-autoloader');
  }
  die($fatal_error);
}

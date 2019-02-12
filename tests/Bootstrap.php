<?php

namespace lleber\Tests;

const DEV_DEPENDENCIES_REQUIRED = TRUE;

// Form a path to the autoload file in the repository root.
$autoloader = join(DIRECTORY_SEPARATOR, [
  dirname(dirname(__FILE__)),
  'autoload.php',
]);

/* @noinspection PhpIncludeInspection */
require_once($autoloader);
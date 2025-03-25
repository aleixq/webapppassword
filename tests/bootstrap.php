<?php

declare(strict_types=1);

if (!defined('PHPUNIT_RUN')) {
	define('PHPUNIT_RUN', 1);
}

require_once __DIR__.'/../../../lib/base.php';

// Fix for "Autoload path not allowed: .../tests/lib/testcase.php"
\OC::$loader->addValidRoot(OC::$SERVERROOT.'/tests');

// Fix for "Autoload path not allowed: .../webapppassword/tests/testcase.php"
\OC_App::loadApp('webapppassword');

OC_Hook::clear();

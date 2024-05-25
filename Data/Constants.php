<?php

define('UT_PHP_CORE_VERSION', UT_Php_Core\Version::parse('0.0.0.-1'));
define('APP_VERSION', UT_Php_Core\Version::parse('1.0.0.0', [
    'UT.Php.Core' => UT_PHP_CORE_VERSION
]));

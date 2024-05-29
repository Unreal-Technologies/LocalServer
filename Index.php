<?php

require_once 'Data/Pll/Loader.php';

try {
    $coreVersion = \Pll\Loader::initialize('UT.Php.Core');
} catch (\Exception $ex) {
    $coreVersion = \Pll\Loader::packageDevelop(__DIR__ . '/../Compiler/UT.Php.Core');
}

require_once 'Data/Constants.php';
UT_PHP_CORE_VERSION -> update($coreVersion);

$root = \UT_Php_Core\IO\Directory::fromString(__DIR__);

$router = new \UT_Php_Core\Routing\Router($root, true);
$router -> add(\UT_Php_Core\Routing\RequestMethods::Get, '/', function () {
    header('location: /main');
});

foreach (\UT_Php_Core\IO\Directory::fromString('Pages') -> list() as $iDiskManager) {
    if ($iDiskManager instanceof UT_Php_Core\IO\IFile && $iDiskManager -> extension() === 'php') {
        $relative = $iDiskManager -> relativeTo($root);
        $class = '\\' . str_replace('/', '\\', str_replace('.' . $iDiskManager -> extension(), '', $relative));

        $router -> add(
            \UT_Php_Core\Routing\RequestMethods::Get,
            '/' . $iDiskManager -> basename(),
            function () use ($router, $class, $relative) {
                require_once $relative;
                echo new $class($router);
            }
        );
    }
}

$router -> match();

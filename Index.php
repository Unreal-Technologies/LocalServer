<?php
require_once 'PllLoader/PllLoader.php';
PllLoader::initialize('UT.Php.Core:1.0.0.0');

$root = \UT_Php_Core\IO\Directory::fromString(__DIR__);

$router = new \UT_Php_Core\Routing\Router($root, true);
$router -> add(\UT_Php_Core\Enums\RequestMethods::Get, '/', function () {
    header('location: /main');
});

foreach (\UT_Php_Core\IO\Directory::fromString('Pages') -> list() as $iDiskManager) {
    if ($iDiskManager instanceof UT_Php_Core\Interfaces\IFile && $iDiskManager -> extension() === 'php') {
        $relative = $iDiskManager -> relativeTo($root);
        $class = '\\' . str_replace('/', '\\', str_replace('.' . $iDiskManager -> extension(), '', $relative));

        $router -> add(
                \UT_Php_Core\Enums\RequestMethods::Get,
            '/' . $iDiskManager -> basename(),
            function () use ($router, $class, $relative) {
                require_once $relative;
                echo new $class($router);
            }
        );
    }
}

$router -> match();

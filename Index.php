<?php

require_once 'UT.php/.init';

$root = \UT_Php\IO\Directory::fromString(__DIR__);

$router = new \UT_Php\Routing\Router($root, true);
$router -> add(\UT_Php\Enums\RequestMethods::Get, '/', function () {
    header('location: /main');
});

foreach (\UT_Php\IO\Directory::fromString('Pages') -> list() as $iDiskManager) {
    if ($iDiskManager instanceof UT_Php\Interfaces\IFile && $iDiskManager -> extension() === 'php') {
        $relative = $iDiskManager -> relativeTo($root);
        $class = '\\' . str_replace('/', '\\', str_replace('.' . $iDiskManager -> extension(), '', $relative));

        $router -> add(
            \UT_Php\Enums\RequestMethods::Get,
            '/' . $iDiskManager -> basename(),
            function () use ($router, $class, $relative) {
                require_once $relative;
                echo new $class($router);
            }
        );
    }
}

$router -> match();

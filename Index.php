<?php
require_once 'UT.php/.init';
require_once 'Pages/cv.php';
require_once 'Pages/downloads.php';
require_once 'Pages/mapviewer.php';
require_once 'Pages/home.php';

$root = \UT_Php\IO\Directory::fromString(__DIR__);

$router = new \UT_Php\Routing\Router($root, true);
$router -> add(\UT_Php\Enums\RequestMethods::Get, '/', function() { home(); });
$router -> add(\UT_Php\Enums\RequestMethods::Get, '/home', function() { home(); });
$router -> add(\UT_Php\Enums\RequestMethods::Get, '/mapviewer', function() { mapviewer(); });
$router -> add(\UT_Php\Enums\RequestMethods::Get, '/downloads', function() { downloads(); });
$router -> add(\UT_Php\Enums\RequestMethods::Get, '/cv', function() { cv(); });

$router -> match();

function cv()
{
    global $root;
    
    echo new \Pages\Cv($root);
}

function downloads()
{
    global $root;
    
    echo new Pages\Downloads($root);
}

function mapviewer()
{
    global $root;
    
    echo new Pages\Mapviewer($root);
}

function home()
{
    global $root;
    
    echo new \Pages\Home($root);
}

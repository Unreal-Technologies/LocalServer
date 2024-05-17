<?php
require_once 'UT.php/.init';

$root = \UT_Php\IO\Directory::fromString(__DIR__);

$router = new \UT_Php\Routing\Router($root, true);
$router -> add(\UT_Php\Enums\RequestMethods::Get, '/', function() 
{ 
    global $root;
    require_once 'Pages/home.php';
    
    echo new \Pages\Home($root);
});
$router -> add(\UT_Php\Enums\RequestMethods::Get, '/mapviewer', function() { 
    global $root;
    require_once 'Pages/mapviewer.php';
    
    echo new Pages\Mapviewer($root);
});
$router -> add(\UT_Php\Enums\RequestMethods::Get, '/downloads', function() { 
    global $root;
    require_once 'Pages/downloads.php';
    
    echo new Pages\Downloads($root);
});
$router -> add(\UT_Php\Enums\RequestMethods::Get, '/cv', function() { 
    global $root;
    require_once 'Pages/cv.php';
    
    echo new \Pages\Cv($root);
});

$router -> match();
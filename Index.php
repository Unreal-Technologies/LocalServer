<?php
require_once 'UT.php/.init';

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
    require_once('Ut.php/.init');
    require_once('Data/Cv.php');

    $xmlFile = new UT_Php\IO\Common\Xml('Data/Cv-PeterOvereijnder.xml');
    $cv = new Data\Cv($xmlFile, 'root');
    ?>
    <!DOCTYPE>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="style.css"/>
            <title>Curriculum Vitae</title>
        </head>
        <body>
            <?php
            echo $cv -> asHtml();
            ?>
        </body>
    </html>
    <?php
}

function downloads()
{
    require_once 'UT.php/.init';
    $downloads = UT_Php\IO\Directory::fromString("Content/Downloads");
    if (!$downloads -> exists()) {
        $downloads -> create();
    }
    $root = UT_Php\IO\Directory::fromString('.');

    $tree = new UT_Php\Html\Directory($downloads, $root);
    ?>
    <!DOCTYPE>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="style.css"/>
            <title>Downloads</title>
        </head>
        <body>
            <?php
            echo $tree;
            ?>
        </body>
    </html>
    <?php
}

function mapviewer()
{
    require_once 'UT.Php/.init';
    require_once 'MapView/.init';

    $ini = parse_ini_file('Content/Configuration/MapView.ini', true);
    ini_set('memory_limit', '2G');

    $generatedWorlds = UT_Php\IO\Directory::fromString($ini['MapView']['GeneratedWorlds']);
    $steamCommon = UT_Php\IO\Directory::fromString($ini['MapView']['SteamCommon']);
    $root = UT_Php\IO\Directory::fromString(__DIR__);

    $selected = count($_GET) === 0 || !isset($_GET['selected']) ? null : $_GET['selected'];

    if ($selected == null) {
        $s = microtime(true);
        $listing = new MapView\MapView($generatedWorlds, $steamCommon, $root);
        $stream = (string)$listing;
        $e = microtime(true);
        $dif = ($e - $s) * 1000;

        $stream .= '<hr />' . number_format($dif, 0, ',', '.') . ' ms';
        ?>
        <html>
            <head>
                <link rel="stylesheet" type="text/css" href="style.css"/>
                <title>Map Viewer</title>
                <style type="text/css">
                    table, th, td
                    {
                        border-width: 0px;
                        border-style: solid;
                        border-color: #000000;
                        border-spacing: 0;
                    }

                    th, td
                    {
                        border-right-width: 1px;
                        border-top-width: 1px;
                    }

                    tr th:first-child
                    {
                        border-left-width: 1px;
                    }

                    tr:first-child th:first-child
                    {
                        border-top-width: 0px;
                        border-left-width: 0px;
                    }

                    tr:last-of-type th, tr:last-of-type td
                    {
                        border-bottom-width: 1px;
                    }

                    td
                    {
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <?php echo $stream; ?>
            </body>
        </html>
        <?php
        exit;
    }
}

function home()
{
    $version = new \UT_Php\Version(
        1,
        0,
        0,
        4,
        ['UT.Php' => UT_PHP_VERSION]
    );
    
    ?>
    <!DOCTYPE>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="style.css"/>
            <title>Home</title>
        </head>
        <body>
            <div id="menu" class="left">
                <a href="MapViewer" target="Content">Map Viewer</a>
                <a href="Downloads" target="Content">Downloads</a>
            </div>
            <div id="frame" class="left">
                <iframe name="Content"></iframe>
            </div>
            <span id="copyright">
                <a href="cv" target="Content">&copy; Peter Overeijnder <?php echo date('Y'); ?></a>
            </span>
            <span id="version">
                <a href="https://github.com/Unreal-Technologies" target="_blank">Version <?php echo $version; ?></a>
            </span>
        </body>
    </html>
    <?php
}

<?php
require_once 'UT.Php/.init';
require_once 'MapView/.init';

$ini = parse_ini_file('MapView.ini', true);
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
?>

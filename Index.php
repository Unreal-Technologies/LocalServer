<?php
require_once 'UT.php/.init';

const APP_VERSION = new \UT_Php\Version(
    1,
    0,
    0,
    2,
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
            <a href="MapViewer.php" target="Content">Map Viewer</a>
            <a href="Downloads.php" target="Content">Downloads</a>
        </div>
        <div id="frame" class="left">
            <iframe name="Content"></iframe>
        </div>
        <span id="copyright">
            <a href="cv.php" target="Content">&copy; Peter Overeijnder <?php echo date('Y'); ?></a>
        </span>
        <span id="version">
            <a href="https://github.com/Unreal-Technologies" target="_blank">Version <?php echo APP_VERSION; ?></a>
        </span>
    </body>
</html>
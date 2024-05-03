<?php
require_once('UT.php/.init');

const APP_VERSION = new \UT_Php\Version(1, 0, 0, 0, [
    'UT.Php' => UT_PHP_VERSION]
);

?>
<!DOCTYPE>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css"/>
        <title>Home</title>
    </head>
    <body>
        <div id="menu">
            <a href="Downloads.php" target="Content">Downloads</a>
            <a href="MapViewer.php" target="Content">Map Viewer</a>
        </div>
        <div id="frame">
            <iframe name="Content"></iframe>
        </div>
        <span id="copyright">&copy; Peter Overeijnder <?php echo date('Y'); ?></span>
        <span id="version">Version <?php echo APP_VERSION; ?></span>
    </body>
</html>
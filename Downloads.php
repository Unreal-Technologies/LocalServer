<?php
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
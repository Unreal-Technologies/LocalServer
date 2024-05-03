<?php
require_once('UT.php/.init');
$downloads = UT_Php\IO\Directory::FromString("Downloads");
if(!$downloads -> Exists())
{
    $downloads -> Create();
}
$root = UT_Php\IO\Directory::FromString('.');

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
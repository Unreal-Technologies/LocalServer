<?php
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
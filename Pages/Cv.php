<?php
namespace Pages;

class Cv extends \UT_Php_Core\Html\PageController
{
    /**
     * @var Data\Cv
     */
    private $cv;

    /**
     * @return void
     */
    public function initialize(): void
    {
        require_once('Data/Cv.php');

        $xmlFile = new \UT_Php_Core\IO\Common\Xml('Data/Cv-PeterOvereijnder.xml');
        $this -> cv = new \Data\Cv($xmlFile, 'root');
    }

    /**
     * @param string $title
     * @param UT_Php_Core\Interfaces\IFile[] $css
     * @return void
     */
    public function setup(string &$title, array &$css): void
    {
        $title = 'Curriculum Vitae';
        $css[] = \UT_Php_Core\IO\File::fromString(__DIR__ . '/Cv.css');
    }

    public function render(): string
    {
        return $this -> cv -> asHtml();
    }
}

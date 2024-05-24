<?php
namespace Pages;

require_once 'Tools/Work/UT_Php_Core+Html.php';

class Downloads extends \UT_Php_Core\Html\PageController
{
    /**
     * @var \UT_Php_Core\Html\Directory
     */
    private $tree;

    /**
     * @return void
     */
    public function initialize(): void
    {
        $downloads = \UT_Php_Core\IO\Directory::fromString("Content/Downloads");
        if (!$downloads -> exists()) {
            $downloads -> create();
        }

        $this -> tree = new \UT_Php_Core\Html\Directory($downloads, $this -> root());
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return '<h1>Downloads</h1><hr />' . $this -> tree;
    }

    /**
     * @param string $title
     * @param \UT_Php_Core\Interfaces\IFile[] $css
     * @return void
     */
    public function setup(string &$title, array &$css): void
    {
        $title = 'Downloads';
    }
}

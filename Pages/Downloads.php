<?php

namespace Pages;

class Downloads extends \UT_Php\Html\PageController
{
    /**
     * @var \UT_Php\Html\Directory
     */
    private $tree;

    /**
     * @return void
     */
    public function initialize(): void
    {
        $downloads = \UT_Php\IO\Directory::fromString("Content/Downloads");
        if (!$downloads -> exists()) {
            $downloads -> create();
        }

        $this -> tree = new \UT_Php\Html\Directory($downloads, $this -> root());
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
     * @param \UT_Php\Interfaces\IFile[] $css
     * @return void
     */
    public function setup(string &$title, array &$css): void
    {
        $title = 'Downloads';
    }
}

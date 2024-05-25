<?php

namespace Pages;

class Main extends \UT_Php_Core\Html\PageController
{
    /**
     * @return void
     */
    public function initialize(): void
    {
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $html = '<div id="menu" class="left">';
        $html .= '<a href="Home" target="Content">Home</a>';
        $html .= '<a href="MapViewer" target="Content">Map Viewer</a>';
        $html .= '<a href="Downloads" target="Content">Downloads</a>';
        $html .= '</div>';
        $html .= '<div id="frame" class="left">';
        $html .= '<iframe name="Content" src="home"></iframe>';
        $html .= '</div>';
        $html .= '<span id="copyright">';
        $html .= '<a href="cv" target="Content">&copy; Peter Overeijnder ' . date('Y') . '</a>';
        $html .= '</span>';
        $html .= '<span id="version">';
        $html .= '<a href="https://github.com/Unreal-Technologies" target="_blank">Version ' .
            APP_VERSION .
            '</a>';
        $html .= '</span>';
        return $html;
    }

    /**
     * @param string $title
     * @param array $css
     * @return void
     */
    public function setup(string &$title, array &$css): void
    {
        $title = 'A Lonely Gameserver';
    }
}

<?php
namespace Pages;

require_once 'Tools/Work/UT_Php_Core+Html.php';

class Mapviewer extends \UT_Php_Core\Html\PageController
{
    /**
     * @var \UT_Php_Core\IO\Directory
     */
    private \UT_Php_Core\IO\Directory $generatedWorlds;

    /**
     * @var \UT_Php_Core\IO\Directory
     */
    private \UT_Php_Core\IO\Directory $steamCommon;

    /**
     * @return void
     */
    public function initialize(): void
    {
        require_once 'MapView/.init';

        $ini = parse_ini_file('Content/Configuration/MapView.ini', true);
        ini_set('memory_limit', '2G');

        $this -> generatedWorlds = \UT_Php_Core\IO\Directory::fromString($ini['MapView']['GeneratedWorlds']);
        $this -> steamCommon = \UT_Php_Core\IO\Directory::fromString($ini['MapView']['SteamCommon']);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $listing = new \MapView\MapView($this -> generatedWorlds, $this -> steamCommon, $this -> root());
        $stream = (string)$listing;

        return '<h1>Map Viewer</h1><hr />' . $stream;
    }

    /**
     * @param string $title
     * @param array $css
     * @return void
     */
    public function setup(string &$title, array &$css): void
    {
        $title = 'Map Viewer';
        $css[] = \UT_Php_Core\IO\File::fromString(__DIR__ . '/TableView.css');
    }
}

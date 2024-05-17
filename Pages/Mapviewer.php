<?php
namespace Pages;

class Mapviewer extends \UT_Php\Html\PageController
{
    /**
     * @var \UT_Php\Interfaces\IDirectory
     */
    private $generatedWorlds;
    
    /**
     * @var \UT_Php\Interfaces\IDirectory
     */
    private $steamCommon;
    
    /**
     * @return void
     */
    public function initialize(): void 
    {
        require_once 'MapView/.init';

        $ini = parse_ini_file('Content/Configuration/MapView.ini', true);
        ini_set('memory_limit', '2G');

        $this -> generatedWorlds = \UT_Php\IO\Directory::fromString($ini['MapView']['GeneratedWorlds']);
        $this -> steamCommon = \UT_Php\IO\Directory::fromString($ini['MapView']['SteamCommon']);
    }
    
    /**
     * @return string
     */
    public function render(): string 
    {
        $s = microtime(true);
        $listing = new \MapView\MapView($this -> generatedWorlds, $this -> steamCommon, $this -> root());
        $stream = (string)$listing;
        $e = microtime(true);
        $dif = ($e - $s) * 1000;

        $stream .= '<hr />' . number_format($dif, 0, ',', '.') . ' ms';
        
        return $stream;
    }
    
    /**
     * @param string $title
     * @param array $css
     * @return void
     */
    public function setup(string &$title, array &$css): void 
    {
        $title = 'Map Viewer';
        $css[] = \UT_Php\IO\File::fromString(__DIR__.'/Mapviewer.css');
    }
}
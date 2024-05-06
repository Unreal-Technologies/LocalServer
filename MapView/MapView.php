<?php
namespace MapView;

class MapView
{
    /**
     * @var World[]
     */
    private $worlds = [];
    
    /**
     * @var SevenDaysToDieApp[]
     */
    private $clients = [];
    
    /**
     * @var array
     */
    private $comparison = [];
    
    /**
     * @var \UT_Php\IO\Directory
     */
    private $root = [];
    
    /**
     * @param \UT_Php\IO\Directory $generatedWorlds
     * @param \UT_Php\IO\Directory $steamCommon
     * @param \UT_Php\IO\Directory $root
     */
    public function __construct(
        \UT_Php\IO\Directory $generatedWorlds,
        \UT_Php\IO\Directory $steamCommon,
        \UT_Php\IO\Directory $root
    ) {
        $this -> root = $root;
        foreach ($generatedWorlds -> list() as $world) {
            if ($world instanceof \UT_Php\IO\Directory &&
                $world -> contains('/^biomes.png$/i') &&
                $world -> contains('/^map_info.xml$/i')
            ) {
                $this -> worlds[] = new World($world);
            }
        }
        foreach ($steamCommon -> list() as $app) {
            if ($app instanceof \UT_Php\IO\Directory &&
                (
                    $app -> contains('/^7DaysToDie.exe$/i') ||
                    $app -> contains('/^7DaysToDieServer.exe$/i')
                )
            ) {
                $sdtdApp = new SevenDaysToDieApp($app);
                if ($sdtdApp -> isValid()) {
                    $this -> clients[] = $sdtdApp;
                }
            }
        }
        
        $this -> compare();
    }
    
    /**
     * @return void
     */
    private function compare(): void
    {
        $buffer = [];
        foreach ($this -> worlds as $wi => $world) {
            $render = $world -> render();
            $createRender = $render === null || !$render -> exists();
            
            foreach ($this -> clients as $ci => $client) {
                $isCompatable = $this -> compareWorldClient($world, $client);
                $key = $wi.'.'.$ci;
                $buffer[$key] = $isCompatable;
                
                if ($createRender && $isCompatable) {
                    $image = \UT_Php\Drawing\Image::getImage($world -> biomes());
                    $fc = \UT_Php\Drawing\Color::fromRGB(192, 192, 192);
                    $bc = \UT_Php\Drawing\Color::fromRGB(0, 0, 0);
                    $fcTrader = \UT_Php\Drawing\Color::fromRGB(0, 192, 0);
                    $fcSettlement = \UT_Php\Drawing\Color::fromRGB(192, 0, 0);
                    $wpc = \UT_Php\Drawing\Color::fromRGB(0, 0, 192);
                    
                    if ($image -> gdOpen()) {
                        $half = $image -> size() -> x() / 2;
                        foreach ($world -> prefabs() as $key => $prefab) {
                            foreach ($prefab as $locationData) {
                                if ($locationData === null) {
                                    continue;
                                }
                                
                                $prefabGame = (array)$client -> prefabs()[$key];
                                if (!isset($prefabGame['X'])) {
                                    continue;
                                }
                                
                                $foregroundColor = $fc;
                                $borderColor = $bc;
                                if (preg_match('/^trader_/i', $key)) {
                                    $foregroundColor = $fcTrader;
                                } elseif (preg_match('/^DFalls_settlement/i', $key)) {
                                    $foregroundColor = $fcSettlement;
                                }
                                
                                $rotation = $locationData['Rotation'] * 90;
                                
                                $xGame = $locationData['Location'][0];
                                $zGame = $locationData['Location'][2];
                                
                                $xMap = $xGame === 0 ? $half : ($xGame < 0 ? $half - abs($xGame) : $half + $xGame);
                                $yMap = $zGame === 0 ? $half : ($zGame < 0 ? $half + abs($zGame) : $half - $zGame);

                                $wMap = $prefabGame['X'] * 0.95;
                                $hMap = $prefabGame['Z'] * 0.95;
                                
                                
                                $location = new \UT_Php\Drawing\Point2D($xMap, $yMap);
                                $size = new \UT_Php\Drawing\Point2D($wMap, $hMap);
                                $rect = new \UT_Php\Drawing\Rectangle($size, $location, $rotation);

                                $image -> gdDrawRectangle($rect, $foregroundColor, $borderColor);
                            }
                        }
                        foreach ($world -> spawnPoints() as $spawnpoint) {
                            $xGame = (int)$spawnpoint[0];
                            $zGame = (int)$spawnpoint[2];
                            
                            $xMap = $xGame === 0 ? $half : ($xGame < 0 ? $half - abs($xGame) : $half + $xGame);
                            $yMap = $zGame === 0 ? $half : ($zGame < 0 ? $half + abs($zGame) : $half - $zGame);
                            
                            $location = new \UT_Php\Drawing\Point2D($xMap, $yMap);
                            $size = new \UT_Php\Drawing\Point2D(30, 30);
                            $rect = new \UT_Php\Drawing\Rectangle($size, $location, 0);
                            
                            $image -> gdDrawEllipse($rect, $wpc, $bc);
                        }
                        
                        $image -> gdSaveAs($world -> render());
                    }
                }
            }
        }
        $this -> comparison = $buffer;
    }
    
    /**
     * @param  World             $world
     * @param  SevenDaysToDieApp $client
     * @return bool
     */
    private function compareWorldClient(World $world, SevenDaysToDieApp $client): bool
    {
        $versionOk = $this -> compareWorldClientVersion($world, $client);
        if (!$versionOk) {
            return false;
        }
        $prefabsOk = $this -> compareWorldClientPrefabs($world, $client);
        if (!$prefabsOk) {
            return false;
        }
        
        return true;
    }
    
    /**
     * @param  World             $world
     * @param  SevenDaysToDieApp $client
     * @return bool
     */
    private function compareWorldClientPrefabs(World $world, SevenDaysToDieApp $client): bool
    {
        $wPrefabs = array_keys($world -> prefabs());
        $cPrefabs = array_keys($client -> prefabs());

        foreach ($wPrefabs as $prefab) {
            if (!in_array($prefab, $cPrefabs)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * @param  World             $world
     * @param  SevenDaysToDieApp $client
     * @return bool
     */
    private function compareWorldClientVersion(World $world, SevenDaysToDieApp $client): bool
    {
        $wVersion = $world -> version();
        $cVersion = $client -> version();
        
        return $wVersion['Release'] === $cVersion['Release'] &&
                $wVersion['Major'] === $cVersion['Major'] &&
                $wVersion['Minor'] === $cVersion['Minor'];
    }
            
    /**
     * @return string
     */
    public function __toString(): string
    {
        $html = '<table>';
        $html .= '<tr>';
        $html .= '<th></th>';
        $html .= '<th>Render<br /><small>Click to Enlarge</small></th>';
        foreach ($this -> clients as $client) {
            $version = $client -> version();
            
            $html .= '<th>'.
                    $client -> name().
                    '<br><small>'.
                    $version['Release'].' '.$version['Major'].'.'.$version['Minor'].
                    ' (b'.$version['Beta'].
                    ')</small></th>';
        }
        $html .= '</tr>';
        foreach ($this -> worlds as $wi => $world) {
            $version = $world -> version();
            
            $html .= '<tr>';
            $html .= '<th>'.
                    $world -> name().
                    '<br><small>'.
                    $version['Release'].' '.$version['Major'].'.'.$version['Minor'].
                    ' (b'.$version['Beta'].
                    ')</small></th>';
            $image = $world -> visuals() -> relativeTo($this -> root);
            $html .= '<td><a href="'.$image.'" target="_blank"><img width="100px" src="'.$image.'" /></a></td>';
            foreach ($this -> clients as $ci => $client) {
                $key = $wi.'.'.$ci;
                $state = $this -> comparison[$key];
                
                $html .= '<td>'.($state ? 'Ja' : 'Nee').'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        
        return $html;
    }
}

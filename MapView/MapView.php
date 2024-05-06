<?php
namespace MapView;

class MapView
{
    /**
     * @var World[]
     */
    private $_worlds = [];
    
    /**
     * @var SevenDaysToDieApp[]
     */
    private $_clients = [];
    
    /**
     * @var array
     */
    private $_comparison = [];
    
    /**
     * @var \UT_Php\IO\Directory
     */
    private $_root = [];
    
    /**
     * @param \UT_Php\IO\Directory $generatedWorlds
     * @param \UT_Php\IO\Directory $steamCommon
     * @param \UT_Php\IO\Directory $root
     */
    function __construct(\UT_Php\IO\Directory $generatedWorlds, \UT_Php\IO\Directory $steamCommon, \UT_Php\IO\Directory $root)
    {
        $this -> _root = $root;
        foreach($generatedWorlds -> List() as $world)
        {
            if($world instanceof \UT_Php\IO\Directory && $world -> Contains('/^biomes.png$/i') && $world -> Contains('/^map_info.xml$/i')) {
                $this -> _worlds[] = new World($world);
            }
        }
        foreach($steamCommon -> List() as $app)
        {
            if($app instanceof \UT_Php\IO\Directory && ($app -> Contains('/^7DaysToDie.exe$/i') || $app -> Contains('/^7DaysToDieServer.exe$/i'))) {
                $sdtdApp = new SevenDaysToDieApp($app);
                if($sdtdApp -> IsValid()) {
                    $this -> _clients[] = $sdtdApp;
                }
            }
        }
        
        $this -> Compare();
    }
    
    /**
     * @return void
     */
    private function Compare(): void
    {
        $buffer = [];
        foreach($this -> _worlds as $wi => $world)
        {
            $render = $world -> Render();
            $createRender = $render === null || !$render -> Exists();
            
            foreach($this -> _clients as $ci => $client)
            {
                $isCompatable = $this -> Compare_World_Client($world, $client);
                $key = $wi.'.'.$ci;
                $buffer[$key] = $isCompatable;
                
                if($createRender && $isCompatable) {
                    $image = \UT_Php\Drawing\Image::GetImage($world -> Biomes());
                    $fc = \UT_Php\Drawing\Color::FromRGB(192, 192, 192);
                    $bc = \UT_Php\Drawing\Color::FromRGB(0, 0, 0);
                    $fcTrader = \UT_Php\Drawing\Color::FromRGB(0, 192, 0);
                    $fcSettlement = \UT_Php\Drawing\Color::FromRGB(192, 0, 0);
                    $wpc = \UT_Php\Drawing\Color::FromRGB(0, 0, 192);
                    
                    if($image -> GD_Open()) {
                        $half = $image -> Size() -> X() / 2;
                        foreach($world -> Prefabs() as $key => $prefab)
                        {
                            foreach($prefab as $locationData)
                            {
                                if($locationData === null) {
                                    continue;
                                }
                                
                                $prefabGame = (array)$client -> Prefabs()[$key];
                                if(!isset($prefabGame['X'])) {
                                    continue;
                                }
                                
                                $foregroundColor = $fc;
                                $borderColor = $bc;
                                if(preg_match('/^trader_/i', $key)) {
                                    $foregroundColor = $fcTrader;
                                }
                                else if(preg_match('/^DFalls_settlement/i', $key)) {
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

                                $image -> GD_Draw_Rectangle($rect, $foregroundColor, $borderColor);
                            }
                        }
                        foreach($world -> SpawnPoints() as $spawnpoint)
                        {
                            $xGame = (int)$spawnpoint[0];
                            $zGame = (int)$spawnpoint[2];
                            
                            $xMap = $xGame === 0 ? $half : ($xGame < 0 ? $half - abs($xGame) : $half + $xGame);
                            $yMap = $zGame === 0 ? $half : ($zGame < 0 ? $half + abs($zGame) : $half - $zGame);
                            
                            $location = new \UT_Php\Drawing\Point2D($xMap, $yMap);
                            $size = new \UT_Php\Drawing\Point2D(30, 30);
                            $rect = new \UT_Php\Drawing\Rectangle($size, $location, 0);
                            
                            $image -> GD_Draw_Ellipse($rect, $wpc, $bc);
                        }
                        
                        $image -> GD_SaveAs($world -> Render());
                    }
                }
            }
        }
        $this -> _comparison = $buffer;
    }
    
    /**
     * @param  World             $world
     * @param  SevenDaysToDieApp $client
     * @return bool
     */
    private function Compare_World_Client(World $world, SevenDaysToDieApp $client): bool
    {
        $versionOk = $this -> Compare_World_Client_Version($world, $client);
        if(!$versionOk) {
            return false;
        }
        $prefabsOk = $this -> Compare_World_Client_Prefabs($world, $client);
        if(!$prefabsOk) {
            return false;
        }
        
        return true;
    }
    
    /** 
     * @param  World             $world
     * @param  SevenDaysToDieApp $client
     * @return bool
     */
    private function Compare_World_Client_Prefabs(World $world, SevenDaysToDieApp $client): bool
    {
        $wPrefabs = array_keys($world -> Prefabs());
        $cPrefabs = array_keys($client -> Prefabs());

        foreach($wPrefabs as $prefab)
        {
            if(!in_array($prefab, $cPrefabs)) {
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
    private function Compare_World_Client_Version(World $world, SevenDaysToDieApp $client): bool
    {
        $wVersion = $world -> Version();
        $cVersion = $client -> Version();
        
        return $wVersion['Release'] === $cVersion['Release'] && $wVersion['Major'] === $cVersion['Major'] && $wVersion['Minor'] === $cVersion['Minor'];
    }
            
    /**
     * @return string
     */
    function __toString(): string
    {
        $html = '<table>';
        $html .= '<tr>';
        $html .= '<th></th>';
        $html .= '<th>Render<br /><small>Click to Enlarge</small></th>';
        foreach($this -> _clients as $client)
        {
            $version = $client -> Version();
            
            $html .= '<th>'.$client -> Name().'<br><small>'.$version['Release'].' '.$version['Major'].'.'.$version['Minor'].' (b'.$version['Beta'].')</small></th>';
        }
        $html .= '</tr>';
        foreach($this -> _worlds as $wi => $world)
        {
            $version = $world -> Version();
            
            $html .= '<tr>';
            $html .= '<th>'.$world -> Name().'<br><small>'.$version['Release'].' '.$version['Major'].'.'.$version['Minor'].' (b'.$version['Beta'].')</small></th>';
            $image = $world -> Visuals() -> RelativeTo($this -> _root);
            $html .= '<td><a href="'.$image.'" target="_blank"><img width="100px" src="'.$image.'" /></a></td>';
            foreach($this -> _clients as $ci => $client)
            {
                $key = $wi.'.'.$ci;
                $state = $this -> _comparison[$key];
                
                $html .= '<td>'.($state ? 'Ja' : 'Nee').'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        
        return $html;
    }
}

?>
<?php
namespace MapView;

class SevenDaysToDieApp
{
    /**
     * @var bool
     */
    private $_isValid = false;
    
    /**
     * @var string
     */
    private $_name = null;
    
    /**
     * @var array
     */
    private $_version = [];
    
    /**
     * @var string[]
     */
    private $_prefabs = [];
    
    /**
     * @return array
     */
    public function Prefabs(): array
    {
        return $this -> _prefabs;
    }
    
    /**
     * @return array
     */
    public function Version(): array
    {
        return $this -> _version;
    }
    
    /**
     * @return bool
     */
    public function Name(): string
    {
        return $this -> _name;
    }
    
    /**
     * @return bool
     */
    public function IsValid(): bool
    {
        return $this -> _isValid;
    }
    
    /**
     * @param \UT_Php\IO\Directory $app
     */
    function __construct(\UT_Php\IO\Directory $app) 
    {
        $this -> _name = $app -> Name();
        $mapView = \UT_Php\IO\File::FromDirectory($app, 'MapView.Game.bin');
        $create = $mapView -> Exists() ? false : true;

        if($create) {
            if(!$app -> Exists()) {
                return; 
            }
            
            $bepInEx = \UT_Php\IO\Directory::FromDirectory($app, 'BepInEx');
            $log = null;

            $checkAltLog = false;
            if($bepInEx === null || !$bepInEx -> Exists()) {
                $checkAltLog = true;
            }
            else 
            {
                $log = \UT_Php\IO\File::FromDirectory($bepInEx, 'LogOutput.log');
                if($log === null) {
                    $checkAltLog = true;
                }
            }
            
            if($checkAltLog) {
                $data = \UT_Php\IO\Directory::FromDirectory($app, '7DaysToDie_Data');
                if($data === null || !$data -> Exists()) {
                    $data = \UT_Php\IO\Directory::FromDirectory($app, '7DaysToDieServer_Data');
                }

                $list = $data -> List('/^output\_log\_dedi/i');
                
                if(count($list) == 0) {
                    $localLow = \UT_Php\IO\Directory::FromString('C:\\Users\\Peter\\AppData\\LocalLow\\The Fun Pimps\\7 Days To Die');
                    $list = $localLow -> List('/^Player.log$/i');
                }
                
                $log = count($list) === 0 ? null : $list[count($list) - 1];
            }
            
            if($log === null) {
                return;
            }
            $this -> _isValid = true;

            $this -> GetVersion($log, $checkAltLog);
            $this -> GetPrefabs($app);
            
            $this -> SaveMapView($mapView);
        }
        else
        {
            $this -> _isValid = true;
            $this -> LoadMapView($mapView);
        }
    }
    
    /**
     * @param  \UT_Php\IO\File $file
     * @return void
     */
    private function LoadMapView(\UT_Php\IO\File $file): void
    {
        $data = (array)json_decode(gzuncompress(file_get_contents($file -> Path())));
        
        $this -> _version = (array)$data['Version'];
        $this -> _prefabs = (array)$data['Prefabs'];
    }
    
    /**
     * @param  \UT_Php\IO\File $file
     * @return void
     */
    private function SaveMapView(\UT_Php\IO\File $file): void
    {
        $data = json_encode(
            [
            'Stamp' => date('U'),
            'Version' => $this -> _version,
            'Prefabs' => $this -> _prefabs
            ]
        );
        
        file_put_contents($file -> Path(), gzcompress($data, 9));
    }
    
    /**
     * @param  \UT_Php\IO\Directory $app
     * @return void
     */
    private function GetPrefabs(\UT_Php\IO\Directory $app): void
    {
        $default = \UT_Php\IO\Directory::FromDirectory(\UT_Php\IO\Directory::FromDirectory($app, 'Data'), 'Prefabs');
        $mods = \UT_Php\IO\Directory::FromDirectory($app, 'Mods');

        $folders = [ $default ];
        foreach($mods -> List() as $iDiskManager)
        {
            if(!($iDiskManager instanceof \UT_Php\IO\Directory)) {
                continue;
            }
            
            $modPrefabs = \UT_Php\IO\Directory::FromDirectory($iDiskManager, 'Prefabs');
            
            if($modPrefabs -> Exists()) {
                $folders[] = $modPrefabs;
            }
        }
        
        $buffer = [];
        foreach($folders as $folder)
        {
            $list = $this -> GetPrefabs_Listing($folder);
            $buffer = array_merge($buffer, $list);
        }
        ksort($buffer);
        $this -> _prefabs = $buffer;
    }
    
    /**
     * @param  \UT_Php\IO\Directory $dir
     * @return string[]
     */
    private function GetPrefabs_Listing(\UT_Php\IO\Directory $dir): array
    {
        $buffer = [];
        foreach($dir -> List() as $iDiskManager)
        {
            if($iDiskManager instanceof \UT_Php\IO\Directory) {
                $list = $this -> GetPrefabs_Listing($iDiskManager);
                $buffer = array_merge($buffer, $list);
            }
            else if($iDiskManager instanceof \UT_Php\IO\File && $iDiskManager -> Extension() === 'tts') {
                $prefab = \UT_Php\IO\File::FromDirectory($iDiskManager -> Parent(), $iDiskManager -> Basename().'.xml');
                $prefabXml = \UT_Php\IO\Xml\Document::CreateFromXml(file_get_contents($prefab -> Path()));
                
                $hasSize = false;
                foreach($prefabXml -> Search('/^property$/i') as $element)
                {
                    $attributes = $element -> Attributes();
                    if(isset($attributes['name']) && $attributes['name'] === 'PrefabSize') {
                        list($x, $y, $z) = explode(', ', $attributes['value']);
                        $buffer[$iDiskManager -> Basename()] = [
                            'X' => $x, 
                            'Y' => $y, 
                            'Z' => $z
                        ];
                        $hasSize = true;
                        break;
                    }
                }
                
                if(!$hasSize) {
                    $buffer[$iDiskManager -> Basename()] = null;
                }
            }
        }
        ksort($buffer);
        
        return $buffer;
    }
    
    /**
     * @param  \UT_Php\IO\File $log
     * @return void
     */
    private function GetVersion(\UT_Php\IO\File $log, bool $isAlternativeLog): void
    {
        $stream = file_get_contents($log -> Path());
        
        $regex = '/^\[Info( )*\:( )*Console\] [0-9]{4}\-[0-9]{2}\-[0-9]{2}T[0-9]{2}\:[0-9]{2}\:[0-9]{2} [0-9\.]{5} INF Version.*$/msiU';
        if($isAlternativeLog) {
            $regex = '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}T[0-9]{2}\:[0-9]{2}\:[0-9]{2} [0-9\.]{5} INF Version.*$/msiU';
        }
        
        $matches = [];
        preg_match_all($regex, $stream, $matches);
        
        $rawVersion = trim($matches[0][0]);
        $split = preg_split('/Version\:/', $rawVersion);
        
        $shortRawVersion = trim(substr(trim($split[1]), 0, -13));
        $parts = explode(' ', $shortRawVersion);
        
        $release = $parts[0];
        $beta = substr($parts[2], 2, -1);
        list($major, $minor) = explode('.', $parts[1]);
        
        $this -> _version = [
            'Release' => $release,
            'Major' => $major,
            'Minor' => $minor,
            'Beta' => $beta
        ];
    }
}
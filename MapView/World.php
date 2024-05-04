<?php
namespace MapView;

class World
{
    /**
     * @var string
     */
    private $_name;
    
    /**
     * @var int[]
     */
    private $_size;
    
    /**
     * @var array
     */
    private $_version;
    
    /**
     * @var int
     */
    private $_scale;
    
    /**
     * @var array
     */
    private $_spawnPoints = [];
    
    /**
     * @var array
     */
    private $_prefabs = [];
    
    /** 
     * @var \Data\IO\File
     */
    private $_biomes;
    
    /** 
     * @var \Data\IO\File|null
     */
    private $_render;
    
    /**
     * @return \UT_Php\IO\File
     */
    public function Visuals(): \UT_Php\IO\File
    {
        $f = $this -> _render === null || !$this -> _render -> Exists() ? $this -> _biomes : $this -> _render;
        $images = \UT_Php\IO\Directory::FromString('Images');
        if(!$images -> Exists())
        {
            $images -> Create();
        }
        
        $name = md5($this -> _name).'.'.$f -> Extension();
        $f -> CopyTo($images, $name);
        return \UT_Php\IO\File::FromDirectory($images, $name);
    }
    
    /**
     * @return \UT_Php\IO\File|null
     */
    public function Render(): ?\UT_Php\IO\File
    {
        return $this -> _render;
    }
    
    /**
     * @return string
     */
    public function Name(): string
    {
        return $this -> _name;
    }
    
    /**
     * @return \Data\Drawing\Point2D
     */
    public function Size(): \Data\Drawing\Point2D
    {
        return new \Data\Drawing\Point2D($this -> _size[0], $this -> _size[1]);
    }
    
    /** 
     * @return array
     */
    public function Version(): array
    {
        return $this -> _version;
    }
    
    /** 
     * @return int
     */
    public function Scale(): int
    {
        return $this -> _scale;
    }
    
    /**
     * @return array
     */
    public function SpawnPoints(): array
    {
        return $this -> _spawnPoints;
    }
    
    /**
     * @return array
     */
    public function Prefabs(): array
    {
        return $this -> _prefabs;
    }
    
    /**
     * @return \UT_Php\IO\File
     */
    public function Biomes(): \UT_Php\IO\File
    {
        return $this -> _biomes;
    }
    
    /**
     * @param \UT_Php\IO\Directory $world
     */
    public function __construct(\UT_Php\IO\Directory $world) 
    {
        $this -> _name = $world -> Name();
        
        $biomes = \UT_Php\IO\File::FromDirectory($world, 'biomes.png');
        $render = \UT_Php\IO\File::FromDirectory($world, 'render.png');
        $mapView = \UT_Php\IO\File::FromDirectory($world, 'MapView.World.bin');
        $create = $mapView -> Exists() ? false : true;
        
        if($create)
        {
            $mapInfo = \UT_Php\IO\File::FromDirectory($world, 'map_info.xml');
            $prefabs = \UT_Php\IO\File::FromDirectory($world, 'prefabs.xml');
            $spawnpoints = \UT_Php\IO\File::FromDirectory($world, 'spawnpoints.xml');

            $this -> GetMapInfo($mapInfo);
            $this -> GetSpawnPoints($spawnpoints);
            $this -> GetPrefabs($prefabs);
            
            $this -> SaveMapView($mapView);
        }
        else
        {
            $this -> LoadMapView($mapView);
        }
        $this -> _biomes = $biomes;
        $this -> _render = $render;
    }
    
    /**
     * @param \UT_Php\IO\File $file
     * @return void
     */
    private function LoadMapView(\UT_Php\IO\File $file): void
    {
        $data = (array)json_decode(gzuncompress(file_get_contents($file -> Path())));
        
        $this -> _size = $data['Size'];
        $this -> _version = (array)$data['Version'];
        $this -> _scale = $data['Scale'];
        $this -> _spawnPoints = $data['SpawnPoints'];
        
        $prefabs = [];
        foreach((array)$data['Prefabs'] as $k => $v)
        {
            $buffer = [];
            foreach((array)$v as $i)
            {
                $buffer[] = (array)$i;
            }
            $prefabs[$k] = $buffer;
        }
        $this -> _prefabs = $prefabs;
    }
    
    /**
     * @param \UT_Php\IO\File $file
     * @return void
     */
    private function SaveMapView(\UT_Php\IO\File $file): void
    {
        $data = gzcompress(json_encode([
            'Stamp' => date('U'),
            'Size' => $this -> _size,
            'Version' => $this -> _version,
            'Scale' => $this -> _scale,
            'SpawnPoints' => $this -> _spawnPoints,
            'Prefabs' => $this -> _prefabs
        ]),9);
        
        file_put_contents($file -> Path(), $data);
    }
    
    /**
     * @param \UT_Php\IO\File $prefabs
     * @return void
     */
    private function GetPrefabs(\UT_Php\IO\File $prefabs): void
    {
        $prefabsXml = \UT_Php\IO\Xml\Document::CreateFromXml(file_get_contents($prefabs -> Path()));
        
        $list = [];
        foreach($prefabsXml -> Search('/^decoration$/i') as $element)
        {
            $attributes = $element -> Attributes();
            $name = $attributes['name'];
            
            if(!isset($list[$name]))
            {
                $list[$name] = [];
            }
            
            $rotation = (int)$attributes['rotation'];
            list($x, $y, $z) = explode(',', $attributes['position']);
            
            $list[$name][] = [
                'Location' => [$x, $y, $z],
                'Rotation' => $rotation
            ];
        }
        
        $this -> _prefabs = $list;
    }
    
    /** 
     * @param \UT_Php\IO\File $spawnPoints
     * @return void
     */
    private function GetSpawnPoints(\UT_Php\IO\File $spawnPoints): void
    {
        $spawnPointsXml = \UT_Php\IO\Xml\Document::CreateFromXml(file_get_contents($spawnPoints -> Path()));
        
        $list = [];
        foreach($spawnPointsXml -> Search('/^spawnpoint$/i') as $element)
        {
            $attributes = $element -> Attributes();
            list($lx, $ly, $lz) = explode(',', $attributes['position']);

            $list[] = [$lx, $ly, $lz];
        }
        
        $this -> _spawnPoints = $list;
    }
    
    /**
     * @param \UT_Php\IO\File $mapInfo
     * @return void
     */
    private function GetMapInfo(\UT_Php\IO\File $mapInfo): void
    {
        $mapInfoXml = \UT_Php\IO\Xml\Document::CreateFromXml(file_get_contents($mapInfo -> Path()));

        foreach($mapInfoXml -> Search('/^property$/i') as $element)
        {
            $attributes = $element -> Attributes();
            
            if($attributes['name'] === 'Scale')
            {
                $this -> _scale = $attributes['value'];
                continue;
            }
            if($attributes['name'] === 'HeightMapSize')
            {
                list($x, $y) = explode(',', $attributes['value']);
                $this -> _size = [$x, $y];
                continue;
            }
            if($attributes['name'] === 'GameVersion')
            {
                list($t, $maj, $min, $bet) = explode('.', $attributes['value']);
                $this -> _version = [
                    'Release' => $t,
                    'Major' => $maj,
                    'Minor' => $min,
                    'Beta' => $bet
                ];
                continue;
            }
        }
    }
}
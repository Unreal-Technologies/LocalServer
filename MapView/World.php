<?php

namespace MapView;

class World
{
    /**
     * @var string
     */
    private $name_;

    /**
     * @var int[]
     */
    private $size_;

    /**
     * @var array
     */
    private $version_;

    /**
     * @var int
     */
    private $scale_;

    /**
     * @var array
     */
    private $spawnPoints_ = [];

    /**
     * @var array
     */
    private $prefabs_ = [];

    /**
     * @var \Data\IO\File
     */
    private $biomes_;

    /**
     * @var \Data\IO\File|null
     */
    private $render_;

    /**
     * @return \UT_Php\IO\File
     */
    public function visuals(): \UT_Php\IO\File
    {
        $f = $this -> render_ === null || !$this -> render_ -> exists() ? $this -> biomes_ : $this -> render_;
        $images = \UT_Php\IO\Directory::fromString('Images');
        if (!$images -> exists()) {
            $images -> create();
        }

        $name = md5($this -> name_) . '.' . $f -> extension();
        $f -> copyTo($images, $name);
        return \UT_Php\IO\File::fromDirectory($images, $name);
    }

    /**
     * @return \UT_Php\IO\File|null
     */
    public function render(): ?\UT_Php\IO\File
    {
        return $this -> render_;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this -> name_;
    }

    /**
     * @return \Data\Drawing\Point2D
     */
    public function size(): \Data\Drawing\Point2D
    {
        return new \Data\Drawing\Point2D($this -> size_[0], $this -> size_[1]);
    }

    /**
     * @return array
     */
    public function version(): array
    {
        return $this -> version_;
    }

    /**
     * @return int
     */
    public function scale(): int
    {
        return $this -> scale_;
    }

    /**
     * @return array
     */
    public function spawnPoints(): array
    {
        return $this -> spawnPoints_;
    }

    /**
     * @return array
     */
    public function prefabs(): array
    {
        return $this -> prefabs_;
    }

    /**
     * @return \UT_Php\IO\File
     */
    public function biomes(): \UT_Php\IO\File
    {
        return $this -> biomes_;
    }

    /**
     * @param \UT_Php\IO\Directory $world
     */
    public function __construct(\UT_Php\IO\Directory $world)
    {
        $this -> name_ = $world -> name();

        $biomes = \UT_Php\IO\File::fromDirectory($world, 'biomes.png');
        $render = \UT_Php\IO\File::fromDirectory($world, 'render.png');
        $mapView = \UT_Php\IO\File::fromDirectory($world, 'MapView.World.bin');
        $create = $mapView -> exists() ? false : true;

        if ($create) {
            $mapInfo = \UT_Php\IO\File::fromDirectory($world, 'map_info.xml');
            $prefabs = \UT_Php\IO\File::fromDirectory($world, 'prefabs.xml');
            $spawnpoints = \UT_Php\IO\File::fromDirectory($world, 'spawnpoints.xml');

            $this -> getMapInfo($mapInfo);
            $this -> getSpawnPoints($spawnpoints);
            $this -> getPrefabs($prefabs);

            $this -> saveMapView($mapView);
        } else {
            $this -> loadMapView($mapView);
        }
        $this -> biomes_ = $biomes;
        $this -> render_ = $render;
    }

    /**
     * @param  \UT_Php\IO\File $file
     * @return void
     */
    private function loadMapView(\UT_Php\IO\File $file): void
    {
        $data = (array)json_decode(gzuncompress(file_get_contents($file -> path())));

        $this -> size_ = $data['Size'];
        $this -> version_ = (array)$data['Version'];
        $this -> scale_ = $data['Scale'];
        $this -> spawnPoints_ = $data['SpawnPoints'];

        $prefabs = [];
        foreach ((array)$data['Prefabs'] as $k => $v) {
            $buffer = [];
            foreach ((array)$v as $i) {
                $buffer[] = (array)$i;
            }
            $prefabs[$k] = $buffer;
        }
        $this -> prefabs_ = $prefabs;
    }

    /**
     * @param  \UT_Php\IO\File $file
     * @return void
     */
    private function saveMapView(\UT_Php\IO\File $file): void
    {
        $data = gzcompress(
            json_encode(
                [
                'Stamp' => date('U'),
                'Size' => $this -> size_,
                'Version' => $this -> version_,
                'Scale' => $this -> scale_,
                'SpawnPoints' => $this -> spawnPoints_,
                'Prefabs' => $this -> prefabs_
                ]
            ),
            9
        );

        file_put_contents($file -> path(), $data);
    }

    /**
     * @param  \UT_Php\IO\File $prefabs
     * @return void
     */
    private function getPrefabs(\UT_Php\IO\File $prefabs): void
    {
        $prefabsXml = \UT_Php\IO\Xml\Document::createFromXml(file_get_contents($prefabs -> path()));

        $list = [];
        foreach ($prefabsXml -> search('/^decoration$/i') as $element) {
            $attributes = $element -> attributes();
            $name = $attributes['name'];

            if (!isset($list[$name])) {
                $list[$name] = [];
            }

            $rotation = (int)$attributes['rotation'];
            list($x, $y, $z) = explode(',', $attributes['position']);

            $list[$name][] = [
                'Location' => [$x, $y, $z],
                'Rotation' => $rotation
            ];
        }

        $this -> prefabs_ = $list;
    }

    /**
     * @param  \UT_Php\IO\File $spawnPoints
     * @return void
     */
    private function getSpawnPoints(\UT_Php\IO\File $spawnPoints): void
    {
        $spawnPointsXml = \UT_Php\IO\Xml\Document::createFromXml(file_get_contents($spawnPoints -> path()));

        $list = [];
        foreach ($spawnPointsXml -> search('/^spawnpoint$/i') as $element) {
            $attributes = $element -> attributes();
            list($lx, $ly, $lz) = explode(',', $attributes['position']);

            $list[] = [$lx, $ly, $lz];
        }

        $this -> spawnPoints_ = $list;
    }

    /**
     * @param  \UT_Php\IO\File $mapInfo
     * @return void
     */
    private function getMapInfo(\UT_Php\IO\File $mapInfo): void
    {
        $mapInfoXml = \UT_Php\IO\Xml\Document::createFromXml(file_get_contents($mapInfo -> path()));

        foreach ($mapInfoXml -> search('/^property$/i') as $element) {
            $attributes = $element -> attributes();

            if ($attributes['name'] === 'Scale') {
                $this -> scale_ = $attributes['value'];
                continue;
            }
            if ($attributes['name'] === 'HeightMapSize') {
                list($x, $y) = explode(',', $attributes['value']);
                $this -> size_ = [$x, $y];
                continue;
            }
            if ($attributes['name'] === 'GameVersion') {
                list($t, $maj, $min, $bet) = explode('.', $attributes['value']);
                $this -> version_ = [
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

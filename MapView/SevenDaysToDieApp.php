<?php

namespace MapView;

class SevenDaysToDieApp
{
    /**
     * @var bool
     */
    private $isValid_ = false;

    /**
     * @var string
     */
    private $name_ = null;

    /**
     * @var array
     */
    private $version_ = [];

    /**
     * @var string[]
     */
    private $prefabs_ = [];

    /**
     * @return array
     */
    public function prefabs(): array
    {
        return $this -> prefabs_;
    }

    /**
     * @return array
     */
    public function version(): array
    {
        return $this -> version_;
    }

    /**
     * @return bool
     */
    public function name(): string
    {
        return $this -> name_;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this -> isValid_;
    }

    /**
     * @param \UT_Php_Core\IO\Directory $app
     */
    public function __construct(\UT_Php_Core\IO\Directory $app)
    {
        $this -> name_ = $app -> name();
        $mapView = \UT_Php_Core\IO\File::fromDirectory($app, 'MapView.Game.bin');
        $create = $mapView -> exists() ? false : true;

        if ($create) {
            if (!$app -> exists()) {
                return;
            }

            $bepInEx = \UT_Php_Core\IO\Directory::fromDirectory($app, 'BepInEx');
            $log = null;

            $checkAltLog = false;
            if ($bepInEx === null || !$bepInEx -> exists()) {
                $checkAltLog = true;
            } else {
                $log = \UT_Php_Core\IO\File::fromDirectory($bepInEx, 'LogOutput.log');
                if ($log === null) {
                    $checkAltLog = true;
                }
            }

            if ($checkAltLog) {
                $data = \UT_Php_Core\IO\Directory::fromDirectory($app, '7DaysToDie_Data');
                if ($data === null || !$data -> exists()) {
                    $data = \UT_Php_Core\IO\Directory::fromDirectory($app, '7DaysToDieServer_Data');
                }

                $list = $data -> list('/^output\_log\_dedi/i');

                if (count($list) == 0) {
                    $localLow = \UT_Php_Core\IO\Directory::fromString(
                        'C:\\Users\\Peter\\AppData\\LocalLow\\The Fun Pimps\\7 Days To Die'
                    );
                    $list = $localLow -> list('/^Player.log$/i');
                }

                $log = count($list) === 0 ? null : $list[count($list) - 1];
            }

            if ($log === null) {
                return;
            }
            $this -> isValid_ = true;

            $this -> getVersion($log, $checkAltLog);
            $this -> getPrefabs($app);

            $this -> saveMapView($mapView);
        } else {
            $this -> isValid_ = true;
            $this -> loadMapView($mapView);
        }
    }

    /**
     * @param  \UT_Php_Core\IO\File $file
     * @return void
     */
    private function loadMapView(\UT_Php_Core\IO\File $file): void
    {
        $data = (array)json_decode(gzuncompress(file_get_contents($file -> path())));

        $this -> version_ = (array)$data['Version'];
        $this -> prefabs_ = (array)$data['Prefabs'];
    }

    /**
     * @param  \UT_Php_Core\IO\File $file
     * @return void
     */
    private function saveMapView(\UT_Php_Core\IO\File $file): void
    {
        $data = json_encode(
            [
            'Stamp' => date('U'),
            'Version' => $this -> version_,
            'Prefabs' => $this -> prefabs_
            ]
        );

        file_put_contents($file -> path(), gzcompress($data, 9));
    }

    /**
     * @param  \UT_Php_Core\IO\Directory $app
     * @return void
     */
    private function getPrefabs(\UT_Php_Core\IO\Directory $app): void
    {
        $default = \UT_Php_Core\IO\Directory::fromDirectory(
            \UT_Php_Core\IO\Directory::fromDirectory($app, 'Data'),
            'Prefabs'
        );
        $mods = \UT_Php_Core\IO\Directory::fromDirectory($app, 'Mods');

        $folders = [ $default ];
        foreach ($mods -> list() as $iDiskManager) {
            if (!($iDiskManager instanceof \UT_Php_Core\IO\Directory)) {
                continue;
            }

            $modPrefabs = \UT_Php_Core\IO\Directory::fromDirectory($iDiskManager, 'Prefabs');

            if ($modPrefabs -> exists()) {
                $folders[] = $modPrefabs;
            }
        }

        $buffer = [];
        foreach ($folders as $folder) {
            $list = $this -> getPrefabsListing($folder);
            $buffer = array_merge($buffer, $list);
        }
        ksort($buffer);
        $this -> prefabs_ = $buffer;
    }

    /**
     * @param  \UT_Php_Core\IO\Directory $dir
     * @return string[]
     */
    private function getPrefabsListing(\UT_Php_Core\IO\Directory $dir): array
    {
        $buffer = [];
        foreach ($dir -> list() as $iDiskManager) {
            if ($iDiskManager instanceof \UT_Php_Core\IO\Directory) {
                $list = $this -> getPrefabsListing($iDiskManager);
                $buffer = array_merge($buffer, $list);
            } elseif ($iDiskManager instanceof \UT_Php_Core\IO\File && $iDiskManager -> extension() === 'tts') {
                $prefab = \UT_Php_Core\IO\File::fromDirectory(
                    $iDiskManager -> parent(),
                    $iDiskManager -> basename() . '.xml'
                );
                $prefabXml = \UT_Php_Core\IO\Xml\Document::createFromXml(file_get_contents($prefab -> path()));

                $hasSize = false;
                foreach ($prefabXml -> search('/^property$/i') as $element) {
                    $attributes = $element -> attributes();
                    if (isset($attributes['name']) && $attributes['name'] === 'PrefabSize') {
                        list($x, $y, $z) = explode(', ', $attributes['value']);
                        $buffer[$iDiskManager -> basename()] = [
                            'X' => $x,
                            'Y' => $y,
                            'Z' => $z
                        ];
                        $hasSize = true;
                        break;
                    }
                }

                if (!$hasSize) {
                    $buffer[$iDiskManager -> basename()] = null;
                }
            }
        }
        ksort($buffer);

        return $buffer;
    }

    /**
     * @param  \UT_Php_Core\IO\File $log
     * @return void
     */
    private function getVersion(\UT_Php_Core\IO\File $log, bool $isAlternativeLog): void
    {
        $stream = file_get_contents($log -> path());

        $regex = '/^\[Info( )*\:( )*Console\] [0-9]{4}\-[0-9]{2}\-[0-9]{2}' .
            'T[0-9]{2}\:[0-9]{2}\:[0-9]{2} [0-9\.]{5} INF Version.*$/msiU';
        if ($isAlternativeLog) {
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

        $this -> version_ = [
            'Release' => $release,
            'Major' => $major,
            'Minor' => $minor,
            'Beta' => $beta
        ];
    }
}

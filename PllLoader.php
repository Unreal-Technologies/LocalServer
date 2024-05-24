<?php
class PllLoader
{
    public static function initialize(string $library)
    {
        if(!preg_match('/^.+\:([0-9][\.]?){4}$/i', $library))
        {
            throw new \Exception('Invalid library: "'.$library.'"');
        }
        list($name, $version) = explode(':', $library);
        
        $dir = __DIR__.'/'.$name;
        $file = $dir.'.pll';

        if(file_exists($file))
        {
            throw new \Exception('NYI "'.$file.'"');
        }
        else if(file_exists($dir))
        {
            PllLoader::directoryMode($dir);
        }
        else
        {
            throw new \Exception('Could not locate "'.$library.'"');
        }
    }
    
    /**
     * @param string $dir
     * @return void
     */
    private static function directoryMode(string $dir): void
    {
        spl_autoload_register(function(string $className) use($dir)
        {
            $parts = explode('\\', $className);
            $target = $dir.'/Sources/'.implode('/', array_slice($parts, 1, count($parts))).'.php';
            
            require_once($target);
        });
    }
}
<?php
namespace Data;

class Cv
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string[]
     */
    private $address;
    
    /**
     * @var string
     */
    private $phone;
    
    /**
     * @var string
     */
    private $email;
    
    /**
     * @var \DateTime
     */
    private $birthday;
    
    /**
     * @var string
     */
    private $city;
    
    /**
     * @var array
     */
    private $driverslicence;
    
    /**
     * @var string
     */
    private $nationality;
    
    /**
     * @var string
     */
    private $state;
    
    /**
     * @var string
     */
    private $website;
    
    /**
     * @var array
     */
    private $skills;
    
    /**
     * @var array
     */
    private $languages;
    
    /**
     * @var string
     */
    private $description;
    
    /**
     * @var type
     */
    private $experiences;


    /**
     * @param \UT_Php\IO\Common\Xml $file
     * @param string $root
     */
    public function __construct(\UT_Php\IO\Common\Xml $file, string $root) 
    {
        $dtd = new \UT_Php\IO\Common\Dtd(__DIR__.'\\Cv.dtd');
        $xml = $file -> document();
        if(!$xml -> validateDtd($dtd, $root))
        {
            echo '"'.$file -> path().'" is an invalid XML Format';
            exit;
        }
        
        $this -> name = $xml -> search('/^name$/i')[0] -> attributes()['value'];
        $this -> address = $this -> getAddress($xml -> search('/^address/i')[0]);
        $this -> phone = $xml -> search('/^phone$/i')[0] -> attributes()['value'];
        $this -> email = $xml -> search('/^email$/i')[0] -> attributes()['value'];
        $this -> birthday = new \DateTime($xml -> search('/^birthday$/i')[0] -> attributes()['value']);
        $this -> city = $xml -> search('/^city$/i')[0] -> attributes()['value'];
        $this -> gender = $xml -> search('/^gender$/i')[0] -> attributes()['value'];
        $this -> driverslicence = $this -> getDriverslicence($xml -> search('/^driverslicence$/i')[0]);
        $this -> nationality = $xml -> search('/^nationality$/i')[0] -> attributes()['value'];
        $this -> state = $xml -> search('/^state$/i')[0] -> attributes()['value'];
        $this -> website = $xml -> search('/^website$/i')[0] -> attributes()['value'];
        $this -> skills = $this -> getSkill($xml -> search('/^skills$/i')[0]);
        $this -> languages = $this -> getLanguages($xml -> search('/^languages$/i')[0]);
        $this -> description = $this -> formatText($xml -> search('/^description/i')[0] -> Text());
        $this -> experiences = $this -> getExperiences($xml -> search('/^experiences$/i')[0]);
    }
    
    /**
     * @param string $text
     * @return string
     */
    private function formatText(string $text): string
    {
        $lines = explode("\n", $text);
        $buffer = [];
        foreach($lines as $line)
        {
            $buffer[] = trim($line);
        }
        
        return trim(implode("\r\n", $buffer));
    }
    
    /**
     * @param \UT_Php\IO\Xml\Element $experiences
     * @return array
     */
    private function getExperiences(\UT_Php\IO\Xml\Element $experiences): array
    {
        return 
            (new \UT_Php\Collections\Linq($experiences -> children()))
            -> select(function(\UT_Php\IO\Xml\Element $x) {
                $attributes = $x -> attributes();
                return [
                    'Start' => new \DateTime($attributes['start'].'-01'),
                    'End' => new \DateTime($attributes['end'].'-01'),
                    'CurrentlyAtWork' => (int)$attributes['currentlyAtWork'] === 1,
                    'Title' => $attributes['title'],
                    'Company' => $attributes['company'],
                    'Location' => $attributes['location'],
                    'Text' => $this -> formatText($x -> text())
                ];
            })
            -> orderBy(function(array $x) { return $x['Start'] -> format('Y-m-d'); }, \UT_Php\Enums\SortDirections::Asc)
            -> toArray();
    }
    
    /**
     * @param \UT_Php\IO\Xml\Element $languages
     * @return array
     */
    private function getLanguages(\UT_Php\IO\Xml\Element $languages): array
    {
        return 
            (new \UT_Php\Collections\Linq($languages -> children())) 
            -> select(function(\UT_Php\IO\Xml\Element $x){ 
                $attributes = $x -> attributes();
                return [
                    'Name' => $attributes['name'],
                    'Value' => $attributes['value']
                ];
            })
            -> orderBy(function(array $x) { return $x['Name']; }, \UT_Php\Enums\SortDirections::Asc) 
            -> toArray();
    }
    
    /**
     * @param \UT_Php\IO\Xml\Element $skills
     * @return array
     */
    private function getSkill(\UT_Php\IO\Xml\Element $skills): array
    {
        return 
            (new \UT_Php\Collections\Linq($skills -> children())) 
            -> select(function(\UT_Php\IO\Xml\Element $x){
                $attributes = $x -> attributes();
                return [
                    'Name' => $attributes['name'],
                    'Value' => $attributes['value']
                ];
            })
            -> orderBy(function(array $x) {
                return $x['Name'];
            }, \UT_Php\Enums\SortDirections::Asc)
            -> toArray();
    }
    
    /**
     * @param \UT_Php\IO\Xml\Element $driverslicences
     * @return array
     */
    private function getDriverslicence(\UT_Php\IO\Xml\Element $driverslicences): array
    {
        return 
            (new \UT_Php\Collections\Linq($driverslicences -> children()))
            -> where(function(\UT_Php\IO\Xml\Element $x) { 
                $attributes = $x -> attributes();
                return isset($attributes['start']) && isset($attributes['end']);
            })
            -> select(function(\UT_Php\IO\Xml\Element $x)
            {
                $attributes = $x -> attributes();
                return [
                    'ID' => strtoupper($x -> name()),
                    'Start' => new \DateTime($attributes['start']),
                    'End' => new \DateTime($attributes['end'])
                ];
            })
            -> orderBy(function(array $x){ return $x['ID']; }, \UT_Php\Enums\SortDirections::Asc)
            -> toArray();
    }
    
    /**
     * @param \UT_Php\IO\Xml\Element $address
     * @return string[]
     */
    private function getAddress(\UT_Php\IO\Xml\Element $address): array
    {
        return 
            (new \UT_Php\Collections\Linq($address -> search('/^addressline/'))) 
            -> select(function(\UT_Php\IO\Xml\Element $x) { return $x -> text(); })
            -> toArray();
    }
}
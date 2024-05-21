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
     * @var \UT_Php\IO\File
     */
    private $photo;

    /**
     * @var array
     */
    private $educations;

    /**
     * @var string
     */
    private $gender;

    /**
     * @param \UT_Php\IO\Common\Xml $file
     * @param string $root
     */
    public function __construct(\UT_Php\IO\Common\Xml $file, string $root)
    {
        $dtd = new \UT_Php\IO\Common\Dtd(__DIR__ . '\\Cv.dtd');
        $xml = $file -> document();

        if (!$xml -> validateDtd($dtd, $root)) {
            throw new \Exception('"' . $file -> path() . '" is an invalid XML Format');
        }

        $this -> photo = \UT_Php\IO\File::fromString(
            'Images/' . $xml -> search('/^photo$/i')[0] -> attributes()['value']
        );
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
        $this -> educations = $this -> getEducations($xml -> search('/^educations$/i')[0]);
    }

    /**
     * @return string
     */
    public function asHtml(): string
    {
        $buffer = [];
        $buffer[] = '<table id="cv">';
        $buffer[] = '<tr>';
        $buffer[] = '<td id="personalia">' . $this -> asHtmlPersonalia() . '</td>';
        $buffer[] = '<td id="other">' . $this -> asHtmlOther() . '</td>';
        $buffer[] = '</tr>';
        $buffer[] = '</table>';

        return implode("\r\n", $buffer);
    }

    /**
     * @return string
     */
    private function asHtmlPersonalia(): string
    {
        $root = \UT_Php\IO\Directory::fromString(__DIR__ . '/../');

        $buffer = [];
        $buffer[] = '<img src="' . $this -> photo -> relativeTo($root) . '" />';
        $buffer[] = '<h2>Personalia</h2>';
        $buffer[] = '<hr />';
        $buffer[] = '<b>Naam</b><br />';
        $buffer[] = $this -> name . '<br />';
        $buffer[] = '<br />';
        $buffer[] = '<b>Adres</b><br />';
        foreach ($this -> address as $line) {
            $buffer[] = $line . '<br />';
        }
        $buffer[] = '<br />';
        $buffer[] = '<b>Telefoonnummer</b><br />';
        $buffer[] = $this -> phone . '<br />';
        $buffer[] = '<br />';
        $buffer[] = '<b>E-mail</b><br />';
        $buffer[] = $this -> email . '<br />';
        $buffer[] = '<br />';
        $buffer[] = '<b>Geboortedatum</b><br />';
        $buffer[] = $this -> birthday -> format('d-m-Y') . '<br />';
        $buffer[] = '<br />';
        $buffer[] = '<b>Geboorteplaats</b><br />';
        $buffer[] = $this -> city . '<br />';
        $buffer[] = '<br />';
        $buffer[] = '<b>Geslacht</b><br />';
        $buffer[] = $this -> gender . '<br />';
        if (count($this -> driverslicence) > 0) {
            $buffer[] = '<br />';
            $buffer[] = '<b>Rijbewijs</b>';
            $buffer[] = '<table>';
            foreach ($this -> driverslicence as $data) {
                $buffer[] = '<tr>';
                $buffer[] = '<td>' . $data['ID'] . '</td>';
                $buffer[] = '<td>' .
                    $data['Start'] -> format('d-m-Y') .
                    ' ~ ' .
                    $data['End'] -> format('d-m-Y') .
                    '</td>';
                $buffer[] = '<tr>';
            }
            $buffer[] = '</table>';
        }
        $buffer[] = '<br />';
        $buffer[] = '<b>Nationaliteit</b><br />';
        $buffer[] = $this -> nationality . '<br />';
        $buffer[] = '<br />';
        $buffer[] = '<b>Burgelijke staat</b><br />';
        $buffer[] = $this -> state . '<br />';
        $buffer[] = '<br />';
        $buffer[] = '<b>Website</b><br />';
        $buffer[] = '<a href="' . $this -> website . '" target="_blank">' . $this -> website . '</a><br />';

        $buffer[] = '<h2>Vaardigheden</h2>';
        $buffer[] = '<hr />';
        $buffer[] = '<table>';
        foreach ($this -> skills as $skill) {
            $buffer[] = '<tr>';
            $buffer[] = '<td>' . $skill['Name'] . '</td>';
            $buffer[] = '<td>' . $skill['Value'] . '</td>';
            $buffer[] = '<tr>';
        }
        $buffer[] = '</table>';

        $buffer[] = '<h2>Talenkennis</h2>';
        $buffer[] = '<hr />';
        $buffer[] = '<table>';
        foreach ($this -> languages as $language) {
            $buffer[] = '<tr>';
            $buffer[] = '<td>' . $language['Name'] . '</td>';
            $buffer[] = '<td>' . $language['Value'] . '</td>';
            $buffer[] = '<tr>';
        }
        $buffer[] = '</table>';

        return implode("\r\n", $buffer);
    }

    /**
     * @return string
     */
    private function asHtmlOther(): string
    {
        $buffer = [];
        $buffer[] = '<h1>' . $this -> name . '</h1>';
        $buffer[] = '<div class="wrapped">' . str_replace("\r\n", '<br />', $this -> description) . '</div>';
        $buffer[] = '<h2>Werkervaring</h2>';
        $buffer[] = '<hr />';
        foreach ($this -> experiences as $experience) {
            $buffer[] = '<div>';
            $buffer[] = '<div class="left header">' . $experience['Title'] . '</div>';
            $buffer[] = '<div class="right">' .
                $experience['Start'] -> format('m-Y') .
                ' ~ ' .
                $experience['End'] -> format('m-Y') .
                '</div>';
            $buffer[] = '<br />';
            $buffer[] = '<div><i>' . $experience['Company'] . ', ' . $experience['Location'] . '</i></div>';
            $buffer[] = '<br />';
            $buffer[] = '<div class="wrapped">' . str_replace("\r\n", '<br />', $experience['Text']) . '</div>';
            $buffer[] = '</div>';
            $buffer[] = '<br />';
        }

        $buffer[] = '<h2>Opleiding</h2>';
        $buffer[] = '<hr />';
        foreach ($this -> educations as $education) {
            $buffer[] = '<div>';
            $buffer[] = '<div class="left header">' . $education['Title'] . '</div>';
            $buffer[] = '<div class="right">' .
                $education['Start'] -> format('m-Y') .
                ' ~ ' .
                $education['End'] -> format('m-Y') .
                '</div>';
            $buffer[] = '<br />';
            $buffer[] = '<div><i>' . $education['School'] . ', ' . $education['Location'] . '</i></div>';
            $buffer[] = '<br />';
            $buffer[] = '<div class="wrapped">' . str_replace("\r\n", '<br />', $education['Text']) . '</div>';
            $buffer[] = '</div>';
            $buffer[] = '<br />';
        }

        return implode("\r\n", $buffer);
    }

    /**
     * @param string $text
     * @return string
     */
    private function formatText(string $text): string
    {
        $lines = explode("\n", $text);
        $buffer = [];
        foreach ($lines as $line) {
            $buffer[] = trim($line);
        }

        return trim(implode("\r\n", $buffer));
    }

    /**
     * @param \UT_Php\IO\Xml\Element $educations
     * @return array
     */
    private function getEducations(\UT_Php\IO\Xml\Element $educations): array
    {
        return
            (new \UT_Php\Collections\Linq($educations -> children()))
            -> select(function (\UT_Php\IO\Xml\Element $x) {
                $attributes = $x -> attributes();
                return [
                    'Start' => new \DateTime($attributes['start'] . '-01'),
                    'End' => new \DateTime($attributes['end'] . '-01'),
                    'Title' => $attributes['title'],
                    'School' => $attributes['school'],
                    'Location' => $attributes['location'],
                    'Text' => $this -> formatText($x -> text())
                ];
            })
            -> orderBy(function (array $x) {
                return $x['Start'] -> format('Y-m-d');
            }, \UT_Php\Enums\SortDirections::Asc)
            -> toArray();
    }

    /**
     * @param \UT_Php\IO\Xml\Element $experiences
     * @return array
     */
    private function getExperiences(\UT_Php\IO\Xml\Element $experiences): array
    {
        return
            (new \UT_Php\Collections\Linq($experiences -> children()))
            -> select(function (\UT_Php\IO\Xml\Element $x) {
                $attributes = $x -> attributes();
                return [
                    'Start' => new \DateTime($attributes['start'] . '-01'),
                    'End' => new \DateTime($attributes['end'] . '-01'),
                    'CurrentlyAtWork' => (int)$attributes['currentlyAtWork'] === 1,
                    'Title' => $attributes['title'],
                    'Company' => $attributes['company'],
                    'Location' => $attributes['location'],
                    'Text' => $this -> formatText($x -> text())
                ];
            })
            -> orderBy(function (array $x) {
                return $x['Start'] -> format('Y-m-d');
            }, \UT_Php\Enums\SortDirections::Asc)
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
            -> select(function (\UT_Php\IO\Xml\Element $x) {
                $attributes = $x -> attributes();
                return [
                    'Name' => $attributes['name'],
                    'Value' => $attributes['value']
                ];
            })
            -> orderBy(function (array $x) {
                return $x['Name'];
            }, \UT_Php\Enums\SortDirections::Asc)
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
            -> select(function (\UT_Php\IO\Xml\Element $x) {
                $attributes = $x -> attributes();
                return [
                    'Name' => $attributes['name'],
                    'Value' => $attributes['value']
                ];
            })
            -> orderBy(function (array $x) {
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
            -> where(function (\UT_Php\IO\Xml\Element $x) {
                $attributes = $x -> attributes();
                return isset($attributes['start']) && isset($attributes['end']);
            })
            -> select(function (\UT_Php\IO\Xml\Element $x) {
                $attributes = $x -> attributes();
                return [
                    'ID' => strtoupper($x -> name()),
                    'Start' => new \DateTime($attributes['start']),
                    'End' => new \DateTime($attributes['end'])
                ];
            })
            -> orderBy(function (array $x) {
                return $x['ID'];
            }, \UT_Php\Enums\SortDirections::Asc)
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
            -> select(function (\UT_Php\IO\Xml\Element $x) {
                return $x -> text();
            })
            -> toArray();
    }
}

<?php
namespace Pages;

require_once 'Tools/Work/UT_Php_Core+Html.php';
require_once 'Tools/Work/UT_Php_Core+Collections.php';
require_once 'Tools/Work/UT_Php_Core+Collections+Generic.php';


class Home extends \UT_Php_Core\Html\PageController
{
    private const SERVER_SEVENDAYSTODIE = '7 Days To Die';
    private const SERVER_MINECRAFT = 'Minecraft';
    private const SERVER_PALWORLD = 'Palworld';

    /**
     * @var array
     */
    private array $instances;

    /**
     * @var array
     */
    private array $processes;

    /**
     * @var \UT_Php\IO\Memory
     */
    private \UT_Php_Core\IO\Memory $ram;
    /**
     * @return void
     */
    public function initialize(): void
    {
        $processes = \UT_Php_Core\IO\Process::list();
        $serversInstances = [
            self::SERVER_SEVENDAYSTODIE => ['7daystodieserver.exe', '7daystodie.exe'],
            self::SERVER_MINECRAFT => ['javaw.exe', 'java.exe'],
            self::SERVER_PALWORLD => ['PalServer-Win64-Test-Cmd.exe']
        ];

        $active = (new \UT_Php_Core\Collections\Linq($processes))
            -> toArray(function ($x) use ($serversInstances) {
                foreach ($serversInstances as $v) {
                    if (in_array(strtolower($x -> name()), $v)) {
                        return true;
                    }
                }
                return false;
            });

        $buffer = [];
        foreach ($active as $process) {
            $instance = null;
            foreach ($serversInstances as $k => $v) {
                if (in_array(strtolower($process -> name()), $v)) {
                    $instance = $k;
                }
            }

            $isValidatedInstance = false;
            $selectedInfo = null;

            foreach ($process -> pidList() as $pid) {
                $info = $process -> pidInfo($pid);
                if (
                    $instance === self::SERVER_MINECRAFT &&
                    strpos($info -> get('CommandLine'), 'minecraft') !== false
                ) {
                    $isValidatedInstance = true;
                    $selectedInfo = $info;
                } elseif ($instance === self::SERVER_SEVENDAYSTODIE || $instance === self::SERVER_PALWORLD) {
                    $isValidatedInstance = true;
                    $selectedInfo = $info;
                }
            }

            if ($isValidatedInstance) {
                $buffer[$instance] = [$process, $selectedInfo];
            }
        }

        $this -> instances = $serversInstances;
        $this -> processes = $buffer;

        $this -> ram = \UT_Php_Core\IO\Memory::fromInt(
            (new \UT_Php_Core\Collections\Linq(\UT_Php_Core\IO\Server::ram()))
            -> select(function (\UT_Php_Core\IO\Memory $x) {
                return $x -> value();
            })
            -> sum(function (int $x) {
                return $x;
            })
            -> firstOrDefault()
        );
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $html = '<h1>Home</h1><hr />';
        $html .= '<table class="tableView">';
        $html .= '<tr>'
            . '<th colspan="2" class="cl ct">Game Servers</th>'
            . '<th colspan="2">Memory ' . $this -> ram -> format(0) . '</th>'
            . '<th colspan="2" class="cr ct" />'
            . '</tr>';
        $html .= '<tr><th>Server</th><th>State</th><th>Usage</th><th>%</th><th>PID</th><th>Uptime</th></tr>';
        foreach (array_keys($this -> instances) as $instance) {
            $isActive = isset($this -> processes[$instance]);
            $pid = $isActive ? $this -> processes[$instance][1] -> get('ProcessId') : 'N/A';
            $memory = $isActive ? $this -> processes[$instance][0] -> pidMemory($pid, true) : 'N/A';
            $creationDate = $isActive ? $this -> processes[$instance][1] -> get('CreationDate') : 'N/A';

            $uptime = $isActive ? $this -> secondsToDisplay($this -> calculateUpTime($creationDate)) : 'N/A';
            $memoryPerc = $isActive ?
                number_format(
                    ($this -> processes[$instance][0] -> pidMemory($pid) / $this -> ram -> value()) * 100,
                    2,
                    ',',
                    '.'
                ) . ' %' :
                'N/A';

            $html .= '<tr><td>' .
                $instance .
                '</td><td>' .
                ($isActive ? '<span class="green">Online</span>' : '<span class="red">Offline</span>') .
                '</td><td' . ($isActive ? '' : ' class="inactive"') . '>' .
                $memory .
                '</td><td' . ($isActive ? '' : ' class="inactive"') . '>' .
                $memoryPerc .
                '</td><td' . ($isActive ? '' : ' class="inactive"') . '>' .
                $pid .
                '</td><td' . ($isActive ? '' : ' class="inactive"') . '>' .
                $uptime .
                '</td></tr>';
        }
        $html .= '</table>';

        return $html;
    }

    /**
     * @param int $seconds
     * @return string
     */
    private function secondsToDisplay(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        $days = floor($hours / 24);
        $hours -= $days * 24;

        return $days .
            'd ' .
            str_pad($hours, 2, '0', 0) .
            ':' .
            str_pad($minutes, 2, '0', 0) .
            ':' .
            str_pad($seconds, 2, '0', 0);
    }

    /**
     * @param string $creationDate
     * @return int
     */
    private function calculateUpTime(string $creationDate): int
    {
        $y = substr($creationDate, 0, 4);
        $M = substr($creationDate, 4, 2);
        $d = substr($creationDate, 6, 2);
        $h = substr($creationDate, 8, 2);
        $m = substr($creationDate, 10, 2);
        $s = substr($creationDate, 12, 9);

        $start = new \DateTime($y . '-' . $M . '-' . $d . ' ' . $h . ':' . $m . ':' . $s);
        $now = new \DateTime();

        return $now -> format('U') - $start -> format('U');
    }

    /**
     * @param string $title
     * @param \UT_Php\Interfaces\IFile[] $css
     * @return void
     */
    public function setup(string &$title, array &$css): void
    {
        $title = 'Home';
        $css[] = \UT_Php_Core\IO\File::fromString(__DIR__ . '/Home.css');
        $css[] = \UT_Php_Core\IO\File::fromString(__DIR__ . '/TableView.css');
    }
}

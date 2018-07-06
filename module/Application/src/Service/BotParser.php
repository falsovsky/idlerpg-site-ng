<?php
namespace Application\Service;

use Carbon\Carbon;

class BotParser
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getScoreboard()
    {
        $players = [];

        $row = 0;
        if (($handle = fopen($this->config['bot_db'], "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
                $row++;
                if ($row == 1) {
                    continue;
                }
                $players[] = [
                    'nick' => $data[0],
                    'level' => (int)$data[3],
                    'class' => $data[4],
                    'ttl' => (int)$data[5],
                    'status' => (bool)$data[8],
                ];
            }
            fclose($handle);
        }

        array_multisort(array_column($players, 'level'), SORT_DESC, $players);

        return $players;
    }

    private function secondsToTime($seconds)
    {
        $dtF = new Carbon('@0');
        $dtT = new Carbon("@$seconds");
        return $dtF->diffForHumans($dtT, true, false, 2);
    }

    private function parseAlignment($alignment)
    {
        $align = "";
        switch ($alignment) {
            case 'n':
                $align = "Neutral";
                break;
            case 'e':
                $align = "Evil";
                break;
            case 'g':
                $align = "Good";
                break;
        }
        return $align;
    }

    public function getDatabase()
    {
        $database = [];

        $row = 0;
        if (($handle = fopen($this->config['bot_db'], "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
                $row++;
                if ($row == 1) {
                    continue;
                }

                $database[] = [
                    'nick' => $data[0], // nick
                    'level' => $data[3], // level
                    'admin' => ($data[2] ? 'Yes' : 'No'), // admin
                    'class' => $data[4], // class
                    'ttl'  => [
                        'display' => $this->secondsToTime((int) $data[5]),
                        'numeric' => (int) $data[5], // ttl
                    ],
                    'nick_host' => $data[7], // nick and host
                    'online' => ($data[8] ? 'Yes' : 'No'), // online
                    'idled' => [
                        'display' => $this->secondsToTime((int) $data[9]), // idled
                        'numeric' => (int) $data[9],
                    ],
                    'x_pos' => (int) $data[10], // x pos
                    'y_pos' => (int) $data[11], // y pos
                    'msg_pen' => [
                        'display' => $this->secondsToTime((int) $data[12]), // msg pen
                        'numeric' => (int) $data[12],
                    ],
                    'nick_pen' => [
                        'display' => $this->secondsToTime((int) $data[13]), // nick pen
                        'numeric' => (int) $data[13],
                    ],
                    'part_pen' => [
                        'display' => $this->secondsToTime((int) $data[14]), // part pen
                        'numeric' => (int) $data[14],
                    ],
                    'kick_pen' => [
                        'display' => $this->secondsToTime((int) $data[15]), // kick pen
                        'numeric' => (int) $data[15],
                    ],
                    'quit_pen' => [
                        'display' => $this->secondsToTime((int) $data[16]), // quit pen
                        'numeric' => (int) $data[16],
                    ],
                    'quest_pen' => [
                        'display' => $this->secondsToTime((int) $data[17]), // quest pen
                        'numeric' => (int) $data[17],
                    ],
                    'logout_pen' => [
                        'display' => $this->secondsToTime((int) $data[18]), // logout pen
                        'numeric' => (int) $data[18],
                    ],
                    'total_pen' => [
                        'display' => $this->secondsToTime((int) $data[12] + (int) $data[13] + (int) $data[14] +
                            (int) $data[15] + (int) $data[16] + (int) $data[17] + (int) $data[18]), // total pen
                        'numeric' => (int) $data[12] + (int) $data[13] + (int) $data[14] + (int) $data[15] +
                            (int) $data[16] + (int) $data[17] + (int) $data[18],
                    ],
                    'created' => [
                        'display' => date('Y-m-d H:i:s', (int) $data[19]), // created
                        'numeric' => (int) $data[19],
                    ],
                    'last_login' => [
                        'display' => date('Y-m-d H:i:s', (int) $data[20]), // last login
                        'numeric' => (int) $data[20],
                    ],
                    'amulet' => [
                        'display' => $data[21], // amulet
                        'numeric' => (int) $data[21],
                    ],
                    'charm' => [
                        'display' => $data[22], // charm
                        'numeric' => (int) $data[22],
                    ],
                    'helm' => [
                        'display' => $data[23], // helm
                        'numeric' => (int) $data[23],
                    ],
                    'boots' => [
                        'display' => $data[24], // boots
                        'numeric' => (int) $data[24],
                    ],
                    'gloves' => [
                        'display' => $data[25], // gloves
                        'numeric' => (int) $data[25],
                    ],
                    'ring' => [
                        'display' => $data[26], // ring
                        'numeric' => (int) $data[26],
                    ],
                    'leggings' => [
                        'display' => $data[27], // leggings
                        'numeric' => (int) $data[27],
                    ],
                    'shield' => [
                        'display' => $data[28], // shield
                        'numeric' => (int) $data[28],
                    ],
                    'tunic' => [
                        'display' => $data[29], // tunic
                        'numeric' => (int) $data[29],
                    ],
                    'weapon' => [
                        'display' => $data[30], // weapon
                        'numeric' => (int) $data[30],
                    ],
                    'sum' => (int) $data[21] + (int) $data[22] + (int) $data[23] + (int) $data[24] +
                        (int) $data[25] + (int) $data[26] + (int) $data[27] + (int) $data[28] + (int) $data[29] +
                        (int) $data[30], // sum
                    'alignment' => $this->parseAlignment($data[31]), // alignment
                ];
            }
            fclose($handle);
        }

        return ['data' => $database];
    }

    public function getPlayerInfo($nick)
    {
        $player_info = 0;
        $database = $this->getDatabase();

        foreach ($database['data'] as $item) {
            if ($item['nick'] === $nick) {
                $player_info = $item;
            }
        }

        return $player_info;
    }
}

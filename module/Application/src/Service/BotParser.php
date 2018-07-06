<?php
namespace Application\Service;

use Carbon\Carbon;

class BotParser
{
    private $config;
    private $items = [
        'amulet',
        'boots',
        'charm',
        'gloves',
        'helm',
        'leggings',
        'ring',
        'shield',
        'tunic',
        'weapon'
    ];
    private $penalties = [
        'kick',
        'logout',
        'msg',
        'nick',
        'part',
        'quest',
        'quit',
        'total',
    ];

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

                $record = [
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

                foreach ($this->items as $item) {
                    $unique = $this->getUniqueItem($record[$item]['display']);
                    if ($unique) {
                        $record[$item]['unique'] = $unique;
                    }
                }

                foreach ($this->penalties as $penalty) {
                    if ($record[$penalty.'_pen']['display'] == '1 second') {
                        $record[$penalty.'_pen']['display'] = "None";
                    }
                }

                $database[] = $record;
            }
            fclose($handle);
        }

        return ['data' => $database];
    }

    private function getUniqueItem($item_value)
    {
        $result = null;

        $re = $re = '/\d+([a-h])/';
        preg_match($re, $item_value, $matches);

        if (isset($matches[1])) {
            switch ($matches[1]) {
                case 'a':
                    $result = "Mattt's Omniscience Grand Crown";
                    break;
                case 'b':
                    $result = "Res0's Protectorate Plate Mail";
                    break;
                case 'c':
                    $result = "Dwyn's Storm Magic Amulet";
                    break;
                case 'd':
                    $result = "Jotun's Fury Colossal Sword";
                    break;
                case 'e':
                    $result = "Drdink's Cane of Blind Rage";
                    break;
                case 'f':
                    $result = "Mrquick's Magical Boots of Swiftness";
                    break;
                case 'g':
                    $result = "Jeff's Cluehammer of Doom";
                    break;
                case 'h':
                    $result = "Juliet's Glorious Ring of Sparkliness";
                    break;
            }
        }

        return $result;
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

<?php

namespace Application\Service;

use Carbon\Carbon;

class BotParser
{
    const ONLINE_COLOR = '#0080e1';
    const OFFLINE_COLOR = '#d30000';
    const ITEM_COLOR = '#ff8000';
    const UNIQUE_ITEM_COLOR = '#e1c000';
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
        'weapon',
    ];
    private $penalties = [
        'kick',
        'logout',
        'msg',
        'nick',
        'part',
        'quest',
        'quit',
    ];
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Converts a DateInterval to a human readable format.
     * Returns 'None' if the difference is zero.
     * @param int $seconds
     * @param int $start
     * @return string
     */
    private function secondsToTime(int $seconds, int $start = 0)
    {
        $result = 'None';

        if ($seconds > 0) {
            $dtF = new Carbon("@$start");
            $dtT = new Carbon("@$seconds");
            $result = $dtF->diffForHumans($dtT, true, false, 2);
        }

        return $result;
    }

    /**
     * Returns the alignment.
     * @param string $alignment
     * @return string
     */
    private function parseAlignment(string $alignment)
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

    /**
     * Returns the name of item if it is unique, or null if it isn't.
     * @param mixed $itemValue
     * @return null|string
     */
    private function parseUniqueItem($itemValue)
    {
        $items = [
            "a" => "Mattt's Omniscience Grand Crown",
            "b" => "Res0's Protectorate Plate Mail",
            "c" => "Dwyn's Storm Magic Amulet",
            "d" => "Jotun's Fury Colossal Sword",
            "e" => "Drdink's Cane of Blind Rage",
            "f" => "Mrquick's Magical Boots of Swiftness",
            "g" => "Jeff's Cluehammer of Doom",
            "h" => "Juliet's Glorious Ring of Sparkliness"
        ];
        $result = null;

        $regex = '/\d+([a-h])/';
        preg_match($regex, $itemValue, $matches);

        if (isset($matches[1]) && array_key_exists($matches[1], $items)) {
            $result = $items[$matches[1]];
        }

        return $result;
    }

    /**
     * Sums the values from the keys of the array $record from $start till $end.
     * @param array $record
     * @param int $start
     * @param int $end
     * @return int
     */
    private function sumFields(array $record, int $start, int $end)
    {
        $total = 0;
        for ($i = $start; $i <= $end; $i++) {
            $total += (int) $record[$i];
        }
        return $total;
    }

    /**
     * @return array
     */
    public function getPenaltiesList()
    {
        return $this->penalties;
    }

    /**
     * @return array
     */
    public function getItemsList()
    {
        return $this->items;
    }

    /**
     * Returns an array with a list of all the Items from the database.
     * @return array
     */
    public function getItems()
    {
        $items = [];
        $row = 0;
        if (($handle = fopen($this->config['bot_item'], "r")) !== false) {
            while (($data = fgetcsv($handle, 1024, "\t")) !== false) {
                $row++;
                if ($row == 1) {
                    continue;
                }

                $record = [
                    'x_pos' => (int) $data[0],
                    'y_pos' => (int) $data[1],
                    'type'  => $data[2],
                    'level' => $data[3],
                    'age'   => (int) $data[4],
                    'color' => (is_numeric($data[3]) ? self::ITEM_COLOR : self::UNIQUE_ITEM_COLOR)
                ];

                $items[] = $record;
            }
            fclose($handle);
        }

        return $items;
    }

    /**
     * Returns an array with the Players, sorted by level.
     * Includes the fields needed for the scoreboard page.
     * @return array
     */
    public function getScoreboard()
    {
        $players = [];
        $row = 0;
        if (($handle = fopen($this->config['bot_db'], "r")) !== false) {
            while (($data = fgetcsv($handle, 1024, "\t")) !== false) {
                $row++;
                if ($row == 1) {
                    continue;
                }
                $players[] = [
                    'nick'    => $data[0],
                    'level'   => (int) $data[3],
                    'class'   => $data[4],
                    'ttl'     => $this->secondsToTime((int) $data[5]),
                    'ttl_num' => (int) $data[5],
                    'status'  => (bool) $data[8],
                ];
            }
            fclose($handle);
        }
        usort($players, function($valueA, $valueB) {
            return $valueB['level'] - $valueA['level'] ?: $valueA['ttl_num'] - $valueB['ttl_num'];
        });

        return $players;
    }

    /**
     * Returns a list with (almost) all the fields from the Players database.
     * If the parameter $nick is used, only returns the data for that Player
     * or 0 if that Player doesn't exist.
     * @param string|null $nick
     * @return array|int|mixed
     */
    public function getDatabase(string $nick = null)
    {
        $database = [];
        $row = 0;
        if (($handle = fopen($this->config['bot_db'], "r")) !== false) {
            while (($data = fgetcsv($handle, 1024, "\t")) !== false) {
                $row++;
                if ($row == 1) {
                    continue;
                }

                if ($nick !== null && strcmp($data[0], $nick) !== 0) {
                    continue;
                }

                $record = [
                    'nick' => $data[0], // nick
                    'level' => (int) $data[3], // level
                    'admin' => ($data[2] ? 'Yes' : 'No'), // admin
                    'class' => $data[4], // class
                    'ttl' => [
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
                        'display' => $this->secondsToTime($this->sumFields($data, 12, 18)),
                        'numeric' => $this->sumFields($data, 12, 18),
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
                        'unique' => $this->parseUniqueItem($data[21])
                    ],
                    'charm' => [
                        'display' => $data[22], // charm
                        'numeric' => (int) $data[22],
                        'unique' => $this->parseUniqueItem($data[22])
                    ],
                    'helm' => [
                        'display' => $data[23], // helm
                        'numeric' => (int) $data[23],
                        'unique' => $this->parseUniqueItem($data[23])
                    ],
                    'boots' => [
                        'display' => $data[24], // boots
                        'numeric' => (int) $data[24],
                        'unique' => $this->parseUniqueItem($data[24])
                    ],
                    'gloves' => [
                        'display' => $data[25], // gloves
                        'numeric' => (int) $data[25],
                        'unique' => $this->parseUniqueItem($data[25])
                    ],
                    'ring' => [
                        'display' => $data[26], // ring
                        'numeric' => (int) $data[26],
                        'unique' => $this->parseUniqueItem($data[26])
                    ],
                    'leggings' => [
                        'display' => $data[27], // leggings
                        'numeric' => (int) $data[27],
                        'unique' => $this->parseUniqueItem($data[27])
                    ],
                    'shield' => [
                        'display' => $data[28], // shield
                        'numeric' => (int) $data[28],
                        'unique' => $this->parseUniqueItem($data[28])
                    ],
                    'tunic' => [
                        'display' => $data[29], // tunic
                        'numeric' => (int) $data[29],
                        'unique' => $this->parseUniqueItem($data[29])
                    ],
                    'weapon' => [
                        'display' => $data[30], // weapon
                        'numeric' => (int) $data[30],
                        'unique' => $this->parseUniqueItem($data[30])
                    ],
                    'sum' => $this->sumFields($data, 21, 30),
                    'alignment' => $this->parseAlignment($data[31]), // alignment
                ];

                if ($nick !== null) {
                    return $record;
                }

                $database[] = $record;
            }
            fclose($handle);
        }

        if ($nick !== null) {
            return 0;
        }

        return $database;
    }

    /**
     * Returns the last $limit events [from the user $nick].
     * If $limit is 0 returns all.
     * @param int $limit
     * @param string|null $nick
     * @return array|mixed
     */
    public function getEvents(int $limit, string $nick = null)
    {
        $modifiers = [
            'items' => [],
            'total' => 0,
        ];

        $tmp = [];
        $handle = fopen($this->config['bot_mod'], "r");
        if ($handle !== false) {
            while (($line = fgets($handle)) !== false) {
                if ($nick !== null && strpos($line, $nick) !== false) {
                    $tmp[] = trim($line);
                }

                if ($nick === null) {
                    $tmp[] = trim($line);
                }
            }
            fclose($handle);
        }

        $tmp = array_reverse($tmp);
        $modifiers['total'] = count($tmp);
        $modifiers['items'] = array_slice($tmp, 0, ($limit > 0) ? $limit : null);

        return $modifiers;
    }

    /**
     * Returns an array with the coordinates and name of all Players and Items.
     * @return array
     */
    public function getCoordinates()
    {
        $coordinates = [];

        $players = $this->getDatabase();
        $items = $this->getItems();

        foreach ($players as $player) {
            $coordinates[] = [
                'x'     => $player['x_pos'],
                'y'     => $player['y_pos'],
                'text'  => $player['nick'],
                'color' => ($player['online'] == 'Yes' ? self::ONLINE_COLOR : self::OFFLINE_COLOR)
            ];
        }

        foreach ($items as $item) {
            $coordinates[] = [
                'x'     => $item['x_pos'],
                'y'     => $item['y_pos'],
                'text'  => $item['type'],
                'color' => $item['color'],
            ];
        }

        return $coordinates;
    }

    /**
     * Returns an array with all the data associated with the current quest
     * @return array
     */
    public function getQuestData()
    {
        $quest = [];
        if (($handle = fopen($this->config['bot_quest'], "r")) !== false) {
            while (($data = fgets($handle, 1024)) !== false) {
                // T - title
                if (!isset($data['title']) && $data[0] == "T") {
                    $quest['title'] = trim(substr($data, 2));
                }
                // Y - type. 1 for time based, 2 for stages
                if (! isset($data['type']) && $data[0] == "Y") {
                    $quest['type'] = (int) substr($data, 2);
                }
                // S - objective
                if ($data[0] == "S") {
                    if ($quest['type'] == 1) {
                        // Time to end
                        $quest['objective'] = $this->secondsToTime((int) substr($data, 2), time());
                        $quest['objective_num'] = (int) substr($data, 2);
                    } elseif ($quest['type'] == 2) {
                        // Stage
                        $quest['objective'] = (int) substr($data, 2);
                    }
                }
                if ($data[0] == "P") {
                    $data_exploded = explode(" ", $data);
                    // P - stages position
                    if ($data_exploded[0] == "P") {
                        $quest['stages'] = [
                            [
                                'x_pos' => (int) $data_exploded[1],
                                'y_pos' => (int) $data_exploded[2],
                                'color' => self::OFFLINE_COLOR,
                            ],
                            [
                                'x_pos' => (int) $data_exploded[3],
                                'y_pos' => (int) $data_exploded[4],
                                'color' => self::OFFLINE_COLOR,
                            ],
                        ];
                    }
                    // P{1-4} - player position
                    if (isset($data_exploded[0][1])) {
                        if ($quest['type'] == 2) {
                            $quest['players'][] = [
                                'nick'  => trim($data_exploded[1]),
                                'x_pos' => (int) $data_exploded[2],
                                'y_pos' => (int) $data_exploded[3],
                                'color' => self::ONLINE_COLOR,
                            ];
                        } elseif ($quest['type'] == 1) {
                            $quest['players'][] = [
                                'nick' => trim($data_exploded[1]),
                            ];
                        }
                    }
                }
            }
            fclose($handle);
        }

        return $quest;
    }

    /**
     * Returns an array with all the Players nicks.
     * @return array
     */
    public function getPlayers()
    {
        $players = [];

        $row = 0;
        if (($handle = fopen($this->config['bot_db'], "r")) !== false) {
            while (($data = fgetcsv($handle, 1024, "\t")) !== false) {
                $row++;
                if ($row == 1) {
                    continue;
                }

                $players[] = $data[0]; // nick
            }
        }

        return $players;
    }

    /**
     * Returns an array with the dimensions of the map image.
     * @return array
     */
    public function getMapDimensions()
    {
        return [
            'height' => $this->config['map_height'],
            'width'  => $this->config['map_width'],
        ];
    }
}

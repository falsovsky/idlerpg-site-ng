<?php

namespace Application\Service;

use Carbon\Carbon;
use Intervention\Image\ImageManager;

class BotParser
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Converts a DateInterval to a human readable format
     * Returns 'None' if the difference is zero
     * @param int $seconds
     * @return string
     */
    private function secondsToTime(int $seconds)
    {
        $result = 'None';

        if ($seconds > 0) {
            $dtF = new Carbon('@0');
            $dtT = new Carbon("@$seconds");
            $result = $dtF->diffForHumans($dtT, true, false, 2);
        }

        return $result;
    }

    /**
     * Returns the alignment
     * @param $alignment
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
     * Returns the name of item if it is unique, or null if it isn't
     * @param $item_value
     * @return null|string
     */
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

    /**
     * Returns an array with a list of all the Items from the database
     * @return array
     */
    private function getItems()
    {
        $items = [];

        $row = 0;
        if (($handle = fopen($this->config['bot_itemdb'], "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
                $row++;
                if ($row == 1) {
                    continue;
                }

                $record = [
                    'x_pos' => (int) $data[0],
                    'y_pos' => (int) $data[1],
                    'type'  => $data[2],
                    'level' => $data[3],
                    'age'   => $data[4]
                ];

                $items[] = $record;
            }
            fclose($handle);
        }

        return $items;
    }

    /**
     * Returns an array with the Players, sorted by level.
     * Includes the fields needed for the scoreboard page
     * @return array
     */
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

    /**
     * Sums the values from the keys of the array $record from $start till $end
     * @param array $record
     * @param int $start
     * @param int $end
     * @return int
     */
    private function sumFields(array $record, int $start, int $end) {
        $total = 0;
        for($i = $start; $i <= $end; $i++) {
            $total += (int) $record[$i];
        }
        return $total;
    }

    /**
     * Returns a list with (almost) all the fields from the Players database
     * If the parameter $nick is used, only returns the data for that Player
     * or 0 if that Player doesn't exist
     * @param null $nick
     * @return array|int
     */
    public function getDatabase($nick = null)
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
                        'unique'  => $this->getUniqueItem($data[21])
                    ],
                    'charm' => [
                        'display' => $data[22], // charm
                        'numeric' => (int) $data[22],
                        'unique'  => $this->getUniqueItem($data[22])
                    ],
                    'helm' => [
                        'display' => $data[23], // helm
                        'numeric' => (int) $data[23],
                        'unique'  => $this->getUniqueItem($data[23])
                    ],
                    'boots' => [
                        'display' => $data[24], // boots
                        'numeric' => (int) $data[24],
                        'unique'  => $this->getUniqueItem($data[24])
                    ],
                    'gloves' => [
                        'display' => $data[25], // gloves
                        'numeric' => (int) $data[25],
                        'unique'  => $this->getUniqueItem($data[25])
                    ],
                    'ring' => [
                        'display' => $data[26], // ring
                        'numeric' => (int) $data[26],
                        'unique'  => $this->getUniqueItem($data[26])
                    ],
                    'leggings' => [
                        'display' => $data[27], // leggings
                        'numeric' => (int) $data[27],
                        'unique'  => $this->getUniqueItem($data[27])
                    ],
                    'shield' => [
                        'display' => $data[28], // shield
                        'numeric' => (int) $data[28],
                        'unique'  => $this->getUniqueItem($data[28])
                    ],
                    'tunic' => [
                        'display' => $data[29], // tunic
                        'numeric' => (int) $data[29],
                        'unique'  => $this->getUniqueItem($data[29])
                    ],
                    'weapon' => [
                        'display' => $data[30], // weapon
                        'numeric' => (int) $data[30],
                        'unique'  => $this->getUniqueItem($data[30])
                    ],
                    'sum' =>  $this->sumFields($data, 21, 30),
                    'alignment' => $this->parseAlignment($data[31]), // alignment
                ];

                if ($nick && $record['nick'] == $nick) {
                    return $record;
                }

                $database[] = $record;
            }
            fclose($handle);
        }

        if($nick) {
            return 0;
        }

        return $database;
    }

    /**
     * Returns the last $limit modifiers from the user $nick
     * If $limit is 0 returns all
     * @param string $nick
     * @param int $limit
     * @return array
     */
    public function getModifiers(string $nick, int $limit = 5)
    {
        $modifiers = [
            'items' => [],
            'total' => 0,
        ];

        $tmp = [];
        $handle = fopen($this->config['bot_mod'], "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, $nick) !== false) {
                    $tmp[] = $line;
                }
            }
            fclose($handle);
        }

        $tmp = array_reverse($tmp);
        $modifiers['total'] = count($tmp);
        if ($limit > 0) {
            $modifiers['items'] = array_slice($tmp, 0, $limit);
        } else {
            $modifiers['items'] = $tmp;
        }

        return $modifiers;
    }

    /**
     * Draws a crosshair on $image, using coordinates $x and $y and color $color
     * Also writes text next to the crosshair if $text isn't null
     * @param $image
     * @param $x
     * @param $y
     * @param $color
     * @param null $text
     * @return mixed
     */
    private function drawCrosshair($image, $x, $y, $color, $text = null)
    {
        $cross_size = 5;

        // Bottom top
        $image->line($x - $cross_size, $y, $x + $cross_size, $y, function ($draw) use ($color) {
            $draw->color($color);
        });

        // Left right
        $image->line($x, $y - $cross_size, $x, $y + $cross_size, function ($draw) use ($color) {
            $draw->color($color);
        });

        if ($text) {
            $text_x = $x + $cross_size + 2;
            $text_y = $y + ($cross_size + 2);

            // Draw a "shadow" 1 pixel ahead
            $image->text($text, $text_x + 1, $text_y + 1, function ($font) {
                $font->file($this->config['map_font']);
                $font->size(13);
                $font->color("#000");
            });

            // Text
            $image->text($text, $text_x, $text_y, function ($font) use ($color) {
                $font->file($this->config['map_font']);
                $font->size(13);
                $font->color($color);
            });
        }

        return $image;
    }

    /**
     * Returns a string with a generated image with the Player position and name
     * @param string $nick
     * @return string
     */
    public function getPlayerMap(string $nick)
    {
        $player_info = $this->getDatabase($nick);

        $manager = new ImageManager(['driver' => 'gd']);
        $image = $manager->make($this->config['map_image']);

        $image = $this->drawCrosshair(
            $image,
            $player_info['x_pos'],
            $player_info['y_pos'],
            ($player_info['online'] == 'Yes' ? '#0080e1' : '#d30000'),
            $nick
        );

        return $image->encode('png');
    }

    /**
     * Returns a string with a generated image with all the Players positions
     * @return string
     */
    public function getWorldMap()
    {
        $database = $this->getDatabase();
        $items = $this->getItems();

        $manager = new ImageManager(['driver' => 'gd']);
        $image = $manager->make($this->config['map_image']);

        foreach ($database as $player) {
            $image = $this->drawCrosshair(
                $image,
                $player['x_pos'],
                $player['y_pos'],
                ($player['online'] == 'Yes' ? '#0080e1' : '#d30000')
            );
        }

        foreach ($items as $item) {
            $image = $this->drawCrosshair(
                $image,
                $item['x_pos'],
                $item['y_pos'],
                (is_numeric($item['level']) ? '#ff8000' : '#ffc000')
            );
        }

        return $image->encode('png');
    }
}

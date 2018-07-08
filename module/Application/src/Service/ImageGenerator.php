<?php

namespace Application\Service;

class ImageGenerator
{
    const ONLINE_COLOR = '#0080e1';
    const OFFLINE_COLOR = '#d30000';
    const ITEM_COLOR = '#ff8000';
    const UNIQUE_ITEM_COLOR = '#ffc000';
    const CROSSHAIR_SIZE = 5;

    private $config;
    private $cache;
    private $parser;
    private $imageManager;

    public function __construct($config, $cache, $parser, $imageManager)
    {
        $this->config = $config;
        $this->cache = $cache;
        $this->parser = $parser;
        $this->imageManager = $imageManager;
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
        // Bottom top
        $image->line($x - self::CROSSHAIR_SIZE, $y, $x + self::CROSSHAIR_SIZE, $y, function ($draw) use ($color) {
            $draw->color($color);
        });

        // Left right
        $image->line($x, $y - self::CROSSHAIR_SIZE, $x, $y + self::CROSSHAIR_SIZE, function ($draw) use ($color) {
            $draw->color($color);
        });

        if ($text) {
            $text_x = $x + self::CROSSHAIR_SIZE + 4;
            $text_y = $y + self::CROSSHAIR_SIZE;

            // Draw a "shadow" 1 pixel ahead
            $image->text($text, $text_x + 1, $text_y + 1, function ($font) {
                $font->file(4);
                $font->color("#000");
            });

            // Text
            $image->text($text, $text_x, $text_y, function ($font) use ($color) {
                $font->file(4);
                $font->color($color);
            });
        }

        return $image;
    }

    public function getMapDimensions()
    {
        $key = __FUNCTION__;

        if ($this->cache->hasItem($key)) {
            $dimensions = $this->cache->getItem($key);
        } else {
            $image = $this->imageManager->make($this->config['map_image']);

            $dimensions = [
                'height' => $image->height(),
                'width' => $image->width(),
            ];

            $this->cache->setItem($key, $dimensions);
        }

        return $dimensions;
    }

    /**
     * Returns a generated image with the Player position and name
     * @param string $nick
     * @return \Intervention\Image\Image|mixed
     */
    public function getPlayerMap(string $nick)
    {
        $key = __FUNCTION__ . $nick;

        if ($this->cache->hasItem($key)) {
            $image = $this->cache->getItem($key);
        } else {
            $player_info = $this->parser->getDatabase($nick);

            $image = $this->imageManager->make($this->config['map_image']);

            if ($player_info != 0) {
                $image = $this->drawCrosshair(
                    $image,
                    $player_info['x_pos'],
                    $player_info['y_pos'],
                    ($player_info['online'] == 'Yes' ? self::ONLINE_COLOR : self::OFFLINE_COLOR),
                    $nick
                );
            }

            $image = $image->encode('png');
            $this->cache->setItem($key, $image);
        }

        return $image;
    }

    /**
     * Returns a generated image with all the Players positions
     * @return \Intervention\Image\Image|mixed
     */
    public function getWorldMap()
    {
        $key = __FUNCTION__;

        if ($this->cache->hasItem($key)) {
            $image = $this->cache->getItem($key);
        } else {
            $database = $this->parser->getDatabase();
            $items = $this->parser->getItems();

            $image = $this->imageManager->make($this->config['map_image']);

            foreach ($database as $player) {
                $image = $this->drawCrosshair(
                    $image,
                    $player['x_pos'],
                    $player['y_pos'],
                    ($player['online'] == 'Yes' ? self::ONLINE_COLOR : self::OFFLINE_COLOR)
                );
            }

            foreach ($items as $item) {
                $image = $this->drawCrosshair(
                    $image,
                    $item['x_pos'],
                    $item['y_pos'],
                    (is_numeric($item['level']) ? self::ITEM_COLOR : self::UNIQUE_ITEM_COLOR)
                );
            }

            $image = $image->encode('png');
            $this->cache->setItem($key, $image);
        }

        return $image;
    }

    /**
     * Returns a generated image with all the current Players on a quest
     * @return \Intervention\Image\Image|mixed
     */
    public function getQuestMap()
    {
        $key = __FUNCTION__;

        if ($this->cache->hasItem($key)) {
            $image = $this->cache->getItem($key);
        } else {
            $quest = $this->parser->getQuestData();

            $image = $this->imageManager->make($this->config['map_image']);

            foreach ($quest['players'] as $player) {
                $image = $this->drawCrosshair(
                    $image,
                    $player['x_pos'],
                    $player['y_pos'],
                    self::ONLINE_COLOR
                );
            }

            if ($quest['type'] == 2) {
                $image = $this->drawCrosshair(
                    $image,
                    $quest['stages'][$quest['objective']]['x_pos'],
                    $quest['stages'][$quest['objective']]['y_pos'],
                    self::OFFLINE_COLOR
                );
            }

            $image = $image->encode('png');

            $this->cache->setItem($key, $image);
        }

        return $image;
    }
}

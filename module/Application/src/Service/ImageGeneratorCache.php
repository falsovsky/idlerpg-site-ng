<?php

namespace Application\Service;

use Zend\Cache\Storage\Adapter\AbstractAdapter;

/**
 * Class ImageGeneratorCache
 * @package Application\Service
 * @method drawCrosshair(\Intervention\Image\Image $image, int $x, int $y, string $color, string $text = null)
 * @method getMapDimensions()
 * @method getPlayerMap(string $nick)
 * @method getWorldMap()
 * @method getQuestMap()
 */
class ImageGeneratorCache
{
    private $imageGenerator;
    private $cache;

    public function __construct(ImageGenerator $imageGenerator, AbstractAdapter $cache)
    {
        $this->imageGenerator = $imageGenerator;
        $this->cache = $cache;
    }

    private function getKey(string $method, array $args)
    {
        $key = str_replace(['\\', ':'], '_', get_class() . '::' . $method);
        $key .= str_replace('.', '_', implode("-", $args));

        return $key;
    }

    public function __call($name, $arguments)
    {
        $key = $this->getKey($name, $arguments);

        if ($this->cache->hasItem($key)) {
            $result = $this->cache->getItem($key);
        } else {
            $result = call_user_func_array([$this->imageGenerator, $name], $arguments);
            $this->cache->setItem($key, $result);
        }

        return $result;
    }
}

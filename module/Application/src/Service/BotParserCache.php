<?php

namespace Application\Service;

use Laminas\Cache\Storage\Adapter\AbstractAdapter;

/**
 * Class BotParserCache
 * @package Application\Service
 * @method getPenaltiesList()
 * @method getItemsList()
 * @method getItems()
 * @method getScoreboard()
 * @method getDatabase(string $nick = null)
 * @method getEvents(int $limit, string $nick = null)
 * @method getCoordinates()
 * @method getQuestData()
 * @method getPlayers()
 * @method getMapDimensions()
 */
class BotParserCache
{
    private $cache;
    private $parser;

    public function __construct(BotParser $parser, AbstractAdapter $cache)
    {
        $this->parser = $parser;
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
            $result = call_user_func_array([$this->parser, $name], $arguments);
            $this->cache->setItem($key, $result);
        }

        return $result;
    }
}

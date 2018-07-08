<?php

namespace Application\Service;

use Zend\Cache\Storage\Adapter\AbstractAdapter;

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

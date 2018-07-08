<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Application\Service\BotParser;

class JsonController extends AbstractActionController
{
    private $parser;
    private $cache;

    public function __construct(BotParser $parser, AbstractAdapter $cache)
    {
        $this->parser = $parser;
        $this->cache = $cache;
    }

    public function databaseAction()
    {
        $key = str_replace(['\\', ':', '.'], '', __METHOD__);

        if ($this->cache->hasItem($key)) {
            $database = $this->cache->getItem($key);
        } else {
            $database = $this->parser->getDatabase();
            $this->cache->setItem($key, $database);
        }

        return new JsonModel(['data' => $database]);
    }
}

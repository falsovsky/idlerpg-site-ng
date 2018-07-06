<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Service\BotParser;

class JsonController extends AbstractActionController
{
    private $config;
    private $parser;

    public function __construct(array $config, BotParser $parser)
    {
        $this->config = $config;
        $this->parser = $parser;
    }

    public function databaseAction()
    {
        $database = $this->parser->getDatabase();

        return new JsonModel($database);
    }
}

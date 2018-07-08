<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Service\BotParser;

class JsonController extends AbstractActionController
{
    private $parser;

    public function __construct(BotParser $parser)
    {
        $this->parser = $parser;
    }

    public function databaseAction()
    {
        $database = $this->parser->getDatabase();

        return new JsonModel(['data' => $database]);
    }
}

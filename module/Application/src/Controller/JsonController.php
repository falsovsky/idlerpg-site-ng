<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Service\BotParserCache;

class JsonController extends AbstractActionController
{
    private $parser;

    public function __construct(BotParserCache $parser)
    {
        $this->parser = $parser;
    }

    public function databaseAction()
    {
        return new JsonModel([
            'data' => $this->parser->getDatabase()
        ]);
    }
}

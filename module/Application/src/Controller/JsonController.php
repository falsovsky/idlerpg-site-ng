<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
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

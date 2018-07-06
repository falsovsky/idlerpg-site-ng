<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Service\BotParser;

class ImageController extends AbstractActionController
{
    private $config;
    private $parser;

    public function __construct(array $config, BotParser $parser)
    {
        $this->config = $config;
        $this->parser = $parser;
    }

    public function playerMapAction()
    {
        $nick = (string) $this->params()->fromRoute('nick', 0);
        // TODO: GENERATE IMAGE WITH AN ERROR
        /*
        if (0 === $nick) {
            return $this->redirect()->toRoute('home');
        }
        */

        $map = $this->parser->getPlayerMap($nick);

        $response = $this->getResponse();

        $response->setContent($map);
        $response
            ->getHeaders()
            ->addHeaderLine('Content-Transfer-Encoding', 'binary')
            ->addHeaderLine('Content-Type', 'image/png')
            ->addHeaderLine('Content-Length', strlen($map));

        return $response;
    }

    public function worldMapAction()
    {
        $map = $this->parser->getWorldMap();

        $response = $this->getResponse();

        $response->setContent($map);
        $response
            ->getHeaders()
            ->addHeaderLine('Content-Transfer-Encoding', 'binary')
            ->addHeaderLine('Content-Type', 'image/png')
            ->addHeaderLine('Content-Length', strlen($map));

        return $response;
    }
}

<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Service\ImageGeneratorCache;

class ImageController extends AbstractActionController
{
    private $imageGenerator;

    public function __construct(ImageGeneratorCache $imageGenerator)
    {
        $this->imageGenerator = $imageGenerator;
    }

    public function playerMapAction()
    {
        $nick = $this->params()->fromRoute('nick', null);

        $image = $this->imageGenerator->getPlayerMap($nick);

        $response = $this->getResponse();
        $response->setContent($image);
        $response
            ->getHeaders()
            ->addHeaderLine('Content-Transfer-Encoding', 'binary')
            ->addHeaderLine('Content-Type', 'image/png')
            ->addHeaderLine('Content-Length', strlen($image));

        return $response;
    }

    public function worldMapAction()
    {
        $image = $this->imageGenerator->getWorldMap();

        $response = $this->getResponse();
        $response->setContent($image);
        $response
            ->getHeaders()
            ->addHeaderLine('Content-Transfer-Encoding', 'binary')
            ->addHeaderLine('Content-Type', 'image/png')
            ->addHeaderLine('Content-Length', strlen($image));

        return $response;
    }

    public function questMapAction()
    {
        $image = $this->imageGenerator->getQuestMap();

        $response = $this->getResponse();
        $response->setContent($image);
        $response
            ->getHeaders()
            ->addHeaderLine('Content-Transfer-Encoding', 'binary')
            ->addHeaderLine('Content-Type', 'image/png')
            ->addHeaderLine('Content-Length', strlen($image));

        return $response;
    }
}

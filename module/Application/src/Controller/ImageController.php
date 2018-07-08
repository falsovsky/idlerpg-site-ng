<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Application\Service\ImageGenerator;

class ImageController extends AbstractActionController
{
    private $imageGenerator;
    private $cache;

    public function __construct(ImageGenerator $imageGenerator, AbstractAdapter $cache)
    {
        $this->imageGenerator = $imageGenerator;
        $this->cache = $cache;
    }

    public function playerMapAction()
    {
        $nick = (string) $this->params()->fromRoute('nick', 0);

        $key = str_replace(['\\', ':', '.'], '', __METHOD__ . $nick);

        if ($this->cache->hasItem($key)) {
            $image = $this->cache->getItem($key);
        } else {
            $image = $this->imageGenerator->getPlayerMap($nick);
            $this->cache->setItem($key, $image);
        }

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
        $key = str_replace(['\\', ':', '.'], '', __METHOD__);

        if ($this->cache->hasItem($key)) {
            $image = $this->cache->getItem($key);
        } else {
            $image = $this->imageGenerator->getWorldMap();
            $this->cache->setItem($key, $image);
        }

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
        $key = str_replace(['\\', ':', '.'], '', __METHOD__);

        if ($this->cache->hasItem($key)) {
            $image = $this->cache->getItem($key);
        } else {
            $image = $this->imageGenerator->getQuestMap();
            $this->cache->setItem($key, $image);
        }

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

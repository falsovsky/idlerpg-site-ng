<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Application\Service\BotParser;
use Application\Service\ImageGenerator;

class IndexController extends AbstractActionController
{
    private $config;
    private $parser;
    private $imageGenerator;
    private $cache;

    public function __construct(
        array $config,
        BotParser $parser,
        ImageGenerator $imageGenerator,
        AbstractAdapter $cache
    ) {
        $this->config = $config;
        $this->parser = $parser;
        $this->imageGenerator = $imageGenerator;
        $this->cache = $cache;
    }

    public function gameInfoAction()
    {
        return new ViewModel([
            'bot_nick'     => $this->config['bot_nick'],
            'bot_channel'  => $this->config['bot_channel'],
            'network_name' => $this->config['network_name'],
            'network_host' => $this->config['network_host'],
        ]);
    }

    public function scoreBoardAction()
    {
        $key = str_replace(['\\', ':'], '', __METHOD__);

        if ($this->cache->hasItem($key)) {
            $scoreboard = $this->cache->getItem($key);
        } else {
            $scoreboard = $this->parser->getScoreboard();
            $this->cache->setItem($key, $scoreboard);
        }

        return new ViewModel([
            'score' => $scoreboard
        ]);
    }

    public function playerInfoAction()
    {
        $nick = (string) $this->params()->fromRoute('nick', 0);
        $fullmod = (string) $this->params()->fromRoute('mod', false);
        if (0 === $nick) {
            return $this->redirect()->toRoute('home');
        }

        $key = str_replace(['\\', ':'], '', __METHOD__ . $nick . $fullmod);

        if ($this->cache->hasItem($key)) {
            $player_info = $this->cache->getItem($key);
        } else {
            $player_info = $this->parser->getDatabase($nick);
            if (0 === $player_info) {
                return $this->redirect()->toRoute('home');
            }
            $player_info['items'] = $this->parser::ITEMS;
            $player_info['penalties'] = $this->parser::PENALTIES;

            if ($fullmod) {
                $player_info['mod'] = $this->parser->getEvents(0, $nick);
                $player_info['mod']['link'] = false;
            } else {
                $player_info['mod'] = $this->parser->getEvents(5, $nick);
                $player_info['mod']['link'] = true;
            }

            $player_info['dimensions'] = $this->imageGenerator->getMapDimensions();

            $this->cache->setItem($key, $player_info);
        }

        return new ViewModel($player_info);
    }

    public function databaseAction()
    {
        $key = str_replace(['\\', ':'], '', __METHOD__);

        if ($this->cache->hasItem($key)) {
            $dimensions = $this->cache->getItem($key);
        } else {
            $dimensions = $this->imageGenerator->getMapDimensions();
            $this->cache->setItem($key, $dimensions);
        }

        return new ViewModel([
            'dimensions' => $dimensions
        ]);
    }

    public function worldMapAction()
    {
        $key = str_replace(['\\', ':'], '', __METHOD__);

        if ($this->cache->hasItem($key)) {
            $data = $this->cache->getItem($key);
        } else {
            $data = [
                'coords' => $this->parser->getCoordinates(),
                'dimensions' => $this->imageGenerator->getMapDimensions(),
            ];
            $this->cache->setItem($key, $data);
        }

        return new ViewModel($data);
    }

    public function questInfoAction()
    {
        $key = str_replace(['\\', ':'], '', __METHOD__);

        if ($this->cache->hasItem($key)) {
            $data = $this->cache->getItem($key);
        } else {
            $data = [
                'quest' => $this->parser->getQuestData(),
                'dimensions' => $this->imageGenerator->getMapDimensions(),
            ];

            $this->cache->setItem($key, $data);
        }

        return new ViewModel($data);
    }

    public function recentEventsAction()
    {
        $key = str_replace(['\\', ':'], '', __METHOD__);

        if ($this->cache->hasItem($key)) {
            $events = $this->cache->getItem($key);
        } else {
            $events = $this->parser->getEvents(15);
            $this->cache->setItem($key, $events);
        }

        return new ViewModel($events);
    }
}

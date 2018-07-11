<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Service\BotParserCache;

class IndexController extends AbstractActionController
{
    private $config;
    private $parser;

    public function __construct(array $config, BotParserCache $parser)
    {
        $this->config = $config;
        $this->parser = $parser;
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
        return new ViewModel([
            'score' => $this->parser->getScoreboard()
        ]);
    }

    public function playerInfoAction()
    {
        $nick = $this->params()->fromRoute('nick', null);
        $all_events = $this->params()->fromRoute('mod', null);
        if (null === $nick) {
            return $this->redirect()->toRoute('home');
        }

        $player_info = $this->parser->getDatabase($nick);
        if (0 === $player_info) {
            return $this->redirect()->toRoute('home');
        }
        $player_info['items'] = $this->parser->getItemsList();
        $player_info['penalties'] = $this->parser->getPenaltiesList();

        if ($all_events) {
            $player_info['mod'] = $this->parser->getEvents(0, $nick);
            $player_info['mod']['link'] = false;
        } else {
            $player_info['mod'] = $this->parser->getEvents(5, $nick);
            $player_info['mod']['link'] = true;
        }

        $player_info['map_image'] = $this->config['map_image'];
        $player_info['dimensions'] = $this->parser->getMapDimensions();

        return new ViewModel($player_info);
    }

    public function databaseAction()
    {
        return new ViewModel();
    }

    public function worldMapAction()
    {
        return new ViewModel([
            'map_image'  => $this->config['map_image'],
            'coords'     => $this->parser->getCoordinates(),
            'dimensions' => $this->parser->getMapDimensions()
        ]);
    }

    public function questInfoAction()
    {
        return new ViewModel([
            'map_image'  => $this->config['map_image'],
            'quest' => $this->parser->getQuestData(),
            'dimensions' => $this->parser->getMapDimensions(),
        ]);
    }

    public function recentEventsAction()
    {
        return new ViewModel($this->parser->getEvents(15));
    }

    public function adminInfoAction()
    {
        return new ViewModel([
            'bot_nick' => $this->config['bot_nick'],
        ]);
    }
}

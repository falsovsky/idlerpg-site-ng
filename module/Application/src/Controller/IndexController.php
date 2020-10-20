<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
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
        $allEvents = $this->params()->fromRoute('mod', null);
        if (null === $nick) {
            return $this->redirect()->toRoute('home');
        }

        $playerInfo = $this->parser->getDatabase($nick);
        if (0 === $playerInfo) {
            return $this->redirect()->toRoute('home');
        }

        $playerInfo['items'] = $this->parser->getItemsList();
        $playerInfo['penalties'] = $this->parser->getPenaltiesList();

        if ($allEvents) {
            $playerInfo['mod'] = $this->parser->getEvents(0, $nick);
            $playerInfo['mod']['link'] = false;
        } else {
            $playerInfo['mod'] = $this->parser->getEvents(5, $nick);
            $playerInfo['mod']['link'] = true;
        }

        $playerInfo['map_image'] = $this->config['map_image'];
        $playerInfo['dimensions'] = $this->parser->getMapDimensions();

        return new ViewModel($playerInfo);
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
        $quest = $this->parser->getQuestData();
        if ($quest['type'] == 2) {
            $goal = [
                'x_pos' => $quest['stages'][$quest['objective'] - 1]['x_pos'],
                'y_pos' => $quest['stages'][$quest['objective'] - 1]['y_pos'],
                'color' => $quest['stages'][$quest['objective'] - 1]['color']
            ];
        }
        return new ViewModel([
            'map_image'  => $this->config['map_image'],
            'quest'      => $quest,
            'dimensions' => $this->parser->getMapDimensions(),
            'goal'       => isset($goal) ? $goal : null
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

<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Service\BotParser;

class IndexController extends AbstractActionController
{
    private $config;
    private $parser;

    public function __construct(array $config, BotParser $parser)
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
        $scoreboard = $this->parser->getScoreboard();

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

        $player_info = $this->parser->getDatabase($nick);
        if (0 === $player_info) {
            return $this->redirect()->toRoute('home');
        }

        if ($fullmod) {
            $player_info['mod'] = $this->parser->getModifiers($nick, 0);
            $player_info['mod']['link'] = false;
        } else {
            $player_info['mod'] = $this->parser->getModifiers($nick);
            $player_info['mod']['link'] = true;
        }

        return new ViewModel($player_info);
    }

    public function databaseAction()
    {
        return new ViewModel();
    }

    public function worldMapAction()
    {
        return new ViewModel(['coords' => $this->parser->getCoordinates()]);
    }
}

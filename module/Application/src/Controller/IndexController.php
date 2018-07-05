<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Service\BotParser;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    private $config;

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

    }

    public function databaseAction()
    {

    }

    public function databaseJsonAction()
    {
        $database = $this->parser->getDatabase();

        return new JsonModel($database);
    }
}

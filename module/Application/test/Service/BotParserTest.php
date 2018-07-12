<?php

namespace ApplicationTest\Service;

use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Application\Service\BotParser;

class BotParserTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

          parent::setUp();
    }

    public function testPositionQuest()
    {
        $testcase = 'questinfo-position.txt';
        $config = [
            'bot_quest' => __DIR__ . '/../testcases/' . $testcase,
        ];

        $parser = new BotParser($config);
        $db = $parser->getQuestData();

        // Title
        $this->assertEquals('lay waste to the Towers of Ankh-Allor, wherein lies the terrible sorceror Croocq', $db['title']);

        // Type
        $this->assertEquals(2, $db['type']);

        // Current stage
        $this->assertEquals(1, $db['objective']);

        // Number of stages
        $this->assertEquals(2, count($db['stages']));

        // Stage 1
        $this->assertEquals([
            'x_pos' => 225,
            'y_pos' => 315,
            'color' => '#d30000',
        ], $db['stages'][0]);

        // Stage 2
        $this->assertEquals([
            'x_pos' => 280,
            'y_pos' => 360,
            'color' => '#d30000',
        ], $db['stages'][1]);

        // Number of players
        $this->assertEquals(4, count($db['players']));

        // Player 1
        $this->assertEquals([
            'nick'  => 'Orange',
            'x_pos' => 195,
            'y_pos' => 315,
            'color' => '#0080e1',
        ], $db['players'][0]);

        // Player 2
        $this->assertEquals([
            'nick'  => 'pi',
            'x_pos' => 225,
            'y_pos' => 270,
            'color' => '#0080e1',
        ], $db['players'][1]);

        // Player 3
        $this->assertEquals([
            'nick'  => 'BernardoRafael',
            'x_pos' => 225,
            'y_pos' => 315,
            'color' => '#0080e1',
        ], $db['players'][2]);

        // Player 4
        $this->assertEquals([
            'nick'  => 'pirilampo',
            'x_pos' => 225,
            'y_pos' => 315,
            'color' => '#0080e1',
        ], $db['players'][3]);
    }

    public function testTimeQuest()
    {
        $testcase = 'questinfo-time.txt';
        $config = [
            'bot_quest' => __DIR__ . '/../testcases/' . $testcase,
        ];

        $parser = new BotParser($config);
        $db = $parser->getQuestData();

        // Title
        $this->assertEquals('locate the herbs and brew the elixir to rid the realm of the Normonic Plague', $db['title']);

        // Type
        $this->assertEquals(1, $db['type']);

        // Current stage
        $this->assertEquals(1531325222, $db['objective_val']);

        // Number of players
        $this->assertEquals(4, count($db['players']));

        $this->assertEquals('RichardStallman', $db['players'][0]['nick']);
        $this->assertEquals('xhip', $db['players'][1]['nick']);
        $this->assertEquals('Infernvs', $db['players'][2]['nick']);
        $this->assertEquals('.hack', $db['players'][3]['nick']);
    }

    public function testScoreboard()
    {
        $testcase = 'irpg-scoreboard.db';
        $config = [
            'bot_db' => __DIR__ . '/../testcases/' . $testcase,
        ];

        $parser = new BotParser($config);
        $scoreboard = $parser->getScoreboard();

        // Test the sorting method
        // flcl and fALSO have the same level, but flcl should be first
        // because he has a lower ttl
        $this->assertEquals('flcl', $scoreboard[0]['nick']);
        $this->assertEquals('fALSO', $scoreboard[1]['nick']);

        // Number of players
        $this->assertEquals(12, count($scoreboard));

        // Full data of the first player
        $this->assertEquals([
            'nick'    => 'flcl',
            'level'   => 43,
            'class'   => 'gordo',
            'ttl'     => '3 days 1 hour',
            'ttl_num' => 264103,
            'status'  => true
        ], $scoreboard[0]);

        // Full data of the last player
        $this->assertEquals([
            'nick'    => 'MalMen',
            'level'   => 26,
            'class'   => 'amen',
            'ttl'     => '1 hour 40 minutes',
            'ttl_num' => 6031,
            'status'  => true
        ], $scoreboard[11]);
    }
}

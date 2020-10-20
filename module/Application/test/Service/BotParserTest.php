<?php

namespace ApplicationTest\Service;

use Laminas\Stdlib\ArrayUtils;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
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
        $this->assertEquals(
            'lay waste to the Towers of Ankh-Allor, wherein lies the terrible sorceror Croocq',
            $db['title']
        );

        // Type
        $this->assertEquals(2, $db['type']);

        // Current stage
        $this->assertEquals(1, $db['objective']);

        // Number of stages
        $this->assertEquals(2, count($db['stages']));

        // Stage 1
        $this->assertSame([
            'x_pos' => 225,
            'y_pos' => 315,
            'color' => '#d30000',
        ], $db['stages'][0]);

        // Stage 2
        $this->assertSame([
            'x_pos' => 280,
            'y_pos' => 360,
            'color' => '#d30000',
        ], $db['stages'][1]);

        // Number of players
        $this->assertEquals(4, count($db['players']));

        // Player 1
        $this->assertSame([
            'nick'  => 'Orange',
            'x_pos' => 195,
            'y_pos' => 315,
            'color' => '#0080e1',
        ], $db['players'][0]);

        // Player 2
        $this->assertSame([
            'nick'  => 'pi',
            'x_pos' => 225,
            'y_pos' => 270,
            'color' => '#0080e1',
        ], $db['players'][1]);

        // Player 3
        $this->assertSame([
            'nick'  => 'BernardoRafael',
            'x_pos' => 225,
            'y_pos' => 315,
            'color' => '#0080e1',
        ], $db['players'][2]);

        // Player 4
        $this->assertSame([
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
        $this->assertEquals(
            'locate the herbs and brew the elixir to rid the realm of the Normonic Plague',
            $db['title']
        );

        // Type
        $this->assertEquals(1, $db['type']);

        // Current stage
        $this->assertEquals(1531325222, $db['objective_num']);

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
        $this->assertSame([
            'nick'    => 'flcl',
            'level'   => 43,
            'class'   => 'gordo',
            'ttl'     => '3 days 1 hour',
            'ttl_num' => 264103,
            'status'  => true
        ], $scoreboard[0]);

        // Full data of the last player
        $this->assertSame([
            'nick'    => 'MalMen',
            'level'   => 26,
            'class'   => 'amen',
            'ttl'     => '1 hour 40 minutes',
            'ttl_num' => 6031,
            'status'  => true
        ], $scoreboard[11]);
    }

    public function testDatabase()
    {
        $testcase = 'irpg-scoreboard.db';
        $config = [
            'bot_db' => __DIR__ . '/../testcases/' . $testcase,
        ];

        $parser = new BotParser($config);

        $db = $parser->getDatabase();

        // Number of players
        $this->assertEquals(12, count($db));

        // Test for invalid player
        $this->assertEquals(0, $parser->getDatabase('ZBR123'));

        $expectedUser = [
            'nick' => 'fALSO',
            'level' => 43,
            'admin' => 'No',
            'class' => 'GLORIOSO',
            'ttl' => [
                'display' => '3 days 12 hours',
                'numeric' => 304376,
            ],
            'nick_host' => 'secret!secret@secret',
            'online' => 'Yes',
            'idled' => [
                'display' => '2 weeks 5 days',
                'numeric' => 1675484,
            ],
            'x_pos' => 259,
            'y_pos' => 390,
            'msg_pen' => [
                'display' => '10 minutes 3 seconds',
                'numeric' => 603,
            ],
            'nick_pen' => [
                'display' => 'None',
                'numeric' => 0,
            ],
            'part_pen' => [
                'display' => 'None',
                'numeric' => 0,
            ],
            'kick_pen' => [
                'display' => 'None',
                'numeric' => 0,
            ],
            'quit_pen' => [
                'display' => 'None',
                'numeric' => 0,
            ],
            'quest_pen' => [
                'display' => 'None',
                'numeric' => 0,
            ],
            'logout_pen' => [
                'display' => 'None',
                'numeric' => 0,
            ],
            'total_pen' => [
                'display' => '10 minutes 3 seconds',
                'numeric' => 603,
            ],
            'created' => [
                'display' => '2018-06-20 22:58:00',
                'numeric' => 1529535480,
            ],
            'last_login' => [
                'display' => '2018-06-28 13:35:35',
                'numeric' => 1530192935,
            ],
            'amulet' => [
                'display' => '43',
                'numeric' => 43,
                'unique' => null,
            ],
            'charm' => [
                'display' => '40',
                'numeric' => 40,
                'unique' => null,
            ],
            'helm' => [
                'display' => '23',
                'numeric' => 23,
                'unique' => null,
            ],
            'boots' => [
                'display' => '27',
                'numeric' => 27,
                'unique' => null,
            ],
            'gloves' => [
                'display' => '30',
                'numeric' => 30,
                'unique' => null,
            ],
            'ring' => [
                'display' => '52h',
                'numeric' => 52,
                'unique' => 'Juliet\'s Glorious Ring of Sparkliness',
            ],
            'leggings' => [
                'display' => '21',
                'numeric' => 21,
                'unique' => null,
            ],
            'shield' => [
                'display' => '43',
                'numeric' => 43,
                'unique' => null,
            ],
            'tunic' => [
                'display' => '55',
                'numeric' => 55,
                'unique' => null,
            ],
            'weapon' => [
                'display' => '46',
                'numeric' => 46,
                'unique' => null,
            ],
            'sum' => 380,
            'alignment' => 'Neutral',
        ];

        $this->assertSame($expectedUser, $parser->getDatabase('fALSO'));
    }

    public function testPlayers()
    {
        $testcase = 'irpg-scoreboard.db';
        $config = [
            'bot_db' => __DIR__ . '/../testcases/' . $testcase,
        ];

        $parser = new BotParser($config);

        $expectedPlayers = [
            'Abram',
            'Bergalho',
            'Sir_MaD',
            'Infernvs',
            'BernardoRafael',
            'pirilampo',
            'fALSO',
            'Orange',
            'flcl',
            'Weasel',
            'pi',
            'MalMen',
        ];

        $players = $parser->getPlayers();

        // Number of players
        $this->assertEquals(12, count($players));

        // Test full output
        $this->assertSame($expectedPlayers, $players);
    }

    public function testItems()
    {
        $testcase = 'mapitems.db';
        $config = [
            'bot_item' => __DIR__ . '/../testcases/' . $testcase,
        ];

        $parser = new BotParser($config);

        $expectedItems = [
            [
                'x_pos' => 407,
                'y_pos' => 380,
                'type' => 'charm',
                'level' => '20',
                'age' => 792,
                'color' => '#ff8000',
            ],
            [
                'x_pos' => 274,
                'y_pos' => 125,
                'type' => 'shield',
                'level' => '35',
                'age' => 3345,
                'color' => '#ff8000',
            ],
            [
                'x_pos' => 293,
                'y_pos' => 396,
                'type' => 'shield',
                'level' => '2',
                'age' => 0,
                'color' => '#ff8000',
            ],
            [
                'x_pos' => 50,
                'y_pos' => 350,
                'type' => 'weapon',
                'level' => '85',
                'age' => 258753,
                'color' => '#ff8000',
            ],
        ];

        // Test full output
        $this->assertSame($expectedItems, $parser->getItems());
    }

    public function testCoordinates()
    {
        $botDb = 'irpg-scoreboard.db';
        $botItem = 'mapitems.db';
        $config = [
            'bot_db' => __DIR__ . '/../testcases/' . $botDb,
            'bot_item' => __DIR__ . '/../testcases/' . $botItem,
        ];

        $parser = new BotParser($config);

        $coordinates = $parser->getCoordinates();

        // Test for total - 12 players plus 4 items
        $this->assertEquals(12 + 4, count($coordinates));

        // Compare first player
        $this->assertSame([
            'x' => 267,
            'y' => 306,
            'text' => 'Abram',
            'color' => '#0080e1',
        ], $coordinates[0]);
    }

    public function testEvents()
    {
        $testcase = 'modifiers.txt';
        $config = [
            'bot_mod' => __DIR__ . '/../testcases/' . $testcase,
        ];

        $parser = new BotParser($config);

        $events = $parser->getEvents(0);

        // Test total events
        $this->assertEquals(100, count($events['items']));

        // Test total
        $this->assertEquals(100, $events['total']);

        $events = $parser->getEvents(0, 'fAGSO');

        // Test all events
        $this->assertEquals(13, count($events['items']));

        // Test total
        $this->assertEquals(13, $events['total']);

        // Test first event
        $this->assertEquals(
            "[06/22/18 09:03:15] fAGSO has fallen ill with the black plague. " .
                     "This terrible calamity has slowed them 0 days, 10:51:59 from level 81.",
            $events['items'][0]
        );

        // Test for invalid player
        $this->assertSame([
            'items' => [],
            'total' => 0,
        ], $parser->getEvents(0, 'ZBR123'));
    }
}

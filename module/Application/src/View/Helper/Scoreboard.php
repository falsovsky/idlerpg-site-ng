<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;


class Scoreboard extends AbstractHelper
{
    private function secondsToTime($seconds) {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
    }

    public function __invoke($item)
    {
        $url = $this->getView()->plugin('url');
        $escaper = $this->getView()->plugin('escapehtml');

        $class = '';
        if ($item['status']) {
            $class = ' class="offline"';
        }
        $str = '<a' . $class. ' href="' . $url('playerinfo', ['nick' => $item['nick']]) . '">' . $item['nick'] . '</a>';
        $str .=', the level ' . $item['level'] . ' ' . $item['class'] . '. ';
        $str .='Next level in ' . $this->secondsToTime($item['ttl']) . '.';

        return $str;
    }
}

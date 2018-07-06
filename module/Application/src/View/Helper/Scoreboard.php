<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Carbon\Carbon;

class Scoreboard extends AbstractHelper
{
    private function secondsToTime($seconds)
    {
        $dtF = new Carbon('@0');
        $dtT = new Carbon("@$seconds");
        return $dtF->diffForHumans($dtT, true, false);
    }

    public function __invoke($item)
    {
        $url = $this->getView()->plugin('url');
        $escaper = $this->getView()->plugin('escapehtml');

        $class = '';
        if (! $item['status']) {
            $class = ' class="offline"';
        }
        $str = '<a' . $class. ' href="' . $url('playerinfo', ['nick' => $item['nick']]) . '">' . $item['nick'] . '</a>';
        $str .= ', the level ' . $item['level'] . ' ' . $item['class'] . '. ';
        $str .= 'Next level in ' . $this->secondsToTime($item['ttl']) . '.';

        return $str;
    }
}

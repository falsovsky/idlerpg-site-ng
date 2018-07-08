<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Scoreboard extends AbstractHelper
{
    public function __invoke($item)
    {
        $url = $this->getView()->plugin('url');

        $class = '';
        if (! $item['status']) {
            $class = ' class="text-danger"';
        } else {
            $class = ' class="text-success"';
        }
        $str = '<a' . $class . ' href="' . $url('player-info', ['nick' => $item['nick']]) . '">';
        $str .= $item['nick'] . '</a>';
        $str .= ', the level ' . $item['level'] . ' ' . $item['class'] . '. ';
        $str .= 'Next level in ' . $item['ttl'] . '.';

        return $str;
    }
}

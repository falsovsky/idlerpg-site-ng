<?php

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Application\Service\BotParser;

class Event extends AbstractHelper
{
    private $nicks;

    public function __construct(array $nicks)
    {
        $this->nicks = $nicks;
    }

    public function __invoke(string $event)
    {
        $url = $this->getView()->plugin('url');

        foreach ($this->nicks as $nick) {
            $event = str_replace(
                $nick,
                '<a href="' . $url('player-info', ['nick' => $nick]) . '">' . $nick . '</a>',
                $event
            );
        }

        return $event;
    }
}

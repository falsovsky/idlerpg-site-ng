<?php

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class PlayerItem extends AbstractHelper
{
    public function __invoke(string $name, int $value, string $unique = null)
    {
        $str = '<b>' . ucfirst($name) . ':</b> ' . $value;
        if ($unique) {
            $str .= ' [<span class="unique">' . $unique . '</span>]';
        }

        return $str;
    }
}

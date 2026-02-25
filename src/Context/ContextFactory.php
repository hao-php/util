<?php

namespace Haoa\Util\Context;

use Haoa\Util\Util;

class ContextFactory
{

    public static function getContext(): BaseContext
    {
        if (Util::isCoroutine()) {
            return new CoContext();
        }
        return new ArrContext();
    }

}
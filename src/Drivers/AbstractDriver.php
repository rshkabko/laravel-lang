<?php

namespace Flamix\Lang\Drivers;

abstract class AbstractDriver
{
    public function isAvailable(?string $lang): bool
    {
        return in_array($lang ?? null, array_keys(config('lang.available')));
    }
}

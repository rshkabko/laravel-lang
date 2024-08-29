<?php

namespace Flamix\Lang\Drivers\Contracts;

interface SetInterface
{
    public function set(string $lang): mixed;
}

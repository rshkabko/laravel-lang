<?php

namespace Flamix\Lang\Drivers;

class Prefix extends AbstractDriver implements Contracts\DetectInterface
{
    public function detect(): ?string
    {
        $lang = request()->segment(1);
        return $this->isAvailable($lang) ? $lang : null;
    }
}
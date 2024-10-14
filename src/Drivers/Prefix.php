<?php

namespace Flamix\Lang\Drivers;

class Prefix extends AbstractDriver implements Contracts\DetectInterface
{
    public function detect(): ?string
    {
        $url = explode('/', $_SERVER['REQUEST_URI'] ?? '');
        $lang = $url['1'] ?? null;
        return $this->isAvailable($lang) ? $lang : null;
    }
}

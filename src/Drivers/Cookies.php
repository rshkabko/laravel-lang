<?php

namespace Flamix\Lang\Drivers;

class Cookies extends AbstractDriver implements Contracts\DetectInterface, Contracts\SetInterface
{
    public function detect(): ?string
    {
        $lang = request()->cookie('lang');
        return $this->isAvailable($lang) ? $lang : null;
    }

    public function set(string $lang): mixed
    {
        return cookie()->forever('lang', $lang);
    }
}

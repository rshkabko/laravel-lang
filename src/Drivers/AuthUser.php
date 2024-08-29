<?php

namespace Flamix\Lang\Drivers;

class AuthUser extends AbstractDriver implements Contracts\DetectInterface, Contracts\SetInterface
{
    public function detect(): ?string
    {
        $lang = auth()->user()->lang ?? null;
        return $this->isAvailable($lang) ? $lang : null;
    }

    public function set(string $lang): mixed
    {
        if (auth()->check()) {
            return auth()->user()->update(['lang' => $lang]);
        }

        return false;
    }
}

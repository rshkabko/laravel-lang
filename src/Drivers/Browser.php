<?php

namespace Flamix\Lang\Drivers;

class Browser extends AbstractDriver implements Contracts\DetectInterface
{
    public function detect(): ?string
    {
        $request = request()->header('Accept-Language', '');
        $lang = substr($request, 0, 2);

        return $this->isAvailable($lang) ? $lang : null;
    }
}

<?php

namespace Flamix\Lang\Drivers;

class Browser extends AbstractDriver implements Contracts\DetectInterface
{
    public function detect(): ?string
    {
        $lang = strtolower(substr((string) request()->header('Accept-Language'), 0, 2));
        // ISO code → available key (browsers send "uk" for Ukrainian, we use "ua")
        $lang = config('lang.aliases.' . $lang, $lang);

        return $this->isAvailable($lang) ? $lang : null;
    }
}
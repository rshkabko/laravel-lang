<?php

return [
    'autoload' => env('LANG_AUTOLOAD', true),

    'available' => [
        'en' => 'English',
        'ru' => 'Русский',
        'ua' => 'Українська',
    ],

    // Accept-Language ISO code → available key (browsers send "uk" for Ukrainian)
    'aliases' => [
        'uk' => 'ua',
    ],

    // Redirect unmatched bare URLs (/about) to the locale prefix (/en/about)
    'prefix_fallback' => env('LANG_PREFIX_FALLBACK', true),

    'drivers' => [
        'get' => [
            \Flamix\Lang\Drivers\Cookies::class,
            \Flamix\Lang\Drivers\AuthUser::class,
            \Flamix\Lang\Drivers\Browser::class,
        ],
        'set' => [
            \Flamix\Lang\Drivers\Cookies::class,
            \Flamix\Lang\Drivers\AuthUser::class,
        ],
    ],
];

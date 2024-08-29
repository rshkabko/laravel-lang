<?php

return [
    'autoload' => env('LANG_AUTOLOAD', true),

    'available' => [
        'en' => 'English',
        'ru' => 'Русский',
        'ua' => 'Українська',
    ],

    'drivers' => [
        'get' => [
//            \Flamix\Lang\Drivers\Cookies::class,
            \Flamix\Lang\Drivers\AuthUser::class,
            \Flamix\Lang\Drivers\Browser::class,
        ],
        'set' => [
            \Flamix\Lang\Drivers\Cookies::class,
            \Flamix\Lang\Drivers\AuthUser::class,
        ],
    ],
];

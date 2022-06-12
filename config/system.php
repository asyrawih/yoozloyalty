<?php

return [

    "trial_days" => env("TRIAL_DAYS"),

    "grace_period_days" => env("GRACE_PERIOD_DAYS"),

    // Available languages (multi-language is not supported yet)
    "available_languages" => [
        "en" => "English"
    ],

    // Localization defaults
    "default_language" => env("DEFAULT_LANGUAGE", "en"),

    "default_locale" => env("DEFAULT_LOCALE", "en_US"),

    "default_timezone" => env("DEFAULT_TIMEZONE", "UTC"),

    "default_currency" => env("DEFAULT_CURRENCY", "TTD"),

    "default_points_expiry" => env("DEFAULT_POINTS_EXPIRY", 365),
];

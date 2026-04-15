<?php

return [
    'name'     => env('APP_NAME', 'Park In'),
    'env'      => env('APP_ENV', 'production'),
    'debug'    => (bool) env('APP_DEBUG', false),
    'url'      => env('APP_URL', 'http://localhost'),
    'timezone' => 'Asia/Jakarta',
    'locale'   => 'id',
    'fallback_locale' => 'en',
    'faker_locale'    => 'id_ID',
    'cipher'   => 'AES-256-CBC',
    'key'      => env('APP_KEY'),
    'previous_keys' => [],
    'maintenance' => ['driver' => 'file'],
    'providers' => \Illuminate\Support\ServiceProvider::defaultProviders()->merge([])->toArray(),
    'aliases'   => \Illuminate\Support\Facades\Facade::defaultAliases()->merge([])->toArray(),
];

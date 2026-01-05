<?php
use PHPOpenSourceSaver\JWTAuth\Providers\JWT\Lcobucci;
use PHPOpenSourceSaver\JWTAuth\Providers\Auth\Illuminate as AuthIlluminate;
use PHPOpenSourceSaver\JWTAuth\Providers\Storage\Illuminate as StorageIlluminate;
return [
    'secret' => env('JWT_SECRET', 'dev-secret-key'),
    'keys' => [
        'public' => env('JWT_PUBLIC_KEY'),
        'private' => env('JWT_PRIVATE_KEY'),
        'passphrase' => env('JWT_PASSPHRASE'),
    ],
    'ttl' => (int) env('JWT_TTL', 60),
    'refresh_iat' => env('JWT_REFRESH_IAT', false),
    'refresh_ttl' => (int) env('JWT_REFRESH_TTL', 20160),
    'algo' => env('JWT_ALGO', 'HS256'),
    'required_claims' => [
        'iss', 'iat', 'exp', 'nbf', 'sub', 'jti',
    ],
    'persistent_claims' => [],
    'lock_subject' => true,
    'leeway' => (int) env('JWT_LEEWAY', 0),
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', false),
    'blacklist_grace_period' => (int) env('JWT_BLACKLIST_GRACE_PERIOD', 0),
    'decrypt_cookies' => env('JWT_DECRYPT_COOKIES', false),
    'providers' => [
        'jwt' => Lcobucci::class,
        'auth' => AuthIlluminate::class,
        'storage' => StorageIlluminate::class,
    ],
];

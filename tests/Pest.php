<?php
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
uses(TestCase::class, RefreshDatabase::class)->in('Feature');
beforeEach(function () {
    $this->seed();
});

if (!function_exists('authHeadersFor')) {
    function authHeadersFor(\App\Models\User $user): array
    {
        $token = \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::fromUser($user);
        return ['Authorization' => 'Bearer ' . $token];
    }
}

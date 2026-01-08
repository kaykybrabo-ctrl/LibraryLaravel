<?php

namespace App\Services;

use App\Exceptions\ClientException;
use App\Jobs\SendCartEngagementEmail;
use App\Mail\ResetPasswordMail;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

/**
 * Domain service responsible for authentication-related operations.
 */
class AuthService
{
    /**
     * Register a new non-admin user.
     *
     * @param array<string,mixed> $data
     */
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => false,
        ]);
    }

    /**
     * Attempt to authenticate a user and return the authenticated user and JWT token.
     *
     * @param array<string,mixed> $credentials
     * @return array{user: User, token: string}
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new ClientException(__('errors.invalid_credentials'), 'invalid_credentials');
        }

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout the current user and perform side-effects such as cart engagement email.
     *
     * @return array{message: string}
     */
    public function logout(): array
    {
        $token = null;
        try {
            $token = JWTAuth::getToken();
        } catch (\Throwable $e) {
            Log::debug('JWTAuth::getToken failed on logout', [
                'error' => $e->getMessage(),
            ]);
        }

        if (!$token) {
            try {
                $authHeader = (string) request()->header('Authorization', '');
                if (preg_match('/^Bearer\s+(.*)$/i', trim($authHeader), $m)) {
                    $token = $m[1];
                }
            } catch (\Throwable $e) {
                Log::debug('Failed to parse Authorization header on logout', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        try {
            $user = null;
            try {
                if ($token) {
                    $user = JWTAuth::setToken($token)->authenticate();
                }
            } catch (\Throwable $e) {
                Log::debug('JWTAuth::authenticate failed on logout', [
                    'error' => $e->getMessage(),
                ]);
            }

            if (!$token) {
                Log::info('Logout called without JWT token; cart engagement email dispatch skipped');
            }

            $isAdmin = (bool) ($user->is_admin ?? false);
            if ($user && !$isAdmin) {
                $hasCart = CartItem::where('user_id', (int) $user->id)
                    ->where('quantity', '>', 0)
                    ->exists();

                if ($hasCart) {
                    try {
                        SendCartEngagementEmail::dispatch((int) $user->id);
                        Log::info('Dispatched cart engagement email on logout', [
                            'user_id' => (int) $user->id,
                            'email' => (string) $user->email,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('Failed to dispatch cart engagement email on logout', [
                            'user_id' => (int) $user->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Unexpected error during logout side-effects', [
                'error' => $e->getMessage(),
            ]);
        }

        try {
            if ($token) {
                JWTAuth::invalidate($token);
            }
        } catch (\Throwable $e) {
            Log::debug('JWTAuth::invalidate failed on logout', [
                'error' => $e->getMessage(),
            ]);
        }

        return ['message' => __('messages.logged_out')];
    }

    /**
     * Request a password reset for the given email.
     *
     * @return array{message: string}
     */
    public function requestPasswordReset(string $email): array
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return ['message' => __('messages.password_reset_requested')];
        }

        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $baseUrl = config('app.url', 'http://localhost:8080');
        $url = rtrim($baseUrl, '/') . '/reset-password?token=' . urlencode($token) . '&email=' . urlencode($user->email);

        try {
            $resendKey = (string) (
                getenv('RESEND_API_KEY')
                ?: ($_SERVER['RESEND_API_KEY'] ?? '')
                ?: ($_ENV['RESEND_API_KEY'] ?? '')
            );
            Log::info('Password reset email delivery method', [
                'resend_key_present' => !empty($resendKey),
                'resend_key_len' => strlen($resendKey),
                'sapi' => php_sapi_name(),
                'pid' => getmypid(),
            ]);

            if (!empty($resendKey)) {
                $resendFrom = (string) (
                    getenv('RESEND_FROM')
                    ?: ($_SERVER['RESEND_FROM'] ?? '')
                    ?: ($_ENV['RESEND_FROM'] ?? '')
                );
                if (empty($resendFrom)) {
                    $resendFrom = 'PedBook <onboarding@resend.dev>';
                }

                $payload = json_encode([
                    'from' => $resendFrom,
                    'to' => [$user->email],
                    'subject' => __('messages.password_reset_subject'),
                    'html' => __('messages.password_reset_email_html', ['url' => e($url)]),
                ]);

                $tmpFile = tempnam(sys_get_temp_dir(), 'resend_');
                file_put_contents($tmpFile, $payload);

                $cmd = 'curl -sS -o - -w "\nHTTP_STATUS:%{http_code}\n"'
                    . ' -X POST https://api.resend.com/emails'
                    . ' -H ' . escapeshellarg('Authorization: Bearer ' . $resendKey)
                    . ' -H ' . escapeshellarg('Content-Type: application/json')
                    . ' --data-binary @' . escapeshellarg($tmpFile)
                    . ' --max-time 20 2>&1';

                $output = [];
                $exitCode = 0;
                exec($cmd, $output, $exitCode);
                @unlink($tmpFile);

                $raw = implode("\n", $output);
                $status = 0;
                $body = $raw;
                if (preg_match('/\nHTTP_STATUS:(\d+)\n?$/', $raw, $m)) {
                    $status = (int) $m[1];
                    $body = preg_replace('/\nHTTP_STATUS:\d+\n?$/', '', $raw);
                }

                if ($exitCode !== 0 || $status < 200 || $status >= 300) {
                    Log::error('Resend API failed to send password reset email', [
                        'email' => $user->email,
                        'from' => $resendFrom,
                        'exit_code' => $exitCode,
                        'http_status' => $status,
                        'response' => $body,
                    ]);
                } else {
                    Log::info('Resend API sent password reset email', [
                        'email' => $user->email,
                        'from' => $resendFrom,
                        'to' => $user->email,
                        'http_status' => $status,
                        'response' => $body,
                    ]);
                }
            } elseif ((string) (getenv('MAILTRAP_SEND_API_ENABLED') ?: ($_SERVER['MAILTRAP_SEND_API_ENABLED'] ?? '') ?: ($_ENV['MAILTRAP_SEND_API_ENABLED'] ?? '')) === 'true'
                && !empty((string) (getenv('MAILTRAP_API_TOKEN') ?: ($_SERVER['MAILTRAP_API_TOKEN'] ?? '') ?: ($_ENV['MAILTRAP_API_TOKEN'] ?? '')))
            ) {
                $mailtrapToken = (string) (
                    getenv('MAILTRAP_API_TOKEN')
                    ?: ($_SERVER['MAILTRAP_API_TOKEN'] ?? '')
                    ?: ($_ENV['MAILTRAP_API_TOKEN'] ?? '')
                );
                $mailtrapFromEmail = (string) (
                    getenv('MAILTRAP_FROM_EMAIL')
                    ?: ($_SERVER['MAILTRAP_FROM_EMAIL'] ?? '')
                    ?: ($_ENV['MAILTRAP_FROM_EMAIL'] ?? '')
                );
                if (empty($mailtrapFromEmail)) {
                    $mailtrapFromEmail = 'hello@demomailtrap.co';
                }
                $mailtrapFromName = (string) (
                    getenv('MAILTRAP_FROM_NAME')
                    ?: ($_SERVER['MAILTRAP_FROM_NAME'] ?? '')
                    ?: ($_ENV['MAILTRAP_FROM_NAME'] ?? '')
                );
                if (empty($mailtrapFromName)) {
                    $mailtrapFromName = 'PedBook';
                }

                $payload = json_encode([
                    'from' => [
                        'email' => $mailtrapFromEmail,
                        'name' => $mailtrapFromName,
                    ],
                    'to' => [
                        [
                            'email' => $user->email,
                        ],
                    ],
                    'subject' => __('messages.password_reset_subject'),
                    'html' => __('messages.password_reset_email_html', ['url' => e($url)]),
                ]);

                $tmpFile = tempnam(sys_get_temp_dir(), 'mailtrap_');
                file_put_contents($tmpFile, $payload);

                $cmd = 'curl -sS -o - -w "\nHTTP_STATUS:%{http_code}\n"'
                    . ' -X POST https://send.api.mailtrap.io/api/send'
                    . ' -H ' . escapeshellarg('Authorization: Bearer ' . $mailtrapToken)
                    . ' -H ' . escapeshellarg('Content-Type: application/json')
                    . ' --data-binary @' . escapeshellarg($tmpFile)
                    . ' --max-time 20 2>&1';

                $output = [];
                $exitCode = 0;
                exec($cmd, $output, $exitCode);
                @unlink($tmpFile);

                $raw = implode("\n", $output);
                $status = 0;
                $body = $raw;
                if (preg_match('/\nHTTP_STATUS:(\d+)\n?$/', $raw, $m)) {
                    $status = (int) $m[1];
                    $body = preg_replace('/\nHTTP_STATUS:\d+\n?$/', '', $raw);
                }

                if ($exitCode !== 0 || $status < 200 || $status >= 300) {
                    Log::error('Mailtrap Send API failed to send password reset email', [
                        'email' => $user->email,
                        'exit_code' => $exitCode,
                        'http_status' => $status,
                        'response' => $body,
                    ]);
                } else {
                    Log::info('Mailtrap Send API sent password reset email', [
                        'email' => $user->email,
                        'http_status' => $status,
                        'response' => $body,
                    ]);
                }
            } else {
                Mail::to($user->email)->send(new ResetPasswordMail($user, $url));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send password reset email for ' . $user->email . ': ' . $e->getMessage());
        }

        return ['message' => __('messages.password_reset_requested')];
    }

    /**
     * Reset user password using a validated payload.
     *
     * @param array<string,mixed> $data
     * @return array{message: string}
     */
    public function resetPassword(array $data): array
    {
        $record = DB::table('password_reset_tokens')->where('email', $data['email'])->first();
        if (!$record) {
            throw new ClientException(__('errors.invalid_token'), 'invalid_token');
        }

        if (!Hash::check($data['token'], $record->token)) {
            throw new ClientException(__('errors.invalid_token'), 'invalid_token');
        }

        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            throw new ClientException(__('errors.user_not_found'), 'user_not_found');
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        return ['message' => __('messages.password_reset_success')];
    }
}

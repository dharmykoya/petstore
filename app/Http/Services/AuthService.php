<?php

namespace App\Http\Services;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthService {
    private JwtService $jwtService;
    public function __construct(JwtService $jwtService) {
        $this->jwtService = $jwtService;
    }

    /**
     * Create a new user.
     *
     * @param array<string, mixed> $requestData
     * @param bool $isAdmin
     * @return User
     */
    public function createUser(array $requestData, bool $isAdmin = false): User {
        if ($isAdmin) $requestData['is_admin'] = true;
        return User::create($requestData);
    }

    /**
     * Login user.
     *
     * @param array<string, mixed> $requestData
     * @return array<string, mixed> array
     */
    public function loginUser(array $requestData): array {
        $user = User::query()->where('email', $requestData['email'])->first();

        if (!$user || !Hash::check($requestData['password'], $user->password)) {
            return ['status' => false, 'message' => 'Email and/or Password does not match.'];
        }

        $token = $this->jwtService->createToken($user->toArray());
        $user['token'] = $token;
        return ['status' => true, 'data' => $user];
    }

    public function logout(Request $request): void {
        $token = $request->bearerToken();
        DB::table('token_blacklists')->insert(['token' => $token]);
    }

    /**
     * Send password reset link.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sendPasswordResetLink(array $data): array {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return [
                'message' => 'Reset link sent to your email.'
            ];
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->insert([
            'email' => $data['email'],
            'token' => bcrypt($token),
            'created_at' => now()
        ]);

        $resetLink = url('/base_url/reset-password?token=' . $token . '&email=' . urlencode($data['email']));

        Mail::send('emails.userResetPassword', ['link' => $resetLink], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Your Password Reset Link');
        });

        return [
            'message' => 'Reset link sent to your email.'
        ];
    }

    /**
     * reset password.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function resetPassword(array $data): array {
        $reset = DB::table('password_reset_tokens')
            ->where('email', $data['email'])
            ->first();

        if (!$reset) {
            return ['status' => false, 'message' => 'Invalid token.'];
        }

        // Check if the token matches and is not expired (assuming a 60 minute expiration)
        if (!Hash::check($data['token'], $reset->token) || Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $data['email'])->delete();
            return ['status' => false, 'message' => 'Invalid token or expired token.'];
        }

        // Reset the password
        $user = User::where('email', $data['email'])->first();

        if (!$user){
            return ['status' => false, 'message' => 'Invalid token.'];
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        // Delete the reset token
        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        return ['status' => true, 'message' => 'Password has been reset successfully.'];
    }
}

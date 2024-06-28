<?php

namespace App\Http\Services;


use App\Models\User;
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

    public function createUser($requestData, $isAdmin = false) {
        if ($isAdmin) $requestData['is_admin'] = true;
        return User::create($requestData);
    }

    public function loginUser($requestData) {
        $user = User::query()->where('email', $requestData['email'])->first();

        if (!$user || !Hash::check($requestData['password'], $user->password)) {
            return ['status' => false, 'message' => 'Email and/or Password does not match.'];
        }

        $token = $this->jwtService->createToken($user->toArray());
        $user['token'] = $token;
        return ['status' => true, 'data' => $user];
    }

    public function logout($request) {
        $token = $request->bearerToken();
        DB::table('token_blacklists')->insert(['token' => $token]);
    }

    public function sendPasswordResetLink($data)
    {
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

    public function resetPassword($data) {
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

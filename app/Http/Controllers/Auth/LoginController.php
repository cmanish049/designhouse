<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function attemptLogin(Request $request)
    {
        // attempt to issue a token to user based login credentials
        $token = $this->guard()->attempt($this->credentials($request));
        if (! $token) return false;

        // get the authenticated user
        $user = $this->guard()->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return false;
        }

        // set user token
        $this->guard()->setToken($token);

        return true;
    }

    protected function sendLoginResponse(Request $request)
    {
        // clear login attempts
        $this->clearLoginAttempts($request);

        // get the token from authentication guard
        $token = (string) $this->guard()->getToken();

        // extract expiry date
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = $this->guard()->user();

        if($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return response()->json(['errors' => [
                'verification' => 'You need to verify your email address',
            ]]);
        }

        throw ValidationException::withMessages([
            $this->username() => "Authentication failed",
        ]);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        return response()->json(['message' => 'Logged out successfully']);
    }
}

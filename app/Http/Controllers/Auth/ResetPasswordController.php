<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{

    use ResetsPasswords;

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return response()->json(['message' => trans($response)], 200);
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $response
     * @throws ValidationException
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response()->json(['message' => trans($response)], 422);
    }
}

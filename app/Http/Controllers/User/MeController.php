<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __invoke()
    {
        if(auth()->check()) {
            return new UserResource(auth()->user());
        }
        return response()->json(null, 401);
    }
}

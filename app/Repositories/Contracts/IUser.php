<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface IUser extends IBase
{
    public function findByEmail($email);
    public function search(Request $request);
}

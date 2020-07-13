<?php

namespace App\Repositories\Contracts;

interface IUser extends IBase
{
    public function findByEmail($email);
}

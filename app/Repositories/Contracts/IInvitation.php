<?php

namespace App\Repositories\Contracts;

interface IInvitation extends IBase
{
    public function addUsertoTeam($team, $userId);
    public function removeUserFromTeam($team , $userId);
}

<?php

namespace App\Repositories\Contracts;

interface ITeam extends IBase
{
    public function fetchUserTeams();
}

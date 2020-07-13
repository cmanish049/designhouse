<?php

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Contracts\IUser;

class InvitationRepository extends BaseRepository implements IInvitation
{
    public function model()
    {
        return Invitation::class;
    }

    public function addUsertoTeam($team, $userId)
    {
        $team->members()->attach($userId);
    }

    public function removeUserFromTeam($team , $userId)
    {
        $team->members()->detach($userId);
    }
}

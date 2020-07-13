<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    protected $teams;
    protected $users;
    protected $invitations;

    public function __construct(
        ITeam $teams,
        IUser $users,
        IInvitation $invitations
    ) {
        $this->teams = $teams;
        $this->users = $users;
        $this->invitations = $invitations;
    }

    public function index()
    {
        $teams = $this->teams->all();
        return TeamResource::collection($teams);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name'],
        ]);

        $team = $this->teams->create([
            'owner_id' => auth()->id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return new TeamResource($team);

    }


    public function update(Request $request, $id)
    {
        $team = $this->teams->find($id);

        $this->authorize('update', $team);

        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name,'.$id],
        ]);

        $team = $this->teams->update($id, [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return new TeamResource($team);

    }

    public function destroy($id)
    {
        $team = $this->teams->find($id);

        $this->authorize('update', $team);

        $team->delete();

        return response()->json(['message' => 'Team Deleted Successfully']);
    }


    public function findById($id)
    {
        $team = $this->teams->find($id);
        return new TeamResource($team);
    }

    public function fetchUserTeams()
    {
        $teams = $this->teams->fetchUserTeams();
        return TeamResource::collection($teams);
    }

    public function findBySlug($slug)
    {

    }

    public function removeFromTeam($team_id, $user_id)
    {
        $team = $this->teams->find($team_id);
        $user = $this->users->find($user_id);

        // check user is not the owner
        if (!$user->isOwnerOfTeam($team)) {
            return response()->json([
                'message' => 'You are the team owner'
            ], 401);
        }

        if (! auth()->user()->isOwnerOfTeam($team) && auth()->id() !== $user->id) {
            return response()->json([
                'message' => 'You are the team owner'
            ], 401);
        }

        $this->invitations->removeUserFromTeam($team, $user_id);

        return response()->json([
            'message' => 'User Removed'
        ], 200);
    }
}

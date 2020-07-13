<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Mail\SendInvitationToJoinTeam;
use App\Models\Team;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    protected $invitations;
    protected $teams;
    protected $users;
    public function __construct(
        IInvitation $invitations,
        ITeam $teams,
        IUser $users
    ) {
        $this->invitations = $invitations;
        $this->teams = $teams;
        $this->users = $users;
    }

    public function invite(Request $request, $teamId)
    {
        $team = $this->teams->find($teamId);

        $this->validate($request, [
            'email' => ['required', 'email'],
        ]);

        $user = auth()->user();

        if (! $user->isOwnerOfTeam($team))
            return response()->json(['email' => 'You are not a team owner'], 401);

        // check if email has pending invitation
        if ($team->hasPendingInvite($request->email))
            return response()->json(['email' => 'Email already has pending invite'], 422);

        // get recipient by email
        $recipient = $this->users->findByEmail($request->email);

        // if the recipient doesnot exist, send invitation to join the team
        if (! $recipient) {
            $this->createInvitation(false, $team, $request->email);
            return response()->json(['message' => 'Invitation sent to user'], 200);
        }

        // check if the team already has member
        if ($team->hasUser($recipient)) {
            return response()->json(['email' => 'This user seems to be a team member already'], 422);
        }

        // send invitation to user
        $this->createInvitation(true, $team, $request->email);
        return response()->json(['message' => 'Invitation sent to user'], 200);
    }

    public function resend($id)
    {
        $invitation = $this->invitations->find($id);

        $this->authorize('resend', $invitation);
        // if (! auth()->user()->isOwnerOfTeam($invitation->team))
        //     return response()->json(['email' => 'You are not a team owner'], 401);

        // get recipient by email
        $recipient = $this->users->findByEmail($invitation->recipient_email);
        Mail::to($invitation->recipient_email)
            ->send(new SendInvitationToJoinTeam($invitation, !is_null($recipient)));
        return response()->json(['message' => 'Invitation resent'], 200);

    }

    public function respond(Request $request, $id)
    {
        $this->validate($request, [
            'token' => ['required'],
            'decision' => ['required'],
        ]);
        $token = $request->token;
        $decision = $request->decision; // 'accept' or 'deny'
        $invitation = $this->invitations->find($id);

        // check if recipient email equals authenticated user email
        // if ($invitation->recipient_email !== auth()->user()->email) {
        //     return response()->json(['message' => 'This is not your invitation'], 401);
        // }

        $this->authorize('respond', $invitation);

        // check to make sure that token patch
        if ($invitation->token != $token) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        if ($decision !== 'deny') {
            $this->invitations->addUsertoTeam($invitation->team, auth()->id());
        }
        $invitation->delete();

        return response()->json(['message' => 'Successful'], 200);
    }

    public function destroy($id)
    {
        $invitation = $this->invitations->find($id);
        $this->authorize('delete', $invitation);

        // if (! auth()->user()->isOwnerOfTeam($invitation->team))
        //     return response()->json(['email' => 'You are not a team owner'], 401);

        $invitation->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }

    protected function createInvitation(bool $userExists, Team $team, string $email)
    {
        $invitation = $this->invitations->create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => md5(uniqid(microtime())),
        ]);
        Mail::to($email)
            ->send(new SendInvitationToJoinTeam($invitation, $userExists));
    }
}

<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvitationToJoinTeam extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $userExists;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invitation $invitation, bool $userExists)
    {
        $this->invitation = $invitation;
        $this->userExists = $userExists;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->userExists) {
            $url = config('app.client_url'). '/settings/teams';
            return $this->markdown('emails.invitations.invite-existing-user')
                ->with(['invitation' => $this->invitation, 'url' => $url])
                ->subject('Invitation to join team '. $this->invitation->team->name);
        } else {
            $url = config('app.client_url'). '/register?invitation='. $this->invitation->recipient_email;
            return $this->markdown('emails.invitations.invite-new-user')
                ->with(['invitation' => $this->invitation, 'url' => $url])
                ->subject('Invitation to join team '. $this->invitation->team->name);
        }
    }
}

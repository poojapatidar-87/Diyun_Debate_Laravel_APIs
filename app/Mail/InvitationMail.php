<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $teamName;
    public $invitationLink;

    /**
     * Create a new message instance.
     */
    public function __construct($teamName, $invitationLink)
    {
        $this->teamName = $teamName;
        $this->invitationLink = $invitationLink;
    }

    public function build()
    {
        return $this->view('emails.invitation')
                    ->subject('Invitation to Join the Team');
    }

}

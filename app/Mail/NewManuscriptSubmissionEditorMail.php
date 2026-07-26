<?php

namespace App\Mail;

use App\Models\User;
use App\Models\WaitingSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewManuscriptSubmissionEditorMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WaitingSubmission $submission,
        public User $editor,
    ) {
        $this->submission->loadMissing('targetJournal');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Manuscript Submission - '.$this->submission->targetJournal?->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.new_manuscript_submission_editor',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

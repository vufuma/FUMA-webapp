<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use Helper;

use App\Models\SubmitJob;

class JobFailedWithErrorCode extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected SubmitJob $job, protected string $msg)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Job Failed With Error Code',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $err_specific_msg = Helper::searchArrayByKeyValue(config('all_status_codes'), 'short_name', $this->job->status)['email_message'];
        return new Content(
            view: 'emails.jobError',
            with: [
                'jobID' => $this->job->jobID,
                'jobtitle' => $this->job->title,
                'status' => $this->job->status,
                'msg' => $this->msg,
                'err_specific_msg' => $err_specific_msg,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

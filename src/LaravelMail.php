<?php

namespace YukataRm\Laravel\Mail;

use Illuminate\Mail\Mailable;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;

/**
 * Laravel Mail
 * 
 * @package YukataRm\Laravel\Mail
 */
class LaravelMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Envelope instance
     * 
     * @var \Illuminate\Mail\Mailables\Envelope
     */
    protected Envelope $envelope;

    /**
     * Content instance
     * 
     * @var \Illuminate\Mail\Mailables\Content
     */
    protected Content $content;

    /**
     * Attachment instance array
     * 
     * @var array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    protected array $attachments;

    /**
     * Headers instance
     * 
     * @var \Illuminate\Mail\Mailables\Headers
     */
    protected Headers $headers;

    /**
     * constructor
     * 
     * @param \Illuminate\Mail\Mailables\Envelope $envelope
     * @param \Illuminate\Mail\Mailables\Content $content
     * @param array<int, \Illuminate\Mail\Mailables\Attachment> $attachments
     * @param \Illuminate\Mail\Mailables\Headers $headers
     */
    function __construct(
        Envelope $envelope,
        Content $content,
        array $attachments,
        Headers $headers
    ) {
        $this->envelope    = $envelope;
        $this->content     = $content;
        $this->attachments = $attachments;
        $this->headers     = $headers;
    }

    /**
     * get Envelope instance
     * 
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return $this->envelope;
    }

    /**
     * get Content instance
     * 
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return $this->content;
    }

    /**
     * get Attachment instance array
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return $this->attachments;
    }

    /**
     * get Headers instance
     * 
     * @return \Illuminate\Mail\Mailables\Headers
     */
    public function headers(): Headers
    {
        return $this->headers;
    }
}

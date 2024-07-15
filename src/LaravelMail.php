<?php

namespace YukataRm\Laravel\Mail;

use Illuminate\Mail\Mailable;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;

/**
 * Laravelのメール送信機能を包括したクラス
 * BaseMailClientで使用する
 * 
 * @package YukataRm\Laravel\Mail
 */
class LaravelMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * コンストラクタ
     * 
     * @param \Illuminate\Mail\Mailables\Envelope $envelope
     * @param \Illuminate\Mail\Mailables\Content $content
     * @param array<int, \Illuminate\Mail\Mailables\Attachment> $attachments
     * @param \Illuminate\Mail\Mailables\Headers $headers
     */
    public function __construct(
        protected Envelope $envelope,
        protected Content $content,
        protected array $attachments,
        protected Headers $headers
    ) {
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return $this->envelope;
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return $this->content;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return $this->attachments;
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        return $this->headers;
    }
}

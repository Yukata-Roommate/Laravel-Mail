<?php

namespace YukataRm\Laravel\Mail\Interface;

use YukataRm\Laravel\Mail\LaravelMail;

use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Headers;

/**
 * MailClient Interface
 * 
 * @package YukataRm\Laravel\Mail\Interface
 */
interface MailClientInterface
{
    /*----------------------------------------*
     * Mailable
     *----------------------------------------*/

    /**
     * send mail
     * 
     * @return void
     */
    public function send(): void;

    /**
     * get rendered mail
     * 
     * @return string
     */
    public function render(): string;

    /*----------------------------------------*
     * Laravel Mail
     *----------------------------------------*/

    /**
     * get LaravelMail instance
     * 
     * @return \YukataRm\Laravel\Mail\LaravelMail
     */
    public function laravelMail(): LaravelMail;

    /**
     * get Envelope instance
     * 
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope;

    /**
     * get Content instance
     * 
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content;

    /**
     * get Attachment instance array
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array;

    /**
     * get Headers instance
     * 
     * @return \Illuminate\Mail\Mailables\Headers
     */
    public function headers(): Headers;

    /*----------------------------------------*
     * Laravel Mail - Envelope
     *----------------------------------------*/

    /**
     * get sender address
     * 
     * @return string|null
     */
    public function senderAddress(): string|null;

    /**
     * set sender address
     * 
     * @param string $senderAddress
     * @return static
     */
    public function setSenderAddress(string $senderAddress): static;

    /**
     * get sender name
     * 
     * @return string|null
     */
    public function senderName(): string|null;

    /**
     * set sender name
     * 
     * @param string $senderName
     * @return static
     */
    public function setSenderName(string $senderName): static;

    /**
     * get recipient address
     * 
     * @return string|null
     */
    public function recipientAddress(): string|null;

    /**
     * set recipient address
     * 
     * @param string $recipientAddress
     * @return static
     */
    public function setRecipientAddress(string $recipientAddress): static;

    /**
     * get recipient name
     * 
     * @return string|null
     */
    public function recipientName(): string|null;

    /**
     * set recipient name
     * 
     * @param string $recipientName
     * @return static
     */
    public function setRecipientName(string $recipientName): static;

    /**
     * get mail subject
     * 
     * @return string|null
     */
    public function subject(): string|null;

    /**
     * set mail subject
     * 
     * @param string $subject
     * @return static
     */
    public function setSubject(string $subject): static;

    /**
     * get CC address array
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Address>
     */
    public function cc(): array;

    /**
     * set CC address array
     * 
     * @param array<int, \Illuminate\Mail\Mailables\Address> $cc
     * @return static
     */
    public function setCc(array $cc): static;

    /**
     * add CC address
     * 
     * @param string $address
     * @param string|null $name
     * @return static
     */
    public function addCc(string $address, string|null $name = null): static;

    /**
     * get BCC address array
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Address>
     */
    public function bcc(): array;

    /**
     * set BCC address array
     * 
     * @param array<int, \Illuminate\Mail\Mailables\Address> $bcc
     * @return static
     */
    public function setBcc(array $bcc): static;

    /**
     * add BCC address
     * 
     * @param string $address
     * @param string|null $name
     * @return static
     */
    public function addBcc(string $address, string|null $name = null): static;

    /**
     * get ReplyTo address array
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Address>
     */
    public function replyTo(): array;

    /**
     * set ReplyTo address array
     * 
     * @param array<int, \Illuminate\Mail\Mailables\Address> $replyTo
     * @return static
     */
    public function setReplyTo(array $replyTo): static;

    /**
     * add ReplyTo address
     * 
     * @param string $address
     * @param string|null $name
     * @return static
     */
    public function addReplyTo(string $address, string|null $name = null): static;

    /**
     * get tag array
     * 
     * @return array<int, string>
     */
    public function tags(): array;

    /**
     * set tag array
     * 
     * @param array<int, string> $tags
     * @return static
     */
    public function setTags(array $tags): static;

    /**
     * add tag
     * 
     * @param string $tag
     * @return static
     */
    public function addTags(string $tag): static;

    /**
     * get metadata array
     * 
     * @return array<string, string|int>
     */
    public function metadata(): array;

    /**
     * set metadata array
     * 
     * @param array<string, string|int> $metadata
     * @return static
     */
    public function setMetadata(array $metadata): static;

    /**
     * add metadata
     * 
     * @param string $key
     * @param string|int $value
     * @return static
     */
    public function addMetadata(string $key, string|int $value): static;

    /*----------------------------------------*
     * Laravel Mail - Content
     *----------------------------------------*/

    /**
     * get view blade file name
     * 
     * @return string|null
     */
    public function view(): string|null;

    /**
     * set view blade file name
     * 
     * @param string $view
     * @return static
     */
    public function setView(string $view): static;

    /**
     * get html blade file name
     * 
     * @return string|null
     */
    public function html(): string|null;

    /**
     * set html blade file name
     * 
     * @param string $view
     * @return static
     */
    public function setHtml(string $html): static;

    /**
     * get text
     * 
     * @return string|null
     */
    public function text(): string|null;

    /**
     * set text
     * 
     * @param string $text
     * @return static
     */
    public function setText(string $text): static;

    /**
     * get markdown text
     * 
     * @return string|null
     */
    public function markdown(): string|null;

    /**
     * set markdown text
     * 
     * @param string $markdown
     * @return static
     */
    public function setMarkdown(string $markdown): static;

    /**
     * get html string
     * 
     * @return string|null
     */
    public function htmlString(): string|null;

    /**
     * set html string
     * 
     * @param string $htmlString
     * @return static
     */
    public function setHtmlString(string $htmlString): static;

    /**
     * get data for view
     * 
     * @return array<string, mixed>
     */
    public function with(): array;

    /**
     * set data for view
     * 
     * @param array<string, mixed> $with
     * @return static
     */
    public function setWith(array $with): static;

    /*----------------------------------------*
     * Laravel Mail - Attachments
     *----------------------------------------*/

    /**
     * set Attachment instance array
     * 
     * @param array<int, \Illuminate\Mail\Mailables\Attachment>
     * @return static
     */
    public function setAttachments(array $attachments): static;

    /**
     * add Attachment instance
     * 
     * @param \Illuminate\Mail\Mailables\Attachment $attachment
     * @return static
     */
    public function addAttachments(Attachment $attachment): static;

    /**
     * add Attachment instance array from file path
     * 
     * @param string $path
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromPath(string $path, string|null $name = null, string|null $mime = null): static;


    /**
     * add Attachment instance array from storage path
     * 
     * @param string $path
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromStorage(string $path, string|null $name = null, string|null $mime = null): static;

    /**
     * add Attachment instance array from storage disk
     * 
     * @param string $path
     * @param string $disk
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromStorageDisk(string $path, string $disk, string|null $name = null, string|null $mime = null): static;

    /**
     * add Attachment instance array from data
     * 
     * @param \Closure $data
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromData(\Closure $data, string|null $name = null, string|null $mime = null): static;

    /*----------------------------------------*
     * Laravel Mail - Headers
     *----------------------------------------*/

    /**
     * get message id
     * 
     * @return string|null
     */
    public function messageId(): string|null;

    /**
     * set message id
     * 
     * @param string $messageId
     * @return static
     */
    public function setMessageId(string $messageId): static;

    /**
     * get reference array
     * 
     * @return array<int, string>
     */
    public function references(): array;

    /**
     * set reference array
     * 
     * @param array<int, string> $references
     * @return static
     */
    public function setReferences(array $references): static;

    /**
     * add reference
     * 
     * @param string $reference
     * @return static
     */
    public function addReferences(string $reference): static;

    /**
     * get text header array
     * 
     * @return array<string, string>
     */
    public function textHeaders(): array;

    /**
     * set text header array
     * 
     * @param array<string, string> $textHeaders
     * @return static
     */
    public function setTextHeaders(array $textHeaders): static;

    /**
     * add text header
     * 
     * @param string $key
     * @param string $value
     * @return static
     */
    public function addTextHeaders(string $key, string $value): static;

    /*----------------------------------------*
     * Pending Mail
     *----------------------------------------*/

    /**
     * get mail driver
     * 
     * @return string|null
     */
    public function driver(): string|null;

    /**
     * set mail driver
     * 
     * @param string $driver
     * @return static
     */
    public function setDriver(string $driver): static;

    /**
     * get locale
     * 
     * @return string|null
     */
    public function locale(): string|null;

    /**
     * set locale
     * 
     * @param string $locale
     * @return static
     */
    public function setLocale(string $locale): static;

    /*----------------------------------------*
     * Queue
     *----------------------------------------*/

    /**
     * register mail queue
     * if $delay is null, delay mail queue
     * 
     * @param \DateTimeInterface|\DateInterval|int|null $delay
     * @return void
     */
    public function queue(\DateTimeInterface|\DateInterval|int|null $delay = null): void;

    /**
     * whether to send the mail after the transaction is committed
     * 
     * @return bool
     */
    public function afterCommit(): bool;

    /**
     * set whether to send the mail after the transaction is committed
     * 
     * @param bool $queueAfterCommit
     * @return static
     */
    public function isAfterCommit(bool $queueAfterCommit = true): static;

    /**
     * get mail queue connection
     * 
     * @return string|null
     */
    public function queueConnection(): string|null;

    /**
     * set mail queue connection
     * 
     * @param string $queueConnection
     * @return static
     */
    public function onConnection(string $queueConnection): static;

    /**
     * get mail queue name
     * 
     * @return string|null
     */
    public function queueName(): string|null;

    /**
     * set mail queue name
     * 
     * @param string $queueName
     * @return static
     */
    public function onQueue(string $queueName): static;
}

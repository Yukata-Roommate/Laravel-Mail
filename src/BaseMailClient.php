<?php

namespace YukataRm\Laravel\Mail;

use YukataRm\Laravel\Mail\Interface\MailClientInterface;

use YukataRm\Laravel\Mail\LaravelMail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\PendingMail;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Headers;

/**
 * Base Mail Client
 * 
 * @package YukataRm\Laravel\Mail
 */
abstract class BaseMailClient implements MailClientInterface
{
    /*----------------------------------------*
     * Mailable
     *----------------------------------------*/

    /**
     * send mail
     * 
     * @return void
     */
    public function send(): void
    {
        $mail = $this->laravelMail();

        $pendingMail = $this->pendingMail();

        $pendingMail->send($mail);
    }

    /**
     * get rendered mail
     * 
     * @return string
     */
    public function render(): string
    {
        $mail = $this->laravelMail();

        return $mail->render();
    }

    /*----------------------------------------*
     * Laravel Mail
     *----------------------------------------*/

    /**
     * get LaravelMail instance
     * 
     * @return \YukataRm\Laravel\Mail\LaravelMail
     */
    public function laravelMail(): LaravelMail
    {
        return new LaravelMail(
            $this->envelope(),
            $this->content(),
            $this->attachments(),
            $this->headers(),
        );
    }

    /**
     * get Envelope instance
     * 
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        $envelope = new Envelope();

        if (!empty($this->senderAddress())) $envelope->from($this->senderAddress(), $this->senderName());

        if (!empty($this->recipientAddress())) $envelope->to($this->recipientAddress(), $this->recipientName());

        if (!empty($this->subject())) $envelope->subject($this->subject());

        if (!empty($this->cc())) $envelope->cc($this->cc());

        if (!empty($this->bcc())) $envelope->bcc($this->bcc());

        if (!empty($this->replyTo())) $envelope->replyTo($this->replyTo());

        if (!empty($this->tags())) $envelope->tags($this->tags());

        if (!empty($this->metadata())) {
            foreach ($this->metadata() as $key => $value) {
                $envelope->metadata($key, $value);
            }
        }

        return $envelope;
    }

    /**
     * get Content instance
     * 
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        $content = new Content();

        if (!empty($this->view())) $content->view($this->view());

        if (!empty($this->html())) $content->html($this->html());

        if (!empty($this->text())) $content->text($this->text());

        if (!empty($this->markdown())) $content->markdown($this->markdown());

        if (!empty($this->htmlString())) $content->htmlString($this->htmlString());

        if (!empty($this->with())) $content->with($this->with());

        return $content;
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
        $headers = new Headers();

        if (!empty($this->messageId())) $headers->messageId($this->messageId());

        if (!empty($this->references())) $headers->references($this->references());

        if (!empty($this->textHeaders())) $headers->text($this->textHeaders());

        return $headers;
    }

    /*----------------------------------------*
     * Laravel Mail - Envelope
     *----------------------------------------*/

    /**
     * sendeer address
     * 
     * @var string|null
     */
    protected string|null $senderAddress = null;

    /**
     * sendeer name
     * 
     * @var string|null
     */
    protected string|null $senderName = null;

    /**
     * recipient address
     * 
     * @var string|null
     */
    protected string|null $recipientAddress = null;

    /**
     * recipient name
     * 
     * @var string|null
     */
    protected string|null $recipientName = null;

    /**
     * mail subject
     * 
     * @var string|null
     */
    protected string|null $subject = null;

    /**
     * cc address array
     * 
     * @var array<int, \Illuminate\Mail\Mailables\Address>
     */
    protected array $cc = [];

    /**
     * bcc address array
     * 
     * @var array<int, \Illuminate\Mail\Mailables\Address>
     */
    protected array $bcc = [];

    /**
     * replyTo address array
     * 
     * @var array<int, \Illuminate\Mail\Mailables\Address>
     */
    protected array $replyTo = [];

    /**
     * tag array
     * 
     * @var array<int, string>
     */
    protected array $tags = [];

    /**
     * metadata array
     * 
     * @var array<string, string|int>
     */
    protected array $metadata = [];

    /**
     * get sender address
     * 
     * @return string|null
     */
    public function senderAddress(): string|null
    {
        return empty($this->senderAddress) ? config("mail.from.address") : $this->senderAddress;
    }

    /**
     * set sender address
     * 
     * @param string $senderAddress
     * @return static
     */
    public function setSenderAddress(string $senderAddress): static
    {
        $this->senderAddress = $senderAddress;

        return $this;
    }

    /**
     * get sender name
     * 
     * @return string|null
     */
    public function senderName(): string|null
    {
        return empty($this->senderName) ? config("mail.from.name") : $this->senderName;
    }

    /**
     * set sender name
     * 
     * @param string $senderName
     * @return static
     */
    public function setSenderName(string $senderName): static
    {
        $this->senderName = $senderName;

        return $this;
    }

    /**
     * get recipient address
     * 
     * @return string|null
     */
    public function recipientAddress(): string|null
    {
        return $this->recipientAddress;
    }

    /**
     * set recipient address
     * 
     * @param string $recipientAddress
     * @return static
     */
    public function setRecipientAddress(string $recipientAddress): static
    {
        $this->recipientAddress = $recipientAddress;

        return $this;
    }

    /**
     * get recipient name
     * 
     * @return string|null
     */
    public function recipientName(): string|null
    {
        return $this->recipientName;
    }

    /**
     * set recipient name
     * 
     * @param string $recipientName
     * @return static
     */
    public function setRecipientName(string $recipientName): static
    {
        $this->recipientName = $recipientName;

        return $this;
    }

    /**
     * get mail subject
     * 
     * @return string|null
     */
    public function subject(): string|null
    {
        return $this->subject;
    }

    /**
     * set mail subject
     * 
     * @param string $subject
     * @return static
     */
    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * get CC address array
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Address>
     */
    public function cc(): array
    {
        return $this->cc;
    }

    /**
     * set CC address array
     * 
     * @param array<int, \Illuminate\Mail\Mailables\Address> $cc
     * @return static
     */
    public function setCc(array $cc): static
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * add CC address
     * 
     * @param string $address
     * @param string|null $name
     * @return static
     */
    public function addCc(string $address, string|null $name = null): static
    {
        $this->cc[] = empty($name) ? new Address($address) : new Address($address, $name);

        return $this;
    }

    /**
     * get BCC address array
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Address>
     */
    public function bcc(): array
    {
        return $this->bcc;
    }

    /**
     * set BCC address array
     * 
     * @param array<int, \Illuminate\Mail\Mailables\Address> $bcc
     * @return static
     */
    public function setBcc(array $bcc): static
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * add BCC address
     * 
     * @param string $address
     * @param string|null $name
     * @return static
     */
    public function addBcc(string $address, string|null $name = null): static
    {
        $this->bcc[] = empty($name) ? new Address($address) : new Address($address, $name);

        return $this;
    }

    /**
     * get ReplyTo address array
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Address>
     */
    public function replyTo(): array
    {
        return $this->replyTo;
    }

    /**
     * set ReplyTo address array
     * 
     * @param array<int, \Illuminate\Mail\Mailables\Address> $replyTo
     * @return static
     */
    public function setReplyTo(array $replyTo): static
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * add ReplyTo address
     * 
     * @param string $address
     * @param string|null $name
     * @return static
     */
    public function addReplyTo(string $address, string|null $name = null): static
    {
        $this->replyTo[] = empty($name) ? new Address($address) : new Address($address, $name);

        return $this;
    }

    /**
     * get tag array
     * 
     * @return array<int, string>
     */
    public function tags(): array
    {
        return $this->tags;
    }

    /**
     * set tag array
     * 
     * @param array<int, string> $tags
     * @return static
     */
    public function setTags(array $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * add tag
     * 
     * @param string $tag
     * @return static
     */
    public function addTags(string $tag): static
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * get metadata array
     * 
     * @return array<string, string|int>
     */
    public function metadata(): array
    {
        return $this->metadata;
    }

    /**
     * set metadata array
     * 
     * @param array<string, string|int> $metadata
     * @return static
     */
    public function setMetadata(array $metadata): static
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * add metadata
     * 
     * @param string $key
     * @param string|int $value
     * @return static
     */
    public function addMetadata(string $key, string|int $value): static
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /*----------------------------------------*
     * Laravel Mail - Content
     *----------------------------------------*/

    /**
     * view blade file name
     * 
     * @var string|null
     */
    protected string|null $view = null;

    /**
     * html blade file name
     * alternative to view
     * 
     * @var string|null
     */
    protected string|null $html = null;

    /**
     * text
     * 
     * @var string|null
     */
    protected string|null $text = null;

    /**
     * markdown text
     * 
     * @var string|null
     */
    protected string|null $markdown = null;

    /**
     * html string
     * 
     * @var string|null
     */
    protected string|null $htmlString = null;

    /**
     * data for view
     * 
     * @var array<string, mixed>
     */
    protected array $with = [];

    /**
     * get view blade file name
     * 
     * @return string|null
     */
    public function view(): string|null
    {
        return $this->view;
    }

    /**
     * set view blade file name
     * 
     * @param string $view
     * @return static
     */
    public function setView(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    /**
     * get html blade file name
     * 
     * @return string|null
     */
    public function html(): string|null
    {
        return $this->html;
    }

    /**
     * set html blade file name
     * 
     * @param string $view
     * @return static
     */
    public function setHtml(string $html): static
    {
        $this->html = $html;

        return $this;
    }

    /**
     * get text
     * 
     * @return string|null
     */
    public function text(): string|null
    {
        return $this->text;
    }

    /**
     * set text
     * 
     * @param string $text
     * @return static
     */
    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    /**
     * get markdown text
     * 
     * @return string|null
     */
    public function markdown(): string|null
    {
        return $this->markdown;
    }

    /**
     * set markdown text
     * 
     * @param string $markdown
     * @return static
     */
    public function setMarkdown(string $markdown): static
    {
        $this->markdown = $markdown;

        return $this;
    }

    /**
     * get html string
     * 
     * @return string|null
     */
    public function htmlString(): string|null
    {
        return $this->htmlString;
    }

    /**
     * set html string
     * 
     * @param string $htmlString
     * @return static
     */
    public function setHtmlString(string $htmlString): static
    {
        $this->htmlString = $htmlString;

        return $this;
    }

    /**
     * get data for view
     * 
     * @return array<string, mixed>
     */
    public function with(): array
    {
        return $this->with;
    }

    /**
     * set data for view
     * 
     * @param array<string, mixed> $with
     * @return static
     */
    public function setWith(array $with): static
    {
        $this->with = $with;

        return $this;
    }

    /*----------------------------------------*
     * Laravel Mail - Attachments
     *----------------------------------------*/

    /**
     * Attachment instance array
     * 
     * @var array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    protected array $attachments = [];

    /**
     * set Attachment instance array
     * 
     * @param array<int, \Illuminate\Mail\Mailables\Attachment>
     * @return static
     */
    public function setAttachments(array $attachments): static
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * add Attachment instance
     * 
     * @param \Illuminate\Mail\Mailables\Attachment $attachment
     * @return static
     */
    public function addAttachments(Attachment $attachment): static
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * add Attachment instance array from file path
     * 
     * @param string $path
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromPath(string $path, string|null $name = null, string|null $mime = null): static
    {
        $attachment = Attachment::fromPath($path);

        if (!empty($name)) $attachment->as($name);

        if (!empty($mime)) $attachment->withMime($mime);

        return $this->addAttachments($attachment);
    }

    /**
     * add Attachment instance array from storage path
     * 
     * @param string $path
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromStorage(string $path, string|null $name = null, string|null $mime = null): static
    {
        $attachment = Attachment::fromStorage($path);

        if (!empty($name)) $attachment->as($name);

        if (!empty($mime)) $attachment->withMime($mime);

        return $this->addAttachments($attachment);
    }

    /**
     * add Attachment instance array from storage disk
     * 
     * @param string $path
     * @param string $disk
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromStorageDisk(string $path, string $disk, string|null $name = null, string|null $mime = null): static
    {
        $attachment = Attachment::fromStorageDisk($disk, $path);

        if (!empty($name)) $attachment->as($name);

        if (!empty($mime)) $attachment->withMime($mime);

        return $this->addAttachments($attachment);
    }

    /**
     * add Attachment instance array from data
     * 
     * @param \Closure $data
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromData(\Closure $data, string|null $name = null, string|null $mime = null): static
    {
        $attachment = Attachment::fromData($data);

        if (!empty($name)) $attachment->as($name);

        if (!empty($mime)) $attachment->withMime($mime);

        return $this->addAttachments($attachment);
    }

    /*----------------------------------------*
     * Laravel Mail - Headers
     *----------------------------------------*/

    /**
     * message id
     * 
     * @var string|null
     */
    protected string|null $messageId = null;

    /**
     * reference array
     * 
     * @var array<int, string>
     */
    protected array $references = [];

    /**
     * text header array
     * 
     * @var array<string, string>
     */
    protected array $textHeaders = [];

    /**
     * get message id
     * 
     * @return string|null
     */
    public function messageId(): string|null
    {
        return $this->messageId;
    }

    /**
     * set message id
     * 
     * @param string $messageId
     * @return static
     */
    public function setMessageId(string $messageId): static
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**
     * get reference array
     * 
     * @return array<int, string>
     */
    public function references(): array
    {
        return $this->references;
    }

    /**
     * set reference array
     * 
     * @param array<int, string> $references
     * @return static
     */
    public function setReferences(array $references): static
    {
        $this->references = $references;

        return $this;
    }

    /**
     * add reference
     * 
     * @param string $reference
     * @return static
     */
    public function addReferences(string $reference): static
    {
        $this->references[] = $reference;

        return $this;
    }

    /**
     * get text header array
     * 
     * @return array<string, string>
     */
    public function textHeaders(): array
    {
        return $this->textHeaders;
    }

    /**
     * set text header array
     * 
     * @param array<string, string> $textHeaders
     * @return static
     */
    public function setTextHeaders(array $textHeaders): static
    {
        $this->textHeaders = $textHeaders;

        return $this;
    }

    /**
     * add text header
     * 
     * @param string $key
     * @param string $value
     * @return static
     */
    public function addTextHeaders(string $key, string $value): static
    {
        $this->textHeaders[$key] = $value;

        return $this;
    }

    /*----------------------------------------*
     * Pending Mail
     *----------------------------------------*/

    /**
     * mail driver
     * 
     * @var string|null
     */
    protected string|null $driver = null;

    /**
     * locale
     * 
     * @var string|null
     */
    protected string|null $locale = null;

    /**
     * get PendingMail instance
     * 
     * @return \Illuminate\Mail\PendingMail
     */
    protected function pendingMail(): PendingMail
    {
        $pendingMail = new PendingMail($this->mailer());

        $locale = $this->locale();

        return is_string($locale) ? $pendingMail->locale($locale) : $pendingMail;
    }

    /**
     * get Mailer instance
     * 
     * @return \Illuminate\Contracts\Mail\Mailer
     */
    protected function mailer(): Mailer
    {
        return Mail::mailer($this->driver());
    }

    /**
     * get mail driver
     * 
     * @return string|null
     */
    public function driver(): string|null
    {
        return $this->driver;
    }

    /**
     * set mail driver
     * 
     * @param string $driver
     * @return static
     */
    public function setDriver(string $driver): static
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * get locale
     * 
     * @return string|null
     */
    public function locale(): string|null
    {
        return $this->locale;
    }

    /**
     * set locale
     * 
     * @param string $locale
     * @return static
     */
    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /*----------------------------------------*
     * Queue
     *----------------------------------------*/

    /**
     * mail queue connection
     * 
     * @var string|null
     */
    protected string|null $queueConnection = null;

    /**
     * mail queue name
     * 
     * @var string|null
     */
    protected string|null $queueName = null;

    /**
     * whether to send the mail after the transaction is committed
     * 
     * @var bool
     */
    protected bool $queueAfterCommit = false;

    /**
     * register mail queue
     * if $delay is null, delay mail queue
     * 
     * @param \DateTimeInterface|\DateInterval|int|null $delay
     * @return void
     */
    public function queue(\DateTimeInterface|\DateInterval|int|null $delay = null): void
    {
        $mail = $this->queueLaravelMail();

        $pendingMail = $this->pendingMail();

        is_null($delay)
            ? $pendingMail->queue($mail)
            : $pendingMail->later($delay, $mail);
    }

    /**
     * get LaravelMail instance for queue
     * 
     * @return \YukataRm\Laravel\Mail\LaravelMail
     */
    protected function queueLaravelMail(): LaravelMail
    {
        $mail = $this->laravelMail();

        $mail->onConnection($this->queueConnection());

        $mail->onQueue($this->queueName());

        if ($this->afterCommit()) $mail->afterCommit();

        return $mail;
    }

    /**
     * whether to send the mail after the transaction is committed
     * 
     * @return bool
     */
    public function afterCommit(): bool
    {
        return $this->queueAfterCommit;
    }

    /**
     * set whether to send the mail after the transaction is committed
     * 
     * @param bool $queueAfterCommit
     * @return static
     */
    public function isAfterCommit(bool $queueAfterCommit = true): static
    {
        $this->queueAfterCommit = $queueAfterCommit;

        return $this;
    }

    /**
     * get mail queue connection
     * 
     * @return string|null
     */
    public function queueConnection(): string|null
    {
        return $this->queueConnection;
    }

    /**
     * set mail queue connection
     * 
     * @param string $queueConnection
     * @return static
     */
    public function onConnection(string $queueConnection): static
    {
        $this->queueConnection = $queueConnection;

        return $this;
    }

    /**
     * get mail queue name
     * 
     * @return string|null
     */
    public function queueName(): string|null
    {
        return $this->queueName;
    }

    /**
     * set mail queue name
     * 
     * @param string $queueName
     * @return static
     */
    public function onQueue(string $queueName): static
    {
        $this->queueName = $queueName;

        return $this;
    }
}

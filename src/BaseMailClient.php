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
 * Laravelのメール送信機能を拡張した基底クラス
 * 
 * @package YukataRm\Laravel\Mail
 */
abstract class BaseMailClient implements MailClientInterface
{
    /*----------------------------------------*
     * Mailable
     *----------------------------------------*/

    /**
     * 使用するMailerのドライバー名
     * 
     * @var ?string
     */
    protected ?string $driver = null;

    /**
     * メールに使用する言語
     * 
     * @var ?string
     */
    protected ?string $locale = null;

    /**
     * メールをQueueに登録する際のConnection名
     * 
     * @var ?string
     */
    protected ?string $queueConnection = null;

    /**
     * メールをQueueに登録する際のQueue名
     * 
     * @var ?string
     */
    protected ?string $queueName = null;

    /**
     * メールをQueueに登録する処理をTransactionのCommit後に実行するかどうか
     * 
     * @var bool
     */
    protected bool $queueAfterCommit = false;

    /**
     * メールを送信する
     * 
     * @return void
     */
    public function send(): void
    {
        // Mailクラスを生成する
        $mail = $this->getMailInstance();

        // PendingMailクラスを生成する
        $pendingMail = $this->getPendingMail();

        // メールを送信する
        $pendingMail->send($mail);
    }

    /**
     * メールに使用される評価済みのHTMLを取得する
     * 
     * @return string
     */
    public function render(): string
    {
        // Mailクラスを生成する
        $mail = $this->getMailInstance();

        return $mail->render();
    }

    /**
     * Mailクラスを生成する
     * 
     * @return \YukataRm\Laravel\Mail\LaravelMail
     */
    protected function getMailInstance(): LaravelMail
    {
        return new LaravelMail(
            $this->getEnvelope(),
            $this->getContent(),
            $this->getAttachments(),
            $this->getHeaders(),
        );
    }

    /**
     * Mailerインスタンスを生成する
     * 
     * @return \Illuminate\Contracts\Mail\Mailer
     */
    protected function getMailer(): Mailer
    {
        return Mail::mailer($this->driver());
    }

    /**
     * 使用するMailerのドライバー名を取得する
     * 
     * @return ?string
     */
    public function driver(): ?string
    {
        return $this->driver;
    }

    /**
     * 使用するMailerのドライバー名を設定する
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
     * PendingMailインスタンスを生成する
     * 
     * @return \Illuminate\Mail\PendingMail
     */
    protected function getPendingMail(): PendingMail
    {
        $pendingMail = new PendingMail($this->getMailer());

        // locale
        $locale = $this->locale();

        if (is_string($locale)) $pendingMail = $pendingMail->locale($locale);

        return $pendingMail;
    }

    /**
     * メールに使用する言語を取得する
     * 
     * @return ?string
     */
    public function locale(): ?string
    {
        return $this->locale;
    }

    /**
     * メールに使用する言語を設定する
     * 
     * @param string $locale
     * @return static
     */
    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * メール送信処理をQueueに登録する
     * $delayがnullでない場合は、メール送信処理を指定した時間後に実行する
     * 
     * @param \DateTimeInterface|\DateInterval|int|null $delay
     * @return void
     */
    public function queue(\DateTimeInterface|\DateInterval|int|null $delay = null): void
    {
        // Mailクラスを生成する
        $mail = $this->getQueueMailInstance();

        // PendingMailクラスを生成する
        $pendingMail = $this->getPendingMail();

        // メール送信処理をQueueに登録する
        // $delayがnullの場合は、PendingMail::queue()を使用する
        // $delayがnull以外の場合は、PendingMail::later()を使用する
        is_null($delay)
            ? $pendingMail->queue($mail)
            : $pendingMail->later($delay, $mail);
    }

    /**
     * Queueに登録するMailerインスタンスを生成する
     * 
     * @return \YukataRm\Laravel\Mail\LaravelMail
     */
    protected function getQueueMailInstance(): LaravelMail
    {
        $mail = $this->getMailInstance();

        // MailにQueueに登録する際のConnection名を設定する
        $mail = $mail->onConnection($this->queueConnection());

        // MailにQueueに登録する際のQueue名を設定する
        $mail = $mail->onQueue($this->queueName());

        // MailにQueueに登録する処理をTransactionのCommit後に実行するかどうかを設定する
        if ($this->afterCommit()) $mail = $mail->afterCommit();

        return $mail;
    }

    /**
     * メールをQueueに登録する処理をTransactionのCommit後に実行するかどうかを取得する
     * 
     * @return bool
     */
    public function afterCommit(): bool
    {
        return $this->queueAfterCommit;
    }

    /**
     * メールをQueueに登録する処理をTransactionのCommit後に実行する
     * 
     * @param bool $queueAfterCommit
     * @return static
     */
    public function setAfterCommit(bool $queueAfterCommit = true): static
    {
        $this->queueAfterCommit = $queueAfterCommit;

        return $this;
    }

    /**
     * メールをQueueに登録する際のConnection名を取得する
     * 
     * @return ?string
     */
    public function queueConnection(): ?string
    {
        return $this->queueConnection;
    }

    /**
     * メールをQueueに登録する際のConnection名を設定する
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
     * メールをQueueに登録する際のQueue名を取得する
     * 
     * @return ?string
     */
    public function queueName(): ?string
    {
        return $this->queueName;
    }

    /**
     * メールをQueueに登録する際のQueue名を設定する
     * 
     * @param string $queueName
     * @return static
     */
    public function onQueue(string $queueName): static
    {
        $this->queueName = $queueName;

        return $this;
    }

    /*----------------------------------------*
     * Envelope
     *----------------------------------------*/

    /**
     * 送信元メールアドレス
     * 
     * @var ?string
     */
    protected ?string $senderAddress = null;

    /**
     * 送信元名
     * 
     * @var ?string
     */
    protected ?string $senderName = null;

    /**
     * 送信先メールアドレス
     * 
     * @var ?string
     */
    protected ?string $recipientAddress = null;

    /**
     * 送信先名
     * 
     * @var ?string
     */
    protected ?string $recipientName = null;

    /**
     * 件名
     * 
     * @var ?string
     */
    protected ?string $subject = null;

    /**
     * CCのメールアドレスと名前の配列
     * 
     * @var array<int, \Illuminate\Mail\Mailables\Address>
     */
    protected array $cc = [];

    /**
     * BCCのメールアドレスと名前の配列
     * 
     * @var array<int, \Illuminate\Mail\Mailables\Address>
     */
    protected array $bcc = [];

    /**
     * ReplyToのメールアドレスと名前の配列
     * 
     * @var array<int, \Illuminate\Mail\Mailables\Address>
     */
    protected array $replyTo = [];

    /**
     * タグの配列
     * 
     * @var array<int, string>
     */
    protected array $tags = [];

    /**
     * メタデータの配列
     * 
     * @var array<string, string|int>
     */
    protected array $metadata = [];

    /**
     * Envelopeクラスを生成する
     * 
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    protected function getEnvelope(): Envelope
    {
        $envelope = new Envelope();

        // 送信先
        $envelope = $this->setEnvelopeSender($envelope);

        // 送信元
        $envelope = $this->setEnvelopeRecipient($envelope);

        // 件名
        $envelope = $this->setEnvelopeSubject($envelope);

        // CC
        $envelope = $this->setEnvelopeCc($envelope);

        // BCC
        $envelope = $this->setEnvelopeBcc($envelope);

        // ReplyTo
        $envelope = $this->setEnvelopeReplyTo($envelope);

        // Tags
        $envelope = $this->setEnvelopeTags($envelope);

        // Metadata
        $envelope = $this->setEnvelopeMetadata($envelope);

        return $envelope;
    }

    /**
     * Addressインスタンスを生成する
     * 
     * @param string $address
     * @param ?string $name
     * @return \Illuminate\Mail\Mailables\Address
     */
    protected function getAddressInstance(string $address, ?string $name = null): Address
    {
        return empty($name) ? new Address($address) : new Address($address, $name);
    }

    /**
     * 送信元のAddressインスタンスを設定する
     *
     * @param \Illuminate\Mail\Mailables\Envelope $envelope
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    protected function setEnvelopeSender(Envelope $envelope): Envelope
    {
        $senderAddress = $this->senderAddress();
        $senderName    = $this->senderName();

        if (empty($senderAddress)) return $envelope;

        $address = $this->getAddressInstance($senderAddress, $senderName);

        return $envelope->from($address);
    }

    /**
     * 送信元メールアドレスを取得する
     * 
     * @return ?string
     */
    public function senderAddress(): ?string
    {
        return empty($this->senderAddress) ? config("mail.from.address") : $this->senderAddress;
    }

    /**
     * 送信元メールアドレスを設定する
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
     * 送信元名を取得する
     * 
     * @return ?string
     */
    public function senderName(): ?string
    {
        return empty($this->senderName) ? config("mail.from.name") : $this->senderName;
    }

    /**
     * 送信元名を設定する
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
     * 送信先のAddressインスタンスを設定する
     * 
     * @param \Illuminate\Mail\Mailables\Envelope $envelope
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    protected function setEnvelopeRecipient(Envelope $envelope): Envelope
    {
        $recipientAddress = $this->recipientAddress();
        $recipientName    = $this->recipientName();

        if (empty($recipientAddress)) return $envelope;

        $address = $this->getAddressInstance($recipientAddress, $recipientName);

        return $envelope->to($address);
    }

    /**
     * 送信先メールアドレスを取得する
     * 
     * @return ?string
     */
    public function recipientAddress(): ?string
    {
        return $this->recipientAddress;
    }

    /**
     * 送信先メールアドレスを設定する
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
     * 送信先名を取得する
     * 
     * @return ?string
     */
    public function recipientName(): ?string
    {
        return $this->recipientName;
    }

    /**
     * 送信先名を設定する
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
     * 件名を設定する
     * 
     * @param \Illuminate\Mail\Mailables\Envelope $envelope
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    protected function setEnvelopeSubject(Envelope $envelope): Envelope
    {
        $subject = $this->subject();

        if (empty($subject)) return $envelope;

        return $envelope->subject($subject);
    }

    /**
     * 件名を取得する
     * 
     * @return ?string
     */
    public function subject(): ?string
    {
        return $this->subject;
    }

    /**
     * 件名を設定する
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
     * CCのAddressインスタンスが格納された配列を設定する
     * 
     * @param \Illuminate\Mail\Mailables\Envelope $envelope
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    protected function setEnvelopeCc(Envelope $envelope): Envelope
    {
        $cc = $this->cc();

        if (empty($cc)) return $envelope;

        return $envelope->cc($cc);
    }

    /**
     * CCのAddressインスタンスが格納された配列を取得する
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Address>
     */
    public function cc(): array
    {
        return $this->cc;
    }

    /**
     * CCのメールアドレスと名前の配列を設定する
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
     * CCのメールアドレスと名前を追加する
     * 
     * @param string $ccAddress
     * @param ?string $ccName
     * @return static
     */
    public function addCc(string $ccAddress, ?string $ccName = null): static
    {
        $this->cc[] = $this->getAddressInstance($ccAddress, $ccName);

        return $this;
    }

    /**
     * BCCのAddressインスタンスが格納された配列を設定する
     * 
     * @param \Illuminate\Mail\Mailables\Envelope $envelope
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    protected function setEnvelopeBcc(Envelope $envelope): Envelope
    {
        $bcc = $this->bcc();

        if (empty($bcc)) return $envelope;

        return $envelope->bcc($bcc);
    }

    /**
     * BCCのメールアドレスと名前の配列を取得する
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Address>
     */
    public function bcc(): array
    {
        return $this->bcc;
    }

    /**
     * BCCのメールアドレスと名前の配列を設定する
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
     * BCCのメールアドレスと名前を追加する
     * 
     * @param string $bccAddress
     * @param ?string $bccName
     * @return static
     */
    public function addBcc(string $bccAddress, ?string $bccName = null): static
    {
        $this->bcc[] = $this->getAddressInstance($bccAddress, $bccName);

        return $this;
    }

    /**
     * ReplyToのAddressインスタンスが格納された配列を設定する
     * 
     * @param \Illuminate\Mail\Mailables\Envelope $envelope
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    protected function setEnvelopeReplyTo(Envelope $envelope): Envelope
    {
        $replyTo = $this->replyTo();

        if (empty($replyTo)) return $envelope;

        return $envelope->replyTo($replyTo);
    }

    /**
     * ReplyToのメールアドレスと名前の配列を取得する
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Address>
     */
    public function replyTo(): array
    {
        return $this->replyTo;
    }

    /**
     * ReplyToのメールアドレスと名前の配列を設定する
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
     * ReplyToのメールアドレスと名前を追加する
     * 
     * @param string $replyToAddress
     * @param ?string $replyToName
     * @return static
     */
    public function addReplyTo(string $replyToAddress, ?string $replyToName = null): static
    {
        $this->replyTo[] = $this->getAddressInstance($replyToAddress, $replyToName);

        return $this;
    }

    /**
     * タグの配列を設定する
     * 
     * @param \Illuminate\Mail\Mailables\Envelope $envelope
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    protected function setEnvelopeTags(Envelope $envelope): Envelope
    {
        $tags = $this->tags();

        if (empty($tags)) return $envelope;

        return $envelope->tags($tags);
    }

    /**
     * タグの配列を取得する
     * 
     * @return array<int, string>
     */
    public function tags(): array
    {
        return $this->tags;
    }

    /**
     * タグの配列を設定する
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
     * タグを追加する
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
     * メタデータの配列を設定する
     * 
     * @param \Illuminate\Mail\Mailables\Envelope $envelope
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    protected function setEnvelopeMetadata(Envelope $envelope): Envelope
    {
        $metadata = $this->metadata();

        if (empty($metadata)) return $envelope;

        // メタデータは一つずつ設定する必要がある
        foreach ($metadata as $key => $value) {
            $envelope = $envelope->metadata($key, $value);
        }

        return $envelope;
    }

    /**
     * メタデータの配列を取得する
     * 
     * @return array<string, string|int>
     */
    public function metadata(): array
    {
        return $this->metadata;
    }

    /**
     * メタデータの配列を設定する
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
     * メタデータを追加する
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
     * Content
     *----------------------------------------*/

    /**
     * 使用するviewのbladeファイル名
     * 
     * @var ?string
     */
    protected ?string $view = null;

    /**
     * 使用するviewのbladeファイル名
     * $viewの代替構文
     * 
     * @var ?string
     */
    protected ?string $html = null;

    /**
     * 使用するテキスト
     * 
     * @var ?string
     */
    protected ?string $text = null;

    /**
     * 使用するMarkdown
     * 
     * @var ?string
     */
    protected ?string $markdown = null;

    /**
     * 使用するHTML
     * 
     * @var ?string
     */
    protected ?string $htmlString = null;

    /**
     * viewで使用するデータ
     * 
     * @var array<string, mixed>
     */
    protected array $with = [];

    /**
     * Contentクラスを生成する
     * 
     * @return \Illuminate\Mail\Mailables\Content
     */
    protected function getContent(): Content
    {
        $content = new Content();

        // view
        $content = $this->setContentView($content);

        // html
        $content = $this->setContentHtml($content);

        // text
        $content = $this->setContentText($content);

        // markdown
        $content = $this->setContentMarkdown($content);

        // htmlString
        $content = $this->setContentHtmlString($content);

        // with
        $content = $this->setContentWith($content);

        return $content;
    }

    /**
     * 使用するviewのbladeファイル名を設定する
     * 
     * @param \Illuminate\Mail\Mailables\Content $content
     * @return \Illuminate\Mail\Mailables\Content
     */
    protected function setContentView(Content $content): Content
    {
        $view = $this->view();

        if (empty($view)) return $content;

        return $content->view($view);
    }

    /**
     * 使用するviewのbladeファイル名を取得する
     * 
     * @return ?string
     */
    public function view(): ?string
    {
        return $this->view;
    }

    /**
     * 使用するviewのbladeファイル名を設定する
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
     * 使用するviewのbladeファイル名を設定する
     * 
     * @param \Illuminate\Mail\Mailables\Content $content
     * @return \Illuminate\Mail\Mailables\Content
     */
    protected function setContentHtml(Content $content): Content
    {
        $html = $this->html();

        if (empty($html)) return $content;

        return $content->html($html);
    }

    /**
     * 使用するviewのbladeファイル名を取得する
     * 
     * @return ?string
     */
    public function html(): ?string
    {
        return $this->html;
    }

    /**
     * 使用するviewのbladeファイル名を設定する
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
     * 使用するテキストを設定する
     * 
     * @param \Illuminate\Mail\Mailables\Content $content
     * @return \Illuminate\Mail\Mailables\Content
     */
    protected function setContentText(Content $content): Content
    {
        $text = $this->text();

        if (empty($text)) return $content;

        return $content->text($text);
    }

    /**
     * 使用するテキストを取得する
     * 
     * @return ?string
     */
    public function text(): ?string
    {
        return $this->text;
    }

    /**
     * 使用するテキストを設定する
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
     * 使用するMarkdownを設定する
     * 
     * @param \Illuminate\Mail\Mailables\Content $content
     * @return \Illuminate\Mail\Mailables\Content
     */
    protected function setContentMarkdown(Content $content): Content
    {
        $markdown = $this->markdown();

        if (empty($markdown)) return $content;

        return $content->markdown($markdown);
    }

    /**
     * 使用するMarkdownを取得する
     * 
     * @return ?string
     */
    public function markdown(): ?string
    {
        return $this->markdown;
    }

    /**
     * 使用するMarkdownを設定する
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
     * 使用するHTMLを設定する
     * 
     * @param \Illuminate\Mail\Mailables\Content $content
     * @return \Illuminate\Mail\Mailables\Content
     */
    protected function setContentHtmlString(Content $content): Content
    {
        $htmlString = $this->htmlString();

        if (empty($htmlString)) return $content;

        return $content->htmlString($htmlString);
    }

    /**
     * 使用するHTMLを取得する
     * 
     * @return ?string
     */
    public function htmlString(): ?string
    {
        return $this->htmlString;
    }

    /**
     * 使用するHTMLを設定する
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
     * viewで使用するデータを設定する
     * 
     * @param \Illuminate\Mail\Mailables\Content $content
     * @return \Illuminate\Mail\Mailables\Content
     */
    protected function setContentWith(Content $content): Content
    {
        $with = $this->with();

        if (empty($with)) return $content;

        return $content->with($with);
    }

    /**
     * viewで使用するデータを取得する
     * 
     * @return array<string, mixed>
     */
    public function with(): array
    {
        return $this->with;
    }

    /**
     * viewで使用するデータを設定する
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
     * Attachments
     *----------------------------------------*/

    /**
     * 添付ファイルの配列
     * 
     * @var array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    protected array $attachments = [];

    /**
     * Attachmentクラスの配列を生成する
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    protected function getAttachments(): array
    {
        return $this->attachments();
    }

    /**
     * Attachmentインスタンスを生成する
     * 
     * @param \Illuminate\Mail\Mailables\Attachment $attachment
     * @param string|null $name
     * @param string|null $mime
     * @return \Illuminate\Mail\Mailables\Attachment
     */
    protected function getAttachmentInstance(Attachment $attachment, ?string $name = null, ?string $mime = null): Attachment
    {
        // ファイル名が指定されている場合は設定する
        if (!empty($name)) $attachment = $attachment->as($name);

        // MIMEタイプが指定されている場合は設定する
        if (!empty($mime)) $attachment = $attachment->withMime($mime);

        return $attachment;
    }


    /**
     * 添付ファイルを取得する
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return $this->attachments;
    }

    /**
     * 添付ファイルを設定する
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
     * 添付ファイルを追加する
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
     * ファイルパスを使用して添付ファイルを追加する
     * 
     * @param string $path
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromPath(string $path, ?string $name = null, ?string $mime = null): static
    {
        return $this->addAttachments($this->getAttachmentInstance(
            Attachment::fromPath($path),
            $name,
            $mime
        ));
    }

    /**
     * ファイルパスを使用してStorage配下の添付ファイルを追加する
     * 
     * @param string $path
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromStorage(string $path, ?string $name = null, ?string $mime = null): static
    {
        return $this->addAttachments($this->getAttachmentInstance(
            Attachment::fromStorage($path),
            $name,
            $mime
        ));
    }

    /**
     * ファイルパスとディスク名を使用してStorage配下の添付ファイルを追加する
     * 
     * @param string $path
     * @param string $disk
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromStorageDisk(string $path, string $disk, ?string $name = null, ?string $mime = null): static
    {
        return $this->addAttachments($this->getAttachmentInstance(
            Attachment::fromStorageDisk($disk, $path),
            $name,
            $mime
        ));
    }

    /**
     * ファイルパスを使用して添付ファイルを追加する
     * 
     * @param \Closure $data
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromData(\Closure $data, ?string $name = null, ?string $mime = null): static
    {
        return $this->addAttachments($this->getAttachmentInstance(
            Attachment::fromData($data),
            $name,
            $mime
        ));
    }

    /*----------------------------------------*
     * Headers
     *----------------------------------------*/

    /**
     * メッセージID
     * 
     * @var ?string
     */
    protected ?string $messageId = null;

    /**
     * リファレンスの配列
     * 
     * @var array<int, string>
     */
    protected array $references = [];

    /**
     * テキストヘッダーの配列
     * 
     * @var array<string, string>
     */
    protected array $textHeaders = [];

    /**
     * Attachmentクラスの配列を生成する
     * 
     * @return \Illuminate\Mail\Mailables\Headers
     */
    protected function getHeaders(): Headers
    {
        $headers = new Headers();

        // メッセージID
        $headers = $this->setHeadersMessageId($headers);

        // リファレンス
        $headers = $this->setHeadersReferences($headers);

        // テキストヘッダー
        $headers = $this->setHeadersTextHeaders($headers);

        return $headers;
    }

    /**
     * メッセージIDを設定する
     * 
     * @param \Illuminate\Mail\Mailables\Headers $headers
     * @return \Illuminate\Mail\Mailables\Headers
     */
    protected function setHeadersMessageId(Headers $headers): Headers
    {
        $messageId = $this->messageId();

        if (empty($messageId)) return $headers;

        return $headers->messageId($messageId);
    }

    /**
     * メッセージIDを取得する
     * 
     * @return ?string
     */
    public function messageId(): ?string
    {
        return $this->messageId;
    }

    /**
     * メッセージIDを設定する
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
     * リファレンスの配列を設定する
     * 
     * @param \Illuminate\Mail\Mailables\Headers $headers
     * @return \Illuminate\Mail\Mailables\Headers
     */
    protected function setHeadersReferences(Headers $headers): Headers
    {
        $references = $this->references();

        if (empty($references)) return $headers;

        return $headers->references($references);
    }

    /**
     * リファレンスの配列を取得する
     * 
     * @return array<int, string>
     */
    public function references(): array
    {
        return $this->references;
    }

    /**
     * リファレンスの配列を設定する
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
     * リファレンスを追加する
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
     * テキストヘッダーの配列を設定する
     * 
     * @param \Illuminate\Mail\Mailables\Headers $headers
     * @return \Illuminate\Mail\Mailables\Headers
     */
    protected function setHeadersTextHeaders(Headers $headers): Headers
    {
        $textHeaders = $this->textHeaders();

        if (empty($textHeaders)) return $headers;

        return $headers->text($textHeaders);
    }

    /**
     * テキストヘッダーの配列を取得する
     * 
     * @return array<string, string>
     */
    public function textHeaders(): array
    {
        return $this->textHeaders;
    }

    /**
     * テキストヘッダーの配列を設定する
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
     * テキストヘッダーを追加する
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
}

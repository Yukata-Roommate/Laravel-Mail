<?php

namespace YukataRm\Laravel\Mail\Interface;

use Illuminate\Mail\Mailables\Attachment;

/**
 * MailClientのInterface
 * 
 * @package YukataRm\Laravel\Mail\Interface
 */
interface MailClientInterface
{
    /*----------------------------------------*
     * Mailable
     *----------------------------------------*/

    /**
     * メールを送信する
     * 
     * @return void
     */
    public function send(): void;

    /**
     * メールに使用される評価済みのHTMLを取得する
     * 
     * @return string
     */
    public function render(): string;

    /**
     * 使用するMailerのドライバー名を取得する
     * 
     * @return ?string
     */
    public function driver(): ?string;

    /**
     * 使用するMailerのドライバー名を設定する
     * 
     * @param string $driver
     * @return static
     */
    public function setDriver(string $driver): static;

    /**
     * メールに使用する言語を取得する
     * 
     * @return ?string
     */
    public function locale(): ?string;

    /**
     * メールに使用する言語を設定する
     * 
     * @param string $locale
     * @return static
     */
    public function setLocale(string $locale): static;

    /**
     * メール送信処理をQueueに登録する
     * $delayがnullでない場合は、メール送信処理を指定した時間後に実行する
     * 
     * @param \DateTimeInterface|\DateInterval|int|null $delay
     * @return void
     */
    public function queue(\DateTimeInterface|\DateInterval|int|null $delay = null): void;

    /**
     * メールをQueueに登録する処理をTransactionのCommit後に実行するかどうかを取得する
     * 
     * @return bool
     */
    public function afterCommit(): bool;

    /**
     * メールをQueueに登録する処理をTransactionのCommit後に実行する
     * 
     * @param bool $queueAfterCommit
     * @return static
     */
    public function setAfterCommit(bool $queueAfterCommit = true): static;

    /**
     * メールをQueueに登録する際のConnection名を取得する
     * 
     * @return ?string
     */
    public function queueConnection(): ?string;

    /**
     * メールをQueueに登録する際のConnection名を設定する
     * 
     * @param string $queueConnection
     * @return static
     */
    public function onConnection(string $queueConnection): static;

    /**
     * メールをQueueに登録する際のQueue名を取得する
     * 
     * @return ?string
     */
    public function queueName(): ?string;

    /**
     * メールをQueueに登録する際のQueue名を設定する
     * 
     * @param string $queueName
     * @return static
     */
    public function onQueue(string $queueName): static;

    /*----------------------------------------*
     * Envelope
     *----------------------------------------*/

    /**
     * 送信元メールアドレスを取得する
     * 
     * @return ?string
     */
    public function senderAddress(): ?string;

    /**
     * 送信元メールアドレスを設定する
     * 
     * @param string $senderAddress
     * @return static
     */
    public function setSenderAddress(string $senderAddress): static;

    /**
     * 送信元名を取得する
     * 
     * @return ?string
     */
    public function senderName(): ?string;

    /**
     * 送信元名を設定する
     * 
     * @param string $senderName
     * @return static
     */
    public function setSenderName(string $senderName): static;

    /**
     * 送信先メールアドレスを取得する
     * 
     * @return ?string
     */
    public function recipientAddress(): ?string;

    /**
     * 送信先メールアドレスを設定する
     * 
     * @param string $recipientAddress
     * @return static
     */
    public function setRecipientAddress(string $recipientAddress): static;

    /**
     * 送信先名を取得する
     * 
     * @return ?string
     */
    public function recipientName(): ?string;

    /**
     * 送信先名を設定する
     * 
     * @param string $recipientName
     * @return static
     */
    public function setRecipientName(string $recipientName): static;

    /**
     * 件名を取得する
     * 
     * @return ?string
     */
    public function subject(): ?string;

    /**
     * 件名を設定する
     * 
     * @param string $subject
     * @return static
     */
    public function setSubject(string $subject): static;

    /**
     * CCのAddressインスタンスが格納された配列を取得する
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Address>
     */
    public function cc(): array;

    /**
     * CCのメールアドレスと名前の配列を設定する
     * 
     * @param array<int, \Illuminate\Mail\Mailables\Address> $cc
     * @return static
     */
    public function setCc(array $cc): static;

    /**
     * CCのメールアドレスと名前を追加する
     * 
     * @param string $ccAddress
     * @param ?string $ccName
     * @return static
     */
    public function addCc(string $ccAddress, ?string $ccName = null): static;

    /**
     * BCCのメールアドレスと名前の配列を取得する
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Address>
     */
    public function bcc(): array;

    /**
     * BCCのメールアドレスと名前の配列を設定する
     * 
     * @param array<int, \Illuminate\Mail\Mailables\Address> $bcc
     * @return static
     */
    public function setBcc(array $bcc): static;

    /**
     * BCCのメールアドレスと名前を追加する
     * 
     * @param string $bccAddress
     * @param ?string $bccName
     * @return static
     */
    public function addBcc(string $bccAddress, ?string $bccName = null): static;

    /**
     * ReplyToのメールアドレスと名前の配列を取得する
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Address>
     */
    public function replyTo(): array;

    /**
     * ReplyToのメールアドレスと名前の配列を設定する
     * 
     * @param array<int, \Illuminate\Mail\Mailables\Address> $replyTo
     * @return static
     */
    public function setReplyTo(array $replyTo): static;

    /**
     * ReplyToのメールアドレスと名前を追加する
     * 
     * @param string $replyToAddress
     * @param ?string $replyToName
     * @return static
     */
    public function addReplyTo(string $replyToAddress, ?string $replyToName = null): static;

    /**
     * タグの配列を取得する
     * 
     * @return array<int, string>
     */
    public function tags(): array;

    /**
     * タグの配列を設定する
     * 
     * @param array<int, string> $tags
     * @return static
     */
    public function setTags(array $tags): static;

    /**
     * タグを追加する
     * 
     * @param string $tag
     * @return static
     */
    public function addTags(string $tag): static;

    /**
     * メタデータの配列を取得する
     * 
     * @return array<string, string|int>
     */
    public function metadata(): array;

    /**
     * メタデータの配列を設定する
     * 
     * @param array<string, string|int> $metadata
     * @return static
     */
    public function setMetadata(array $metadata): static;

    /**
     * メタデータを追加する
     * 
     * @param string $key
     * @param string|int $value
     * @return static
     */
    public function addMetadata(string $key, string|int $value): static;

    /*----------------------------------------*
     * Content
     *----------------------------------------*/

    /**
     * 使用するviewのbladeファイル名を取得する
     * 
     * @return ?string
     */
    public function view(): ?string;

    /**
     * 使用するviewのbladeファイル名を設定する
     * 
     * @param string $view
     * @return static
     */
    public function setView(string $view): static;

    /**
     * 使用するviewのbladeファイル名を取得する
     * 
     * @return ?string
     */
    public function html(): ?string;

    /**
     * 使用するviewのbladeファイル名を設定する
     * 
     * @param string $view
     * @return static
     */
    public function setHtml(string $html): static;

    /**
     * 使用するテキストを取得する
     * 
     * @return ?string
     */
    public function text(): ?string;

    /**
     * 使用するテキストを設定する
     * 
     * @param string $text
     * @return static
     */
    public function setText(string $text): static;

    /**
     * 使用するMarkdownを取得する
     * 
     * @return ?string
     */
    public function markdown(): ?string;

    /**
     * 使用するMarkdownを設定する
     * 
     * @param string $markdown
     * @return static
     */
    public function setMarkdown(string $markdown): static;

    /**
     * 使用するHTMLを取得する
     * 
     * @return ?string
     */
    public function htmlString(): ?string;

    /**
     * 使用するHTMLを設定する
     * 
     * @param string $htmlString
     * @return static
     */
    public function setHtmlString(string $htmlString): static;

    /**
     * viewで使用するデータを取得する
     * 
     * @return array<string, mixed>
     */
    public function with(): array;

    /**
     * viewで使用するデータを設定する
     * 
     * @param array<string, mixed> $with
     * @return static
     */
    public function setWith(array $with): static;

    /*----------------------------------------*
     * Attachments
     *----------------------------------------*/

    /**
     * 添付ファイルを取得する
     * 
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array;

    /**
     * 添付ファイルを設定する
     * 
     * @param array<int, \Illuminate\Mail\Mailables\Attachment>
     * @return static
     */
    public function setAttachments(array $attachments): static;

    /**
     * 添付ファイルを追加する
     * 
     * @param \Illuminate\Mail\Mailables\Attachment $attachment
     * @return static
     */
    public function addAttachments(Attachment $attachment): static;

    /**
     * ファイルパスを使用して添付ファイルを追加する
     * 
     * @param string $path
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromPath(string $path, ?string $name = null, ?string $mime = null): static;

    /**
     * ファイルパスを使用してStorage配下の添付ファイルを追加する
     * 
     * @param string $path
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromStorage(string $path, ?string $name = null, ?string $mime = null): static;

    /**
     * ファイルパスとディスク名を使用してStorage配下の添付ファイルを追加する
     * 
     * @param string $path
     * @param string $disk
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromStorageDisk(string $path, string $disk, ?string $name = null, ?string $mime = null): static;

    /**
     * ファイルパスを使用して添付ファイルを追加する
     * 
     * @param \Closure $data
     * @param string|null $name
     * @param string|null $mime
     * @return static
     */
    public function addAttachmentsFromData(\Closure $data, ?string $name = null, ?string $mime = null): static;

    /*----------------------------------------*
     * Headers
     *----------------------------------------*/

    /**
     * メッセージIDを取得する
     * 
     * @return ?string
     */
    public function messageId(): ?string;

    /**
     * メッセージIDを設定する
     * 
     * @param string $messageId
     * @return static
     */
    public function setMessageId(string $messageId): static;

    /**
     * リファレンスの配列を取得する
     * 
     * @return array<int, string>
     */
    public function references(): array;

    /**
     * リファレンスの配列を設定する
     * 
     * @param array<int, string> $references
     * @return static
     */
    public function setReferences(array $references): static;

    /**
     * リファレンスを追加する
     * 
     * @param string $reference
     * @return static
     */
    public function addReferences(string $reference): static;

    /**
     * テキストヘッダーの配列を取得する
     * 
     * @return array<string, string>
     */
    public function textHeaders(): array;

    /**
     * テキストヘッダーの配列を設定する
     * 
     * @param array<string, string> $textHeaders
     * @return static
     */
    public function setTextHeaders(array $textHeaders): static;

    /**
     * テキストヘッダーを追加する
     * 
     * @param string $key
     * @param string $value
     * @return static
     */
    public function addTextHeaders(string $key, string $value): static;
}

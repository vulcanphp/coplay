<?php

namespace VulcanPhp\Core\Helpers;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mail extends PHPMailer
{
    protected array $data;

    public function __construct(public $Body)
    {
        if (config('mail.enabled') !== true) {
            throw new Exception('Mail Does Not Enabled');
        }

        parent::__construct(true);

        $this->CharSet = PHPMailer::CHARSET_UTF8;
        $this->isHTML();
        $this->setFrom(config('mail.from_email'), config('mail.from_name', ''));
        $this->addReplyTo(config('mail.reply_to_email'));

        if (config('mail.smtp_enabled', false)) {
            $this->isSMTP();
            $this->Host       = config('mail.smtp.host');
            $this->SMTPAuth   = config('mail.smtp.auth', false);
            $this->Username   = config('mail.smtp.username');
            $this->Password   = config('mail.smtp.password');
            $this->SMTPSecure = config('mail.smtp.secure');
            $this->Port       = config('mail.smtp.port');
        }
    }

    public static function template(string $tmeplate, array $params = []): static
    {
        return new static(view('mail.' . $tmeplate, $params));
    }

    public static function message(string $message): static
    {
        return new static($message);
    }

    public function to(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function send()
    {
        try {

            if (!isset($this->Body) || empty($this->Body)) {
                throw new Exception("mail template or body must is empty");
            } elseif (!isset($this->data) || empty($this->data)) {
                throw new Exception("mail information does not set");
            }

            $this->addAddress($this->data['email'], $this->data['name']);

            if (isset($this->data['cc_address']) && is_array($this->data['cc_address'])) {
                foreach ($this->data['cc_address'] as $cc) {
                    if (is_array($cc)) {
                        $this->addCC($cc['email'], $cc['name'] ?? '');
                    } else {
                        $this->addCC($cc);
                    }
                }
            }

            if (isset($this->data['bcc_address']) && is_array($this->data['bcc_address'])) {
                foreach ($this->data['bcc_address'] as $bcc) {
                    if (is_array($bcc)) {
                        $this->addBCC($bcc['email'], $bcc['name'] ?? '');
                    } else {
                        $this->addBCC($bcc);
                    }
                }
            }

            if (isset($this->data['attachments'])) {
                foreach ((array) $this->data['attachments'] ?? [] as $attachment) {
                    if (is_array($attachment)) {
                        $this->addAttachment($attachment['path'], $attachment['name'] ?? '');
                    } else {
                        $this->addAttachment($attachment);
                    }
                }
            }

            $this->Subject = $this->data['subject'];

            return parent::send() == true;
        } catch (\Throwable $error) {

            if (is_dev()) throw $error;

            return false;
        }
    }
}

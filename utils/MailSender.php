<?php

namespace utils;

class MailSender
{
    private $userName = '';
    private $userKey = '';
    private $userMail = '';
    private $mailContent = '';
    private $mailSubject = '';
    private $send_from = '';

    public function __construct(array $mail_settings)
    {
        ini_set("sendmail_from", $mail_settings['send_from']);
        $this->send_from = $mail_settings['send_from'];
    }

    public function setUser(array $user): void
    {
        if (!isset($user['name'], $user['action_key'], $user['email'])) {
            throw new \InvalidArgumentException('name, action_key and email are required to build and send email', 500);
        }
        $this->userName = $user['name'];
        $this->userKey = $user['action_key'];
        $this->userMail = $user['email'];
    }

    public function setMailSubject(string $subject): void
    {
        $this->mailSubject = $subject;
        switch ($this->mailSubject) {
            case 'User Activation':
                $this->mailContent = `
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Activation code</title>
                </head>
                <body>
                    <h1>Hi $this->userName !</h1>
                    <h3>Thank You for joining us</h3>
                    <p>Your activation key is: <b>$this->userKey</b></p>
                </body>
            </html>
            `;
                break;
            case 'Resend User Activation':
                $this->mailContent = `
                <!DOCTYPE html>
                <html lang="en">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Activation code</title>
                    </head>
                    <body>
                        <h1>Hi $this->userName !</h1>
                        <h3>I heard that You didn't get Your activation key.</h3>
                        <p>Here is another one: <b>$this->userKey</b></p>
                    </body>
                </html>
                `;
                break;
            case 'Change email':
                $this->mailContent = `
                    <!DOCTYPE html>
                    <html lang="en">
                        <head>
                            <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Change email</title>
                        </head>
                        <body>
                            <h1>Hi $this->userName !</h1>
                            <h3>I heard that You didn't want to change Your email.</h3>
                            <p>Here is Your action code: <b>$this->userKey</b></p>
                        </body>
                    </html>
                    `;
                break;
            default:
                throw new NoSuchMailSubjectimplementedException();
                break;
        }
    }

    public function send(): void
    {
        if (!mail($this->userMail, $this->mailSubject, $this->mailContent, $this->send_from)) {
            throw new MailServiceNotAvaliableException();
        }
    }
}

class MailServiceNotAvaliableException extends \Exception
{
    public function __construct(string $message = 'Mail service is not avaliable', int $code = 503)
    {
        parent::__construct($message, $code);
        $this->code = $code;
    }
}
class NoSuchMailSubjectimplementedException extends \Exception
{
    public function __construct(string $message = 'No such type of mail implemented', int $code = 501)
    {
        parent::__construct($message, $code);
        $this->code = $code;
    }
}

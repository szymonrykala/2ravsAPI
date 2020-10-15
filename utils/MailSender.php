<?php

class MailSender
{
    private $userName = '';
    private $userKey = '';
    private $userMail = '';
    private $mailContent = '';
    private $mailSubject = '';

    public function __construct()
    {
        ini_set("sendmail_from", SENDER_MAIL);
    }

    public function setUser(array $user): void
    {
        if (!isset($user['name'], $user['action_key'], $user['email'])) {
            throw new Exception('name, action_key and email are required to build and send email', 500);
        }
        $this->userName = $user['name'];
        $this->userKey = $user['action_key'];
        $this->userMail = $user['email'];
    }

    public function setMailSubject(string $subject): void
    {
        $this->mailSubject = $subject;
        switch ($this->mailSubject) {
            case 'User Activation';
                $this->mailContent = "
            <!DOCTYPE html>
            <html lang='en'>
                <head>
                    <meta http-equiv='Content-Type' content='text/html charset=UTF-8' />
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>$this->mailSubject</title>
                </head>
                <body>
                    <h1>Hi $this->userName !</h1>
                    <h3>Thank You for joining us</h3>
                    <p>Your activation key is: <b>$this->userKey</b></p>
                </body>
            </html>
            ";
                break;
            default:
                throw new Exception('No such type of mail implemented', 501);
                break;
        }
    }

    public function send(): bool
    {
        if (mail($this->userMail, $this->mailSubject, $this->mailContent,SENDER_MAIL)) {
            return true;
        }
        return false;
    }
}

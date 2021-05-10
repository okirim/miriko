<?php


namespace App\core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
    public PHPMailer $mail;
    public string $from;
    public string $to;
    public string $subject;
    public string $text;
    public string $view = '';

    public function __construct()
    {
        //Create a new PHPMailer instance
        $this->mail = new PHPMailer();
        //Tell PHPMailer to use SMTP
        $this->mail->isSMTP();
        //Enable SMTP debugging
        //SMTP::DEBUG_OFF = off (for production use)
        //SMTP::DEBUG_CLIENT = client messages
        //SMTP::DEBUG_SERVER = client and server messages
        $this->mail->SMTPDebug = SMTP::DEBUG_OFF;//SMTP::DEBUG_SERVER;

        $this->mail->Host = $_ENV['MAIL_HOST'];

        $this->mail->Port = $_ENV['MAIL_PORT'];

        $this->mail->SMTPAuth = true;

        $this->mail->Username = $_ENV['MAIL_USERNAME'];

        $this->mail->Password = $_ENV['MAIL_PASSWORD'];
    }

    public static function make()
    {
        return new Mail;
    }

    public function view(string $file,array $params=[])
    {
        $this->view= View::renderMassageView($file,$params);
        return $this;
    }

    public function subject(string $subject)
    {
        filter_var($subject, FILTER_SANITIZE_SPECIAL_CHARS);
        $this->subject = $subject;
        return $this;
    }

    public function text(string $text)
    {
        filter_var($text, FILTER_SANITIZE_SPECIAL_CHARS);
        $this->text = $text;
        return $this;
    }

    public function from(string $email)
    {
        filter_var($email, FILTER_VALIDATE_EMAIL);
        $this->from = $email;
        return $this;
    }

    public function to(string $email)
    {
        filter_var($email, FILTER_VALIDATE_EMAIL);
        $this->to = $email;
        return $this;
    }

    public function send()
    {
        $this->mail->setFrom($this->from);
        $this->mail->addAddress($this->to);
        $this->mail->Subject = $this->subject;
        $this->mail->msgHTML($this->view);
        if (!$this->mail->send()) {
            return false;
        } else {
            return true;

        }
    }

}
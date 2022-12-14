<?php namespace Core;

use PHPMailer\PHPMailer\PHPMailer as PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    public static $Error = [];

    public static $address;
    public static $name;
    public static $subject;
    public static $text;
    public static $reply_to;
    public static $bcc;
    public static $attachment;

    public static function setAddress( string $address, $name = null )
    {
        if( !is_null( $name )) {
            self::$name = $name;
        } else {
            self::$name = $address;
        }

        self::$address = $address;
        return new self;
    }

    public static function setSubject( string $subject )
    {
        self::$subject = $subject;
        return new self;
    }

    public static function setAddReplyTo( string $address )
    {
        self::$reply_to = $address;
        return new self;
    }

    public static function setBcc( string $address )
    {
        self::$bcc = $address;
        return new self;
    }

    public static function setBody( string $body )
    {
        self::$text = $body;
        return new self;
    }

    public static function getError()
    {
        return !empty( self::$Error ) ? self::$Error : null;
    }

    public static function send()
    {
        global $app_path, $config;

        $m = new PHPMailer();
        // Debug
        //$m->SMTPDebug = 3;
        //$m->Debugoutput = "html";
        $m->CharSet = "UTF-8";
        $m->SetLanguage("pl", $app_path . "vendor/phpmailer/phpmailer/");
        $m->AddReplyTo($config['smtp_email']);
        $m->From = $config['smtp_email'];
        $m->FromName = $config['smtp_from'];

        if(!empty($config['smtp'])) {
            $m->Host = $config['smtp_host'];
            $m->Port = $config['smtp_port'];

            $m->IsSMTP();
            $m->Username = $config['smtp_username'];
            $m->Password = $config['smtp_password'];
            $m->SMTPAuth = !empty($config['smtp_auth']) ? true : false;
            if (!empty($config['smtp_ssl'])) {
                $m->SMTPSecure = "ssl";
            }
        }

        if (!empty(self::$attachment)) {
            foreach (self::$attachment as $item) {
                $m->AddAttachment($item[0], $item[1]);
            }
        }

        $m->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $m->CharSet = "UTF-8";
        $m->AddReplyTo($config['smtp_email']);
        $m->AddAddress(self::$address, self::$address);
        if (!empty(self::$bcc)) {
            $m->AddBCC(self::$bcc, self::$bcc);
        }
        $m->Subject = self::$subject;

        if (!empty($config['smtp_html'])) {
            $m->AltBody = "Aby obejrze?? t?? wiadomo???? u??yj klienta poczty e-mail obs??uguj??cego format HTML";
            $m->MsgHTML(self::$text);
        } else {
            $m->Body = self::$text;
        }

        $result = $m->send();
        if (!empty($m->ErrorInfo)) {
            self::$Error = $m->ErrorInfo;
        }
        return $result;
    }

    public static function attachment($file)
    {
        if ($file['error'] == 0) {
            self::$attachment[] = array($file['tmp_name'], $file['name']);
        } else {
            self::$Error[] = "nie uda??o si?? za????czy?? pliku do wiadomo??ci " . $file['name'];
        }
    }

    public static function attachment_path($file)
    {
        if (!empty($file)) {
            self::$attachment[] = array($file, $file);
        }
    }
}

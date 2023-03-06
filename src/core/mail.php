<?php namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    public static $Error = [];

    public static $address;
    public static $name;
    public static $subject;
    public static $text;
    public static $replyTo;
    public static $bcc;
    public static $attachment;
    public static $debug = false;
    private static $isHTML = true;

    public static function setHTML( bool $status = true )
    {
        self::$isHTML = $status;
        return new self;   
    }
    public static function setAddress( string $address, string $name = '' )
    {
        self::$name = $name ?? $address;
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
        self::$replyTo = $address;
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
        $m->SMTPDebug = 3;
        $m->Debugoutput = function($str, $level) {
            \Core\Logs::create('mail.smtp.log', gmdate('Y-m-d H:i:s'). "\t$level\t$str\n", FILE_APPEND | LOCK_EX);
        };

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

        if(!empty(self::$isHTML)) {
            $m->AltBody = "Aby obejrzeć tą wiadomość użyj klienta poczty e-mail obsługującego format HTML";
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
            self::$Error[] = "nie udało się załączyć pliku do wiadomości " . $file['name'];
        }
    }

    public static function attachment_path($file)
    {
        if (!empty($file)) {
            self::$attachment[] = array($file, $file);
        }
    }

/**
 * Dodawanie załącznika do wiadomości
 * @param string $filePath is a valid path to file
 * @return self
 */
    public static function addAttachment( string $filePath = '' )
    {
        if( empty( $filePath )) {
            throw new \Exception('File path must not be empty: ' . $filePath);
        }

        if( !is_file( $filePath )) {
            throw new \Exception('You must provide correct path to file not a directory: ' . $filePath);
        }

        self::$attachment[] = array($filePath, basename($filePath));
        return new self;
    }
}

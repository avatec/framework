<?php namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    public static $address;
    public static $name;
    public static $subject;
    public static $text;
    public static $reply_to;
    public static $bcc;
    public static $error;
    public static $attachment;

    public static function send()
    {
        global $app_path, $config;

        //include_once $app_path . "vendor/phpmailer/phpmailer/src/PHPMailer.php";

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
            //include_once $app_path . "vendor/phpmailer/phpmailer/src/SMTP.php";

            $m->Host = $config['smtp_host'];
            $m->Port = $config['smtp_port'];

            $m->IsSMTP();
            $m->Username = $config['smtp_username'];
            $m->Password = $config['smtp_password'];
            if (!empty($config['smtp_auth'])) {
                $m->SMTPAuth = true;
            } else {
                $m->SMTPAuth = false;
            }
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
            $m->AltBody = "Aby obejrzeć tą wiadomość użyj klienta poczty e-mail obsługującego format HTML";
            $m->MsgHTML(self::$text);
        } else {
            $m->Body = self::$text;
        }

        $result = $m->send();
        if (!empty($m->ErrorInfo)) {
            self::$error = $m->ErrorInfo;
        }
        return $result;
    }

    public static function attachment($file)
    {
        if ($file['error'] == 0) {
            self::$attachment[] = array($file['tmp_name'], $file['name']);
        } else {
            self::$error[] = "nie udało się załączyć pliku do wiadomości " . $file['name'];
        }
    }

    public static function attachment_path($file)
    {
        if (!empty($file)) {
            self::$attachment[] = array($file, $file);
        }
    }
}

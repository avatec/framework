<?php

namespace Core;

/**
 *	Klasa obsługuje prezentacje błędów systemu CMS
 */

class Error
{
    public static $name = 'Avatec Framework';
    public static $mode = 'html';

    /**
     *  Wyświetlenie błedu
     *  @param string $title Tytuł
     *  @param string $text Treść błędu
     */

    public static function show($title, $text)
    {
        @ob_get_contents();
        if (self::$mode == 'html') {
            echo '<html><head><meta charset="utf-8"><title>Wystąpił nieoczekiwany błąd</title>';
            echo '<style type="text/css">';
            echo 'html, body { margin: 0; padding: 0; font-family: Sans-serif; width: 100%; height: 100%; background: #efefef;color: #232323; text-align: center; }';
            echo '.main { position: relative; top: 0; left: 0; z-index: 9999;width: 768px; min-height: 100px; padding: 10px; border: 1px solid #cccccc; margin: 5% auto 0 auto; background: #ffffff; color: #222222; word-break: break-all; font-size: 14pt; font-weight: 300; line-height: 150%; }';
            echo '.main h1 { font-size: 1.3em; letter-spacing: 1px; font-weight: 600; color: #a10000; padding-top: 0; margin-top: 0; text-align: center; padding: 10px 10px; }';
            echo '.main p { margin: 0; padding-bottom: 2rem; }';
            echo '</style>';
            echo '</head><body>' . PHP_EOL;
            echo '<div class="main">' . PHP_EOL . '<img src="/templates/website/images/logo-log.png" alt="' . self::$name . '" /><h1>' . $title . '</h1>' . PHP_EOL . '<p>' . $text . '</p></div><br/><br/>' . PHP_EOL;
            echo '<p align="center"><small>' . self::$name . ' - &copy; ' . date('Y') . '</small></p>';
            echo '</body></html>';
        }
        if (self::$mode == 'json') {
            echo json_encode(['error' => true, 'msg' => $title . ': ' . $text]);
        }
        exit;
    }
}

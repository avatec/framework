<?php
namespace Core\Frontend;

use Core\Db;
use Core\Language\LanguageFolderNotFoundException;
use Core\Language\LanguageTransactionNotFoundException;

class Language
{
    private static $folder;
    private static $current;
    private static $available = [];
    private static $dictionary = [];

    public static function setFolder( string $folder )
    {
        if( !\Files::dir_exists( self::$folder )) {
            throw new LanguageFolderNotFoundException;
        }

        self::$folder = $folder;
        return new self;
    }

    public static function setBrowserLanguage(): Language
    {
        $browser_language = explode("-", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $browser_language = strtolower($browser_language[0]);
        return self::set( $browser_language );
    }
    
    public static function set( string $code ): Language
    {
        self::$current = $code;
        return new self;
    }

    public static function add( string $name, string $code )
    {
        if( self::has( $code ) == false ) {
            self::$available[] = ['name' => $name, 'code' => $code];
            return new self;
        }
    }

    private static function has( string $code )
    {
        $index = array_search( $code, array_column( self::$available, 'code' ));
        return ($index >= 0 ? true : false);
    }

    public static function getCurrent()
    {
        return self::$current;
    }

    public static function getTranslation( string $text )
    {
        return self::find( $text );
    }

    private static function find( string $text )
    {
        $result = self::findInDatabase( $text );
        if( $result == false ) {
            $result = self::findInFile( $text );
            if( $result == false ) {
                throw new LanguageTransactionNotFoundException;
            }
        }

        return $result;
    }

    private static function findInDatabase( string $text )
    {
        $result = Db::query("SELECT text FROM core_translations WHERE text='" . $text. "' AND language='" . self::$current . "'");
        if( $result == false ) {
            return false;
        }

        return $result['text'];
    }

    private static function findInFile( string $text )
    {
        $files = scandir( self::$folder );
        if( empty( $files )) {
            return false;
        }

        foreach( $files as $file ) {
            $content = file_get_contents( $file );
            if( $content['text'] === $text ) {
                return $content['text'];
            }
        }

        return false;
    }
}
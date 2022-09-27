<?php

namespace Core;

use Core\Db;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class FilesTokens
{
    const TOKEN_SALT = 'EC+2kaKfruH+qd:k';

    const GOOGLE_VIEWER_URL = 'https://docs.google.com/gview?embedded=true&url=';
    const GOOGLE_VIEWER_TYPES = [
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.text',
        'application/pdf'
    ];

    const MS_VIEWER_URL = 'https://view.officeapps.live.com/op/embed.aspx?src=';
    const MS_VIEWER_TYPES = [
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/msword',
        'application/msexcel',
        'application/vnd.ms-word',
        'application/vnd.ms-excel',
        'application/vnd.ms-office'
    ];

    const IMAGE_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp'
    ];

    public static $table = 'system_files_tokens';
    public static $Error = [];

    public function getPreviewUrl( string $token ): string
    {
        $result = $this->getFileByToken( $token );

        if( in_array( $result['mime'] , self::GOOGLE_VIEWER_TYPES )) {
            return self::GOOGLE_VIEWER_URL . $result['url'] . $result['filename'];
        }

        if( in_array( $result['mime'] , self::MS_VIEWER_TYPES )) {
            return self::MS_VIEWER_URL . $result['url'] . $result['filename'];
        }

        if( in_array( $result['mime'] , self::IMAGE_TYPES )) {
            return '/file/' . $token . '/view';
        }

        throw new Exception('Unsupported file format ' . $result['mime']);
    }

    public function getFileByToken( string $token ): array
    {
        $r = Db::row("path, filename" , self::$table , "WHERE token='" . $token ."'");
        if( empty( $r )) {
            return [];
        }

        global $app_path, $app_url;
        $path = str_replace($app_url, $app_path, $r['path']);

        return [
            'filename' => $r['filename'],
            'url' => $r['path'],
            'path' => $path,
            'mime' => mime_content_type( $path . $r['filename']),
            'create_at' => time()
        ];
    }


    /**
     * Tworzenie nowego tokenu dla wybranego pliku
     * @param string $path
     * @param string $filename
     * @return string
     */

    public function create( string $path, string $filename )
    {
        $token = $this->generate( $path, $filename );
        $result = Db::insert( self::$table , "null,
        '" . $path . "',
        '" . $filename . "',
        '" . $token . "'");

        if(!empty($result)) {
            return $token;
        }

        return '';
    }

    /**
     * Delete file by token
     * @param string $token
     * @return bool
     */

    public function delete( string $token ): bool
    {
        $r = Db::row("path, filename", self::$table, "WHERE token='" . $token . "'");
        if (empty($r)) {
            return false;
        }

        $this->deleteFile($r['path'], $r['filename']);
        $this->deleteFile($r['path'] . 'thumbs/' , $r['filename']);
        Db::delete(self::$table, "token='" . $token . "'");
        return true;
    }

    /**
     * Generowanie tokena
     * @param string $token
     * @param string $filename
     * @return string
     */
    private function generate( string $path, string $filename ): string
    {
        $token = Uuid::uuid5(Uuid::NAMESPACE_DNS, md5($path . $filename . self::TOKEN_SALT . time()));
        if( $this->has( $token )) {
            $this->generate( $path, $filename );
        }

        return $token;
    }

    /**
     * Sprawdzanie czy istnieje wybrany token
     * @param string $token
     * @return bool
     */

    private function has( string $token ): bool
    {
        return Db::check( self::$table , "token='".  $token . "'");
    }

    /**
     * Delete file
     * @param string $path
     * @param string $filename
     * @return bool
     */
    private function deleteFile( string $path, string $filename ): bool
    {
        global $app_url, $app_path;
        $path = str_replace($app_url, $app_path, $path);

        Files::delete( $path . $filename );

        return Db::delete(self::$table, "path='" . $path . "' AND filename='" . $filename . "'");
    }
}

<?php namespace Core\Language;

use Exception;

class LanguageFolderNotFoundException extends Exception
{
    public function errorMessage()
    {
        return 'Folder with translations was not found';
    }
}
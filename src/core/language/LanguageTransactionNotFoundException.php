<?php namespace Core\Language;

use Exception;

class LanguageTransactionNotFoundException extends Exception
{
    public function errorMessage()
    {
        return 'Translation was not found';
    }
}
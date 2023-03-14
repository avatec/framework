<?php namespace Core;

use Smsapi\Client\Feature\Sms\Bag\SendSmsBag;
use Smsapi\Client\Feature\Sms\Data\Sms;
use Smsapi\Client\Curl\SmsapiHttpClient;
use Smsapi\Client\Infrastructure\ResponseMapper\ApiErrorException;

class SmsNotification {

    private $apiToken;
  
    public function __construct($apiToken) 
    {
        $this->apiToken = $apiToken;
    }
  
    public function sendMessage($to, $message) 
    {
        try {
            $sms = (new SmsapiHttpClient())
                ->smsapiPlService($this->apiToken)
                ->smsFeature()
                ->sendSms(SendSmsBag::withMessage($to, $message));
        } catch (SmsapiException $e) {
            throw new \Exception("Error sending SMS: " . $e->getMessage());
            return false;
        }

        return true;
    }
}
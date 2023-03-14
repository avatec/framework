<?php namespace Core;

use SMSApi\Client;
use SMSApi\Api\Sms\Message\Send;
use SMSApi\Exception\SmsapiException;

class SmsNotification {

    private $client;
  
    public function __construct($apiKey) 
    {
        $this->client = new Client($apiKey);
    }
  
    public function sendMessage($to, $message) 
    {
        $sms = new Send();
        $sms->setTo($to);
        $sms->setText($message);
  
        try {
            $result = $this->client->smsSend()->send($sms); 
            return $result->getList()[0]->getPoints();
        } catch (SmsapiException $e) {
            throw new \Exception("Error sending SMS: " . $e->getMessage());
            return false;
        }
    }
}
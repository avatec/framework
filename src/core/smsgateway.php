<?php namespace Core;

use Core\Db;
use SMSGatewayMe\Client\ApiClient;
use SMSGatewayMe\Client\Configuration;
use SMSGatewayMe\Client\Api\MessageApi;
use SMSGatewayMe\Client\Model\SendMessageRequest;

class SMSGateway
{
    public static $table = "sms_logs";

    protected $client;
    protected $config;
    protected $message_client;
    protected $device_id;

    public function __construct()
    {
        global $config;

        self::$table = $config['db_prefix'] . self::$table;

        $this->config = Configuration::getDefaultConfiguration();
        $this->config->setApiKey('Authorization', $config['smsgateway_api_key']);
        $this->client = new ApiClient($this->config);
        $this->message_client = new MessageApi($this->client);
        $this->device_id = $config['smsgateway_device_id'];

        $this->install();
    }

    protected function install()
    {
        if( Db::has_table( self::$table ) == false ) {
            Db::install("CREATE TABLE " . self::$table . " ( `id` INT(11) NOT NULL , `dev_id` INT(11) NOT NULL , `status` VARCHAR(50) NOT NULL , `phone` VARCHAR(50) NOT NULL , `create_at` VARCHAR(50) NOT NULL , `update_at` VARCHAR(50) NOT NULL , `message` TEXT NOT NULL , INDEX (`id`)) ENGINE = InnoDB;");
        }
    }

    public function sendMessage( $phone, $message )
    {
        $result = $this->message_client->sendMessages([
            new SendMessageRequest([
                'phoneNumber' => $phone,
                'message' => $message,
                'deviceId' => $this->device_id
            ])
        ]);

        $r = Db::insert( self::$table , "'" . $result[0]->getId() . "',
        '" . $result[0]->getDeviceId() . "',
        '" . $result[0]->getStatus() . "',
        '" . $phone . "',
        '" . $result[0]->getCreatedAt()->format('Y-m-d H:i:s') . "',
        '" . $result[0]->getUpdatedAt()->format('Y-m-d H:i:s') . "',
        '" . $result[0]->getMessage() . "'");

        if( $r == true ) {
            return true;
        }
    }
}

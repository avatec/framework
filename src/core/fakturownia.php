<?php namespace Core;

use Core\Logs;

/**
 *	Klasa obsługuje połączenie z API fakturownia.pl
 *  @author Grzegorz Miśkiewicz <biuro@avatec.pl>
 *  @version 1.9
 *  @copyright Avatec.pl
 */

class Fakturownia
{
    protected $url = null;
    protected $api_token = null;
    protected $username = null;
    protected $password = null;

    public function __construct()
    {
        global $config;

        if (!empty($config['fakturownia_url'])) {
            $this->url = $config['fakturownia_url'];
        }
        if (!empty($config['fakturownia_api'])) {
            $this->api_token = $config['fakturownia_api'];
        }
        if (!empty($config['fakturownia_user'])) {
            $this->username = $config['fakturownia_user'];
        }
        if (!empty($config['fakturownia_pass'])) {
            $this->password = $config['fakturownia_pass'];
        }
    }

    public function send_invoice( int $id )
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, "https://" . $this->url . "/invoices/" . $id . "/send_by_email.json?api_token=" . $this->api_token);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query([
            'api_token' => $this->api_token
        ]));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($c);
        curl_close($c);

        Logs::create('fakturownia.log', 'Wysyłanie faktury mailem');
        Logs::create('fakturownia.log', "https://" . $this->url . "/invoices/" . $id . "/send_by_email.json?api_token=" . $this->api_token);
        Logs::create('fakturownia.log', $result);

        return json_decode($result);
    }

    public function getDownloadLink( int $id ): string
    {
        return "https://" . $this->url . "/invoices/" . $id . ".pdf?api_token=" . $this->api_token;
    }

    public function getDownloadLinkByToken( string $token ): string
    {
        return "https://" . $this->url . "/invoice/" . $token;
    }

    public function create_invoice($data)
    {
        Logs::create('fakturownia.log' , 'Otrzymano dane do faktury:');
        Logs::create('fakturownia.log' , print_r($data, true));
        $nip = str_replace([' ','-'], ['',''], $data['buyer_tax_no']);
        global $config;

        $json = json_encode([
            'api_token' => $this->api_token,
            'invoice' => [
                'kind' => 'vat',
                'number' => null,
                'sell_date' => date('Y-m-d'),
                'issue_date' => date('Y-m-d'),
                'payment_to' => date('Y-m-d'),
                'seller_name' => $config['fakturownia_name'],
                'seller_tax_no' => $config['fakturownia_nip'],//'8971898947',
                'buyer_name' => $data['buyer_name'],
                'buyer_street' => $data['buyer_street'],
                'buyer_post_code' => $data['buyer_postcode'],
                'buyer_city' => $data['buyer_city'],
                'buyer_tax_no' => $nip,
                'buyer_email' => $data['buyer_email'],
                'positions' => $data['positions'],
                'description' => (!empty($data['description']) ? $data['description'] : null)
            ]
        ], JSON_FORCE_OBJECT);

        $r = $this->request($json);
        Logs::create('fakturownia.log', 'Generowanie faktury');
        Logs::create('fakturownia.log', $json);
        Logs::create('fakturownia.log', print_r($r, true));

        if (!empty($r->id)) {
            $this->send_invoice( $r->id );
        }
        return $r;
    }

    public function request($json)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, "https://" . $this->url . "/invoices.json");

        $head[] = 'Accept: application/json';
        $head[] = 'Content-Type: application/json';

        curl_setopt($c, CURLOPT_HTTPHEADER, $head);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, $json);

        $result = curl_exec($c);
        curl_close($c);

        return json_decode($result);
    }
}

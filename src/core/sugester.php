<?php
namespace Core;

class Sugester
{
    // Ustawienia > Api > Kod autoryzacyjny
    protected $token;
    protected $url;

    public function __construct()
    {
        $this->token = "6mrIR8CTxNhHffk0qC0/operators";
        $this->url = 'https://panel.benefitprawny.pl/';
    }


    private function call( $url, $data )
    {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL , $url );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt( $ch, CURLOPT_HEADER, true);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));
        if(curl_errno($ch)){
            die('Curl error: ' . curl_error($ch));
        }
        $result = curl_exec( $ch );
        curl_close( $ch );

        if(!empty( $result['status'] )) {
            throw new Exception( $result['error'] );
        }

        return $result;
    }


    public function addClient( $data )
    {
        $result = $this->call( $this->url . 'app/clients.json' , [
            "api_token" => $this->token,
            "client" => $data
        ]);

        if(!empty( $result )) {
            $result = json_decode( $result, true );
        }

        if(!empty( $result['id'] )) {
            return $result['id'];
        }

        if(!empty( $result['error'] )) {
            die( $result['error']);
        }
    }

    public function addAccount( $data, $client, $prefix = 'Orlen' )
    {
        $result = $this->call( $this->url . 'app/account.json' , [
            "account" => [
                "prefix" => $prefix,
                "initial_module" => "crm",
                "from_partner" => "biuro@operators.com.pl"
            ],
            "user" => $data,
            "client" => $client
        ]);

        if(!empty( $result )) {
            $result = json_decode( $result, true );
        }

        if(!empty( $result['id'] )) {
            return $result['id'];
        }
    }
}
//
// $sugester = new \Core\Sugester();
// // Tworzymy klienta
// // $contact_id = $sugester->addClient([
// //     "name" => "_Testowy Klient API PHP_",
// //     //"first_name" => "Grzegorz",
// //     //"last_name" => "TEST",
// //     "email" => "testowy@emailexample1.net",
// //     //"phone" => "zzzmmm@@@123",
// //     //"string1" => "Hasieło",
// //     "note" => "orlen1 test"
// // ]);
//
// // Tworzymy użytkownika
// $user_id = $sugester->addAccount([
//     "login" => "test_test",
//     "email" => "testowy@emailexample1.net",
//     "password" => "ZaQa1@31@#1f"
// ]);
//
// print_r($user_id);

<?php

namespace App\Services\Qiwi;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class Qiwi
{
    use QiwiAuth, QiwiReports;

    protected $login;
    protected $password;
    protected $loggedIn;
    protected $cookieJar;
    protected $client;

    public function __construct($login, $password, $proxy = null)
    {
        $this->login = $login;
        $this->password = $password;
        $this->loggedIn = false;
        $this->cookieJar = new CookieJar(false, [
            [
                'Name' => 'new_payment_history',
                'Value' => '0',
                'Domain' => 'qiwi.com',
            ]
        ]);

        $this->client = new Client([
            'cookies' => $this->cookieJar,
            'proxy' => $proxy,
        ]);
    }

    public function getPersonState()
    {
        $this->login();
        
        $response = $this->client->post('https://qiwi.com/person/state.action', [
                'Host' => 'qiwi.com',
                'Origin' => 'https://qiwi.com',
                'X-Requested-With' => 'XMLHttpRequest',
                'Referer' => 'https://qiwi.com/main.action',
            ]);
        
        return json_decode($response->getBody()->getContents());
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->getPersonState()->data->balances;
    }
}

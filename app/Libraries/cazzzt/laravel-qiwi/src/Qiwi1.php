<?php

namespace Cazzzt\Qiwi;

use DOMDocument;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Symfony\Component\DomCrawler\Crawler;

class Qiwi1
{
    protected $login;
    protected $password;
    protected $client;
    protected $browser;
    protected $auth_ticket;
    protected $logged_in;

    /**
     * @param $login
     * @param $password
     */
    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
        $this->browser = new Browser();
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36'
            ],
        ]);

   }


    public function loginInBrowser($browser, $url)
    {
       $browser->visit('https://qiwi.com')
            ->waitFor('.header-unauthorized-nav-item')
            ->click('.brand-button')
            ->type('.phone-input-form-field-input-control-text', $this->login)
            ->type('.password-input-form-field-input-control', $this->password)
            ->click('.auth-submit button');
    }

    public function login()
    {
        $this->client->request('GET', 'https://qiwi.com');
        $this->client->request('GET', 'https://sso.qiwi.com/app/proxy?v=1', [
            'headers' => [
                'Referer' => 'https://qiwi.com',
            ]
        ]);

        $this->client->request('GET', 'https://sso.qiwi.com/signin/oauth2', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Referer' => 'https://qiwi.com',
            ]
        ]);

        $this->saveState();


        $this->auth();

        $this->updateLoginStatus();

        return $this->logged_in;
    }

    /**
     * Обновить статус входа в систему.
     * @return bool
     */
    private function updateLoginStatus(){

        $this->logged_in = false;

        $response = $this->client->request('GET', 'https://qiwi.com/main.action', [
            'headers' => [
                'Refferer' => 'https://qiwi.com',
            ],
        ])->getBody()->getContents(); 

       dd( $response);
        $phone = trim($obj->plaintext);
        if(strcmp($phone, $this->id) == 0){
            $this->logged_in = true;
        }else{
            return false;
        }

        return true;
    }

    public function auth()
    {
        $headers = [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Accept' => 'application/vnd.qiwi.sso-v1+json',
            'Accept-Language' => 'ru;q=0.8,en-US;q=0.6,en;q=0.4',
            'Host' => 'sso.qiwi.com',
            'Referer' => 'https://sso.qiwi.com/app/proxy?v=1'
        ];

        $tgt = $this->getTGT($headers);

        $authTicket = $this->getSt($headers, $tgt);

        $this->checkAuth($authTicket);

        return $authTicket;
    }

    protected function saveState()
    {
        $headers = array(
            'Accept' => '*/*',
            'Accept-Encoding' => 'gzip, deflate',
            'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
            'Connection' => 'keep-alive',
            'Content-type' => 'application/x-www-form-urlencoded',
            'Host' => 'statistic.qiwi.com',
            'Origin' => 'https://qiwi.com',
        );

        $my_ip = $this->myIp();


        $response = $this->client->request('POST', 'https://statistic.qiwi.com/rest/statistic/qw/site/save', [
            'headers' => [
                'referer' => 'https://qiwi.com',
            ],
            'json' => [
                'v' => '1',
                '_v' => 'j41',
                'a' => '474145743',
                't' => 'event',
                'ni' => '0',
                '_s' => '7',
                'dl' => 'https://qiwi.com',
                'ul' => 'ru',
                'de' => 'UTF-8',
                'dt' => 'QIWI%20(%D0%9A%D0%B8%D0%B2%D0%B8)%20-%20%D1%8D%D0%BB%D0%B5%D0%BA%D1%82%D1%80%D0%BE%D0%BD%D0%BD%D0%B0%D1%8F%20%D0%BF%D0%BB%D0%B0%D1%82%D0%B5%D0%B6%D0%BD%D0%B0%D1%8F%20%D1%81%D0%B8%D1%81%D1%82%D0%B5%D0%BC%D0%B0%20%D1%87%D0%B5%D1%80%D0%B5%D0%B7%20%D0%B8%D0%BD%D1%82%D0%B5%D1%80%D0%BD%D0%B5%D1%82%20-%20%D1%81%D0%BE%D0%B7%D0%B4%D0%B0%D1%82%D1%8C%20%D1%81%D0%B2%D0%BE%D0%B9%20%D0%BA%D0%B8%D0%B2%D0%B8%20%D0%BA%D0%BE%D1%88%D0%B5%D0%BB%D0%B5%D0%BA%20%D0%BE%D0%BD%D0%BB%D0%B0%D0%B9%D0%BD',
                'sd' => '24-bit',
                'sr' => '1440x900',
                'vp' => '1440x778',
                'je' => '0',
                'fl' => '21.0%20r0',
                'ec' => 'PersonLoginForm',
                'ea' => 'Login',
                'el' => 'url%3A%20%2F',
                '_u' => 'SDCAiEALD~',
                'jid' => '',
                'cid' => '66635019.1428055349',
                'tid' => 'UA-5597139-18',
                'gtm' => 'GTM-W4FJZS',
                'cd4' => '66635019.1428055349',
                'cd5' => '',
                'cd7' => '',
                'cd8' => '1',
                'cd9' => '1',
                'cd10' => 'Mozilla%2F5.0%20(Macintosh%3B%20Intel%20Mac%20OS%20X%2010_11_3)%20AppleWebKit%2F537.36%20(KHTML%2C%20like%20Gecko)%20Chrome%2F48.0.2564.116%20Safari%2F537.36',
                'cd201' => $my_ip,
                'z' => '1152385182',
                'qw_ip' => $my_ip,
                'qw_phone' => '',
            ],
        ]);

        return $response->getStatusCode() == 200;
    }

    protected function myIp()
    {
        $content = $this->client->request('GET', "http://myip.ru/index_small.php")->getBody()->getContents();

        if (preg_match("#<tr><td>([0-9]+)\\.([0-9]+)\\.([0-9]+)\\.([0-9]+)</td></tr>#", $content, $m)) {
            return "{$m[1]}.{$m[2]}.{$m[3]}.{$m[4]}";
        }

        return false;
    }

    public function checkAuth($ticket)
    {
        $response = $this->client->request('GET', 'https://qiwi.com/j_spring_cas_security_check', [
            'headers' => [
                'Host' => 'qiwi.com',
                'X-Requested-With' => 'XMLHttpRequest',
                'Referer' => 'https://qiwi.com/main.action',
            ],
            'query' => [
                'ticket' => $ticket,
            ],
        ]);

        return $response->getStatusCode() == 200;
    }

    public function getTGT($headers)
    {

        $response = $this->client->request('POST', 'https://sso.qiwi.com/cas/tgts', [
            'headers' => $headers,
            'json' => [
                'login' => $this->login,
                'password' => $this->password,
            ]
        ]);

        return json_decode($response->getBody()->getContents())->entity->ticket;
    }

    public function getSt($headers, $ticket)
    {
        $headers['Accept'] = 'application/json, text/javascript, */*; q=0.01';
        $headers['Origin'] = 'https://sso.qiwi.com';
        $response = $this->client->request('POST', 'https://sso.qiwi.com/cas/sts', [
            'headers' => $headers,
            'json'  =>  [
                'service'  => 'https://qiwi.com/j_spring_cas_security_check',
                'ticket'  =>  $ticket,
            ]
        ]);

        return json_decode($response->getBody()->getContents())->entity->ticket;
    }

    /**
     * Получить баланс кошелька
     * @return bool|string
     */
    public function loadBalance(){
        $data = $this->getWalletState('https://qiwi.com/main.action');
            
        return $data['data']['balances'];
    }

    /**
     * Получить состояние кошелька
     * @param $ref
     * @return bool|mixed
     */
    private function getWalletState($refferer){

        $response = $this->client->request('POST', 'https://qiwi.com/person/state.action', [
            'Host' => 'qiwi.com',
            'Origin' => 'https://qiwi.com',
            'X-Requested-With' => 'XMLHttpRequest',
            'Referer' => $refferer,
        ])->getBody()->getContents();

        dd($response);

        try{
            return json_decode($content, true);
        }catch(Exception $e){
            $this->lastErrorStr = $e;
            $this->lastErrorNo = 1060;
        }
        return false;
    }


    public function getHistory()
    {
               // $url = 'https://qiwi.com/report/list.action?daterange=true&start=08.07.2017&finish=08.08.2017';

        // $response = $this->browser->browse(function ($bro) use ($url) {
        //     $this->loginInBrowser($bro, $url);

        //     return $bro->visit($url)
        //         ->clickLink('Вернуться к старому дизайну ⟶')
        //         ->visit($url)
        //         ->waitFor('.reports')
        //         ->text();
        // });
        $this->login();

        // $cookieJar = CookieJar::fromArray([
        //     'new_payment_history' => '0'
        // ], 'qiwi.com');

        // dd($cookieJar);


        // $cookieJar = new CookieJar();

        // $cookieJar->setCookie("new_payment_history=0; Domain=qiwi.com");

        // $cookie[] = SetCookie::fromString("new_payment_history=0; Domain=qiwi.com");

        // $cookie[] = SetCookie::fromString("CASTG={$this->login()}; Domain=sso.qiwi.com");

        $jar = new CookieJar();

        // $cookieJar = $jar->fromArray([
        //     'new_payment_history' => '0'
        // ], 'qiwi.com');

        // dd($cookieJar);

        $cookieJar = CookieJar::fromArray([
            'CASTGC' => $this->login()
        ], 'sso.qiwi.com');

        // $response = $this->client
        //     ->request('GET', 'https://qiwi.com/settings.action', [
        //         'cookies' => $cookieJar,
        //     ])->getBody()->__toString();

        $response = $this->client
            ->request('GET', 'https://qiwi.com/settings.action', [
                'cookies' => $cookieJar,
            ])->getBody()->__toString();


            dd($response);

            $crawler = new Crawler($response);

            dd(trim($crawler->filter('.reports')->first()->text()));

            $nodeValues = $crawler->filter('.reportsLine')->each(function (Crawler $node, $i) {
                return $node->text();
            });

        dd($nodeValues);
        $ticket = $this->auth();
        $phone = substr($this->login, 1, 11);
        $url = "https://edge.qiwi.com/payment-history/v1/persons/{$phone}/payments?rows=10";

        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Host' => 'edge.qiwi.com',
                'Authorization' => "Token {$ticket}",
                'Origin' => 'https://qiwi.com',
                'Referer' => 'https://qiwi.com/payment/history'
            ],
        ]);

        dd($response);
    }
}

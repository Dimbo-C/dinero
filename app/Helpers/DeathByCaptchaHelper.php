<?php

namespace App\Helpers;

use DeathByCaptcha_HttpClient;
use GuzzleHttp;

class DeathByCaptchaHelper {

    public function __construct() {

    }

    static function execute() {
        $client = new DeathByCaptcha_HttpClient(
                env("DEATHBYCAPTCHA_USERNAME"),
                env("DEATHBYCAPTCHA_PASSWORD")
        );

        $html = self::getContent();
        $sitekey = self::getSitekey($html);
        $extra = self::getExtra($sitekey);

        $client->is_verbose = true;
        echo "Sitekey is $sitekey";
        echo "<br>";
        echo "Your balance is {$client->balance} US cents\n";

        dump($extra);


        // Put null the first parameter and add the extra payload
        if ($captcha = $client->decode(null, $extra)) {
            echo "CAPTCHA {$captcha['captcha']} uploaded\n";

            sleep(30);
            //        sleep(DeathByCaptcha_Client::DEFAULT_TIMEOUT);
            // Poll for CAPTCHA indexes:
            if ($text = $client->get_text($captcha['captcha'])) {
                echo "CAPTCHA {$captcha['captcha']} solved: {$text}\n";

                // Report an incorrectly solved CAPTCHA.
                // Make sure the CAPTCHA was in fact incorrectly solved!
                //$client->report($captcha['captcha']);
            }
        } else {
            echo "CAPTCHA was not uploaded<br>";
        }
    }

    static function getSitekey($html) {
        preg_match("/sitekey:\"(.+?)\"/", $html, $matches);

        return $matches[1];
    }

    private static function getContent() {
        $client = new GuzzleHttp\Client();
        $res = $client->request("GET", "https://qiwi.com");

        return $res->getBody()->getContents();
    }

    private static function getExtra($sitekey) {
        $data = array(
                'proxy' => 'http://user6760:0ajq7n@5.8.66.122:8239',
                'proxytype' => 'HTTP',
            //        'googlekey' => '6Lc2fhwTAAAAAGatXTzFYfvlQMI2T7B6ji8UVV_b',
                'googlekey' => $sitekey,
                'pageurl' => 'https://qiwi.com/');
        //Create a json string
        $json = json_encode($data);

        //Put the type and the json payload
        $extra = [
                'username' => env("DEATHBYCAPTCHA_USERNAME"),
                'password' => env("DEATHBYCAPTCHA_PASSWORD"),
                'type' => 4,
                'token_params' => $json,  # banner img
        ];

        return $extra;
    }


}
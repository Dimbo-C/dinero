<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'WelcomeController@show');

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');

Route::get('/aliexpress', 'Admins\OwnAdminsController@all');
Route::get('/gross-indicators', 'Admins\GrossIndicatorsController@all');


Route::get("/test", function () {
    $client = new GuzzleHttp\Client();
    $res = $client->request("GET", "https://qiwi.com");
    $content = $res->getBody()->getContents();

    preg_match("/sitekey:\"(.+?)\"/", $content, $matches);
    $sitekey = $matches[1]; // text like "6LfjX_4SAAAAAFfINkDklY_r2Q5BRiEqmLjs4UAC"

    $client = new DeathByCaptcha_HttpClient(
        env("DEATHBYCAPTCHA_USERNAME"),
        env("DEATHBYCAPTCHA_PASSWORD")
    );

    $client->is_verbose = true;
    echo "Sitekey is $sitekey";
    echo "<br>";
    echo "Your balance is {$client->balance} US cents\n";

    $data = array(
        'proxy' => 'http://validUser:validPass@validHost:validPort',
        'proxytype' => 'HTTP',
//        'googlekey' => '6Lc2fhwTAAAAAGatXTzFYfvlQMI2T7B6ji8UVV_b',
        'googlekey' => $sitekey,
        'pageurl' => 'https://qiwi.com/');
//Create a json string
    $json = json_encode($data);

//Put the type and the json payload
    $extra = [
        'type' => 4,
        'token_params' => $json,  # banner img
    ];

// Put null the first parameter and add the extra payload
//    dd(['result' => $client->decode(null, $extra)]);
    if ($captcha = $client->decode(null, $extra)) {
        echo "CAPTCHA {$captcha['captcha']} uploaded\n";

        sleep(30);
//        sleep(DeathByCaptcha_Client::DEFAULT_TIMEOUT);
//        dd($client->get_text($captcha['captcha']));
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
//    echo $res->getBody()->getContents();

//    $control = \App\Helpers\QiwiGeneralHelper::getQiwiControlObject("380960968460");
//    dd(['card provider' => $control->detectCardProvider("5168742207673892")]);

//    dd(json_decode($json));
//    file_put_contents()
//    dump()
//    $money = 361.1;
//    $percent = 2;
//    dd(\App\Helpers\MoneyHelper::getBaseCost($money, $percent, 50));
//    $login = "380960968460";
//
//    dispatch(new UpdateBalanceJob($login));
});

Route::get('/{all}', 'HomeController@show')
    ->where('slug', '!=', '/')
    ->where('slug', '!=', 'login')
    ->where('all', '!=', 'logout')
    ->where('all', '!=', 'register')
    ->where(['all' => '.*']);


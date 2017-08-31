<?php

namespace Cazzzt\Qiwi;

trait QiwiAuth
{
	/**
     * Должна вызываться каждый раз чтобы войти в систему
     * @return bool
     */
    public function login() {

        $this->updateLoginStatus();


        if($this->logged_in){
            return true;
        }

        $this->client->request('GET', 'https://qiwi.com');

        $this->client->request('GET', 'https://sso.qiwi.com/app/proxy?v=1', [
        	'headers' => [
        		'Referer' => 'https://qiwi.com',
        	],
        ]);

        $this->client->request('GET', 'https://sso.qiwi.com/signin/oauth2', [
        	'headers' => [
        		'Content-Type' => 'application/json',
        		'Referer' => 'https://qiwi.com',
        	],
        ]);


        if (!$this->doTGTS('GET', false, [
            'Content-Type' => 'application/json'
        ], "401|201")) {
            return false;
        }

        $this->saveState();

        $this->doTGTS(USERAGENT_METHOD_OPTIONS, false, [
            'Access-Control-Request-Method' => 'POST',
            'Access-Control-Request-Headers' => 'content-type',
            'Content-Type' => 'application/json; charset=UTF-8',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        ]);

        //Отправляем информацию о логине
        $loginParams = array(
            "login" => $this->login,
            "password" => $this->password
        );

        $post_data = json_encode($loginParams);

        if(!($authRet = $this->doTGTS(USERAGENT_METHOD_POST, $post_data, [
            'Accept' => 'application/vnd.qiwi.sso-v1+json',
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Length' => strlen($post_data),
        ], 201))){
            $this->lastErrorStr = "Failed to get login session";
            return false;
        }
        $authRet = json_decode($authRet, true);
        if(isset($authRet['entity']['error']['message'])){
            $this->trace("[QIWI] Error: " . $authRet['entity']['error']['message']);
            return false;
        }
        $this->trace("[QIWI] Security ticked received.");
        if(!isset($authRet['entity']['ticket'])){
            $this->lastErrorStr = $authRet['message'];
            $this->lastErrorNo = $authRet['code'];
            return false;
        }
        if(!isset($authRet['entity']['ticket'])){
            $this->lastErrorStr = "Invalid TGTS response ticket format";
            return false;
        }
        $this->auth_ticket = $authRet['entity']['ticket'];
        $stsParams = array(
            "service" => QIWI_URL_SECURITY_CHECK,
            "ticket" => $this->auth_ticket
        );
        $post_data = json_encode($stsParams);
        if(!($authRet = $this->doSTS(USERAGENT_METHOD_POST, $post_data))){
            $this->lastErrorStr = "Failed to get STS data";
            return false;
        }
        $authRet = json_decode($authRet, true);
        if(!isset($authRet['entity']['ticket'])){
            $this->lastErrorStr = "Invalid STS response format";
            return false;
        }
        $this->auth_ticket = $authRet['entity']['ticket'];
        $this->trace("[QIWI] Sending ticket to QIWI server...");

        if(!$this->doSTS(USERAGENT_METHOD_OPTIONS, false, [
            'Access-Control-Request-Headers' => 'accept, accept-language, content-type',
            'Access-Control-Request-Method' => 'POST'])){
            $this->lastErrorStr = "Failed to send options to STS";
            return false;
        }
        if(!($authRet = $this->doSTS(USERAGENT_METHOD_POST, $post_data))){
            $this->lastErrorStr = "Failed to get STS data";
            return false;
        }
        $authRet = json_decode($authRet, true);
        if(!isset($authRet['entity']['ticket'])){
            $this->lastErrorStr = "Invalid STS response format";
            return false;
        }
        $this->trace("[QIWI] Updating security ticket...");
        $this->auth_ticket = $authRet['entity']['ticket'];

        $this->ua->request(USERAGENT_METHOD_GET, QIWI_URL_SECURITY_CHECK . "?ticket=" . $this->auth_ticket, QIWI_URL_MAIN, false, [
            'Host' => 'qiwi.com',
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        if($this->ua->getStatus() !== 200){
            $this->lastErrorStr = "Failed to do security check. Status=" . $this->ua->getStatus() . ", expected=200";
            return false;
        }
        $this->updateLoginStatus();

        if($this->logged_in) {
            $this->trace("[QIWI] Login [$this->id] was successful.");
        }else{
            $this->trace("[QIWI] Login [$this->id] failed.");
        }

        return $this->logged_in;
	}

	/**
     * Обновить статус входа в систему.
     * @return bool
     */
    protected function updateLoginStatus(){
        $this->trace("[QIWI] Updating login status...");

        $this->logged_in = false;

        $content = $this->ua->request(USERAGENT_METHOD_GET, QIWI_URL_MAINACTION, QIWI_URL_MAIN);
        if(!$content){
            return false;
        }

        if(!($dom = str_get_html($content))){
            $this->lastErrorStr = "Failed to build DOM from content of " . QIWI_URL_MAINACTION;
            return false;
        }

        if(!($obj = $dom->find("div[class=auth_login] div[class=phone]", 0))){
            return false;
        }
        $phone = trim($obj->plaintext);
        if(strcmp($phone, $this->id) == 0){
            $this->logged_in = true;
        }else{
            return false;
        }

        return true;
    }

     /**
     * Отпарвить TGTS запрос
     * @param int $method Метод GET, POST или OPTIONS
     * @param bool|string $post_data Данные для отправки в случае POST или OPTIONS
     * @param array $a_headers Дополнительные заголовки
     * @param int $correct_status Ожидаемый статус
     * @return bool|string
     */
    private function doTGTS($method, $post_data = false, $a_headers = [], $correct_status = 200){
        $headers = array(
            'Host' => 'sso.qiwi.com',
            'Origin' => 'https://qiwi.com',
            'Accept' => 'application/vnd.qiwi.sso-v1+json',
        );
        if($a_headers) {
            foreach ($a_headers as $k => $v) {
                $headers[$k] = $v;
            }
        }

        $correct_statuses = preg_split("/\\|/", $correct_status);

        $content = $this->ua->request($method, "https://sso.qiwi.com/cas/tgts", "https://sso.qiwi.com/app/proxy?v=1",
            $post_data, $headers);

        if(!in_array($this->ua->getStatus(), $correct_statuses)){
            $this->trace("[TGTS] ERROR: Expected status=$correct_status, returned status=" . $this->ua->getStatus());
            return false;
        }

        if($content) {
            try {
                if($data = json_decode($content, true)){
                    if(isset($data['entity']['ticket'])){
                        $this->auth_ticket = $data['entity']['ticket'];
                        $this->trace("[TGTS] Security ticket updated: {$this->auth_ticket}");
                    }
                }

            } catch (Exception $e) {
            }
        }

        return $content;
    }

    private function updateTGTSTicket(){
        return $this->doTGTS(USERAGENT_METHOD_GET, false, [
            'Content-Type' => 'application/json'
        ], 201);
    }

    private function doSTS($method, $post_data=false, $a_headers=[], $expected_status=200){
        $headers = array(
            'Accept' => 'application/vnd.qiwi.sso-v1+json',
            'Content-Type' => 'application/json',
            'Host' => 'sso.qiwi.com',
            'Origin' => 'https://sso.qiwi.com',
        );
        if($a_headers) {
            foreach ($a_headers as $k => $v) {
                $headers[$k] = $v;
            }
        }
        $content = $this->ua->request($method, 'https://sso.qiwi.com/cas/sts', 'https://sso.qiwi.com/app/proxy?v=1',
            $post_data, $headers);
        if($this->ua->getStatus() !== $expected_status){
            $this->trace("[STS] Expected status $expected_status, but returned status is " . $this->ua->getStatus());
            return false;
        }
        if(empty($content)){
            return true;
        }
        if($content) {
            try {
                if($data = json_decode($content, true)){
                    if(isset($data['entity']['ticket'])){
                        $this->sts_auth_ticket = $data['entity']['ticket'];
                        $this->trace("[STS] Security ticket updated: {$this->sts_auth_ticket}");
                    }
                }

            } catch (Exception $e) {
            }
        }
        return $content;
    }

    /**
     * Обновить защитный ключ STS
     * @return bool
     */
    private function updateSTSTicket(){
        if(!$this->doSTS(USERAGENT_METHOD_OPTIONS, false, [])){
            return false;
        }
        $stsParams = array(
            "ticket" => $this->auth_ticket,
            "service" => QIWI_URL_MAIN . "/j_spring_cas_security_check"
        );
        $post_data = json_encode($stsParams);
        if(!$this->doSTS(USERAGENT_METHOD_POST, $post_data, [
            'Content-Type' => 'application/json'
        ])){
            return false;
        }
        return true;
    }

    private function saveState(){
        $headers = array(
            'Accept' => '*/*',
            'Accept-Encoding' => 'gzip, deflate',
            'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
            'Connection' => 'keep-alive',
            'Content-type' => 'application/x-www-form-urlencoded',
            'Host' => 'statistic.qiwi.com',
            'Origin' => QIWI_URL_MAIN,
        );
        $myip = $this->ua->getMyIP();
        $post_data = 'v=1&_v=j41&a=474145743&t=event&ni=0&_s=7&dl=https%3A%2F%2F'.QIWI_HOST.'%2F&ul=ru&de=UTF-8&' .
            'dt=QIWI%20(%D0%9A%D0%B8%D0%B2%D0%B8)%20-%20%D1%8D%D0%BB%D0%B5%D0%BA%D1%82%D1%80%D0%BE%D0%BD%D0%BD%D0%B0%D1%8F%20%D0%BF%D0%BB%D0%B0%D1%82%D0%B5%D0%B6%D0%BD%D0%B0%D1%8F%20%D1%81%D0%B8%D1%81%D1%82%D0%B5%D0%BC%D0%B0%20%D1%87%D0%B5%D1%80%D0%B5%D0%B7%20%D0%B8%D0%BD%D1%82%D0%B5%D1%80%D0%BD%D0%B5%D1%82%20-%20%D1%81%D0%BE%D0%B7%D0%B4%D0%B0%D1%82%D1%8C%20%D1%81%D0%B2%D0%BE%D0%B9%20%D0%BA%D0%B8%D0%B2%D0%B8%20%D0%BA%D0%BE%D1%88%D0%B5%D0%BB%D0%B5%D0%BA%20%D0%BE%D0%BD%D0%BB%D0%B0%D0%B9%D0%BD' .
            '&sd=24-bit&sr=1440x900&vp=1440x778&je=0&fl=21.0%20r0&ec=PersonLoginForm&ea=Login&el=url%3A%20%2F&_u=SDCAiEALD~' .
            '&jid=&cid=66635019.1428055349&tid=UA-5597139-18&gtm=GTM-W4FJZS&cd4=66635019.1428055349&cd5=&cd7=&cd8=1&cd9=1' .
            '&cd10=Mozilla%2F5.0%20(Macintosh%3B%20Intel%20Mac%20OS%20X%2010_11_3)%20AppleWebKit%2F537.36%20(KHTML%2C%20like%20Gecko)%20Chrome%2F48.0.2564.116%20Safari%2F537.36' .
            '&cd201=' . $myip .
            '&z=1152385182' .
            '&qw_ip=' . $myip .
            '&qw_phone=';
        $this->ua->request(USERAGENT_METHOD_POST, "https://statistic.qiwi.com/rest/statistic/qw/site/save",
            QIWI_URL_MAIN, $post_data, $headers);
        return $this->ua->getStatus() == 200;
    }
}
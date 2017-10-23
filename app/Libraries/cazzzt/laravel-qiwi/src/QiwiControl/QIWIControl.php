<?php

namespace App\Cazzzt\Qiwi\QiwiControl;

use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
if (PHP_VERSION_ID < 50300) {
    die("PHP >= 5.3 REQUIRED to run QIWIControl");
}

require_once(__DIR__ . DIRECTORY_SEPARATOR . "simple_html_dom.php");
require_once(__DIR__ . DIRECTORY_SEPARATOR . "UserAgent2.php");
ini_set('memory_limit', '1024M');
define('QIWI_HOST', "qiwi.com");
define('QIWI_HOST_REST', "edge.qiwi.com");
define('QIWI_URL_MAIN', "https://" . QIWI_HOST);
define('QIWI_URL_REST', "https://" . QIWI_HOST_REST);
define('QIWI_URL_MAINACTION', QIWI_URL_MAIN . "/main.action");
define('QIWI_URL_REPORTACTION', QIWI_URL_MAIN . "/report.action");
define('QIWI_URL_HISTORY_LAST_WEEK', QIWI_URL_MAIN . "/report/list.action?type=3");
define('QIWI_URL_HISTORY_TODAY', QIWI_URL_MAIN . "/report/list.action?type=1");
define('QIWI_URL_HISTORY_YESTERDAY', QIWI_URL_MAIN . "/report/list.action?type=2");
define('QIWI_URL_HISTORY_DATERANGE', QIWI_URL_MAIN . "/report/list.action");
define('QIWI_URL_BILLS', QIWI_URL_MAIN . "/order/list.action");
define('QIWI_URL_CREATE_BILL', QIWI_URL_MAIN . "/user/order/create.action");
define('QIWI_URL_FORM_BILL', QIWI_URL_MAIN . "/order/form.action");
define('QIWI_URL_SECURITY_CHECK', QIWI_URL_MAIN . "/j_spring_cas_security_check");
define('QIWI_STS', "sts");
define('QIWI_STATUS_SUCCESS', "status_SUCCESS");
define('QIWI_STATUS_ERROR', "status_ERROR");
define('QIWI_STATUS_PROCESSED', "status_PROCESSED");
define('QIWI_STATUS_PAID', "status_PAID");
define('QIWI_STATUS_CANCELED', "status_CANCELED");
define('QIWI_STATUS_AWAITING_CONFIRM', "status_AWAITING_CONFIRM");
define('QIWI_STATUS_NOT_PAID', "status_NOT_PAID");
define('QIWI_BILLS_MODE_IN', 1);
define('QIWI_BILLS_MODE_OUT', 2);
define('QIWI_BILLS_MODE_INOUT', 3);
define('QIWI_SETTINGS_VERSION', "3.6.0");
define('QIWI_CURRENCY_RUB', "643");
define('QIWI_CURRENCY_USD', "840");
define('QIWI_CURRENCY_EUR', "978");
define('QIWI_CURRENCY_KAZ', "398");
//Security settings
define('QIWI_SECURITY_SMS_CONFIRMATION', 'SMS_CONFIRMATION');
define('QIWI_SECURITY_EMAIL', 'EMAIL');
define('QIWI_SECURITY_TOKEN', 'TOKEN');
define('QIWI_SECURITY_PIN', 'PIN');
define('QIWI_SECURITY_SMS_PAYMENT', 'SMS_PAYMENT');
define('QIWI_SECURITY_CALL_CONFIRMATION', 'CALL_CONFIRMATION');

// codes for providers
define("QIWI_PROVIDER_QIWI_WALLET", 99);
define("QIWI_PROVIDER_EMAIL_VOUCHER", 22496);

class QIWIControl {
    private $id;
    private $password;
    private $auth_ticket;
    private $sts_auth_ticket;
    private $auth_links;
    private $logged_in;
    private $debug;
    private $cookie_file;
    private $proxy;
    private $proxyAuth;
    private $lastErrorNo;
    private $lastErrorStr;
    private $ua;
    private $responseData;
    public $debugData;

    /**
     * Создает экземпляр класса
     * @param string $id Ваш номер телефона в формате +79686661111
     * @param string $password Пароль
     * @param string $cookie_dir Каталог с кукисами
     * @param bool|string $proxy Прокси ip:port
     * @param bool|string $proxyAuth Авторизация прокси login:password
     * @param bool|false $debug_mode
     */
    function __construct($id, $password, $cookie_dir = "cookie_data", $proxy = false, $proxyAuth = false, $debug_mode = false) {
        $this->id = (strpos($id, "+") === false) ? "+$id" : $id;
        $this->password = $password;
        $this->auth_ticket = false;
        $this->sts_auth_ticket = false;
        $this->auth_links = false;
        $this->logged_in = false;
        $this->debug = $debug_mode;
        $this->proxy = $proxy;
        $this->proxyAuth = $proxyAuth;
        if (preg_match("/([0-9]+)/", $id, $m)) {
            if (!file_exists($cookie_dir)) {
                if (!mkdir($cookie_dir, 0775, true)) {
                    die("Failed to create directory $cookie_dir");
                }
            }
            $createCookie = $cookie_dir != false;
            $this->cookie_file = $createCookie
                    ? "{$cookie_dir}/cookie{$m[1]}.txt"
                    : false;
        }

        $this->ua = new UserAgent2($this->cookie_file, false);
        $this->ua->setProxy($proxy, $proxyAuth);
    }

    /**
     * Получить последнюю ошибку в работе скрипта. Если false - то нет ошибки
     * @return array|bool
     */
    public function getLastError() {
        return $this->lastErrorStr;
    }

    public function getResponseData() {
        return $this->responseData;
    }

    public function removeCookies() {
        $this->ua->clearCookies();
    }

    private function trace($msg) {
        if ($this->debug) {
            echo $msg . "\n";
        }
    }

    /**
     * Возвращает сумму цифрой из строки
     * @param $str
     * @return array|mixed|string
     */
    private function strParseMoney($str) {
        $cash = "";
        for ($i = 0; $i < strlen($str); $i++) {
            if (($str[$i] >= '0' && $str[$i] <= '9') || $str[$i] == ',') {
                $cash .= $str[$i];
            }
        }
        $tmp = explode(",", $cash);
        if (count($tmp) != 2) {
            return false;
        }
        $tmp[1] = substr($tmp[1], 0, 2);
        $cash = $tmp[0] + $tmp[1] / 100;
        return $cash;
    }

    /**
     * Получить валюту из строки
     * @param $str
     * @return bool
     */
    private function strGetCur($str) {
        $str = str_replace(chr(194) . chr(160), "", $str);
        if (preg_match("/([а-яА-Яa-zA-Z\\.]+)$/u", $str, $m)) {
            return $m[1];
        }
        return false;
    }

    /**
     * Обновить статус входа в систему.
     * @return bool
     */
    private function updateLoginStatus() {
        $this->trace("[QIWI] Updating login status...");

        $this->logged_in = false;

        $content = $this->ua->request(USERAGENT_METHOD_GET, QIWI_URL_MAINACTION, QIWI_URL_MAIN);
        if (!$content) {
            return false;
        }

        if (!($dom = str_get_html($content))) {
            $this->lastErrorStr = "Failed to build DOM from content of " . QIWI_URL_MAINACTION;
            return false;
        }

        if (!($obj = $dom->find("div[class=auth_login] div[class=phone]", 0))) {
            return false;
        }
        $phone = trim($obj->plaintext);
        if (strcmp($phone, $this->id) == 0) {
            $this->logged_in = true;
        } else {
            return false;
        }

        return true;
    }

    /**
     * Должна вызываться каждый раз чтобы войти в систему
     * @return bool
     */
    function login() {
        $this->updateLoginStatus();
        if ($this->logged_in) {
            $this->trace("[QIWI] Already logged in. Skip logging in procedure.");
            return true;
        }

        $this->getUrl(QIWI_URL_MAIN);
        $this->getUrl("https://sso.qiwi.com/app/proxy?v=1", QIWI_URL_MAIN);

        $this->trace("[QIWI] Not logged in. Starting procedure...");
        $this->ua->request(
                USERAGENT_METHOD_GET,
                "https://sso.qiwi.com/signin/oauth2",
                QIWI_URL_MAIN,
                false,
                ['Content-Type' => 'application/json']);
        if (!$this->doTGTS(USERAGENT_METHOD_GET, false, [
                'Content-Type' => 'application/json'
        ], "401|201")
        ) {
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
                "login" => $this->id,
                "password" => $this->password
        );
        $post_data = json_encode($loginParams);
        if (!($authRet = $this->doTGTS(USERAGENT_METHOD_POST, $post_data, [
                'Accept' => 'application/vnd.qiwi.sso-v1+json',
                'Content-Type' => 'application/json; charset=UTF-8',
                'Content-Length' => strlen($post_data),
        ], 201))
        ) {
            $this->lastErrorStr = "Failed to get login session";
            return false;
        }
        $authRet = json_decode($authRet, true);
        if (isset($authRet['entity']['error']['message'])) {
            $this->trace("[QIWI] Error: " . $authRet['entity']['error']['message']);
            return false;
        }
        $this->trace("[QIWI] Security ticked received.");
        if (!isset($authRet['entity']['ticket'])) {
            $this->lastErrorStr = $authRet['message'];
            $this->lastErrorNo = $authRet['code'];
            return false;
        }
        if (!isset($authRet['entity']['ticket'])) {
            $this->lastErrorStr = "Invalid TGTS response ticket format";
            return false;
        }
        $this->auth_ticket = $authRet['entity']['ticket'];
        $stsParams = array(
                "service" => QIWI_URL_SECURITY_CHECK,
                "ticket" => $this->auth_ticket
        );
        $post_data = json_encode($stsParams);
        if (!($authRet = $this->doSTS(USERAGENT_METHOD_POST, $post_data))) {
            $this->lastErrorStr = "Failed to get STS data";
            return false;
        }
        $authRet = json_decode($authRet, true);
        if (!isset($authRet['entity']['ticket'])) {
            $this->lastErrorStr = "Invalid STS response format";
            return false;
        }
        $this->auth_ticket = $authRet['entity']['ticket'];
        $this->trace("[QIWI] Sending ticket to QIWI server...");

        if (!$this->doSTS(USERAGENT_METHOD_OPTIONS, false, [
                'Access-Control-Request-Headers' => 'accept, accept-language, content-type',
                'Access-Control-Request-Method' => 'POST'])
        ) {
            $this->lastErrorStr = "Failed to send options to STS";
            return false;
        }
        if (!($authRet = $this->doSTS(USERAGENT_METHOD_POST, $post_data))) {
            $this->lastErrorStr = "Failed to get STS data";
            return false;
        }
        $authRet = json_decode($authRet, true);
        if (!isset($authRet['entity']['ticket'])) {
            $this->lastErrorStr = "Invalid STS response format";
            return false;
        }
        $this->trace("[QIWI] Updating security ticket...");
        $this->auth_ticket = $authRet['entity']['ticket'];

        $this->ua->request(USERAGENT_METHOD_GET, QIWI_URL_SECURITY_CHECK . "?ticket=" . $this->auth_ticket, QIWI_URL_MAIN, false, [
                'Host' => 'qiwi.com',
                'X-Requested-With' => 'XMLHttpRequest'
        ]);
        if ($this->ua->getStatus() !== 200) {
            $this->lastErrorStr = "Failed to do security check. Status=" . $this->ua->getStatus() . ", expected=200";
            return false;
        }
        $this->updateLoginStatus();

        if ($this->logged_in) {
            $this->trace("[QIWI] Login [$this->id] was successful.");
        } else {
            $this->trace("[QIWI] Login [$this->id] failed.");
        }

        return $this->logged_in;
    }

    /**
     * Загрузить историю платежей за указанный диапазон дат
     * @param string $date_from Дата в формате dd.mm.yyyy
     * @param string $date_to Дата в формате dd.mm.yyyy
     * @return array|bool
     */
    function loadHistoryDateRange($date_from, $date_to) {
        $url = QIWI_URL_HISTORY_DATERANGE . "?daterange=true&start=$date_from&finish=$date_to";
        return $this->loadHistory($url);
    }

    /**
     * Загрузить историю транзакций за сегодня
     * @return array|bool
     */
    function loadHistoryToday() {
        return $this->loadHistory(QIWI_URL_HISTORY_TODAY);
    }

    /**
     * Загрузить историю платежей за вчера
     * @return array|bool
     */
    function loadHistoryYesterday() {
        return $this->loadHistory(QIWI_URL_HISTORY_YESTERDAY);
    }

    /**
     * Загрузить историю платежей за последнюю неделю
     * @return array|bool
     */
    function loadHistoryLastWeek() {
        return $this->loadHistory(QIWI_URL_HISTORY_LAST_WEEK);
    }

    /**
     * Поиск транзакций по сумме платежа и комментарию
     * @param $tr
     * @param $amount
     * @param $comment
     * @return array
     */
    public function findTransaction($tr, $amount, $comment) {
        $result = array();
        foreach ($tr as $t) {
            if ($amount) {
                if ($t['cash'] == $amount) {
                    $amount_match = true;
                } else {
                    $amount_match = false;
                }
            } else {
                $amount_match = true;
            }
            if ($comment) {
                if (preg_match("/$comment/", $t['comment'])) {
                    $comment_match = true;
                } else {
                    $comment_match = false;
                }
            } else {
                $comment_match = true;
            }

            if ($amount_match && $comment_match) {
                $result[] = $t;
            }
        }

        return $result;
    }

    /**
     * Поиск транзакций по сумме платежа
     * @param $tr
     * @param $amount
     * @return array
     */
    public function findTransactionByAmount($tr, $amount) {
        return $this->findTransaction($tr, $amount, false);
    }

    /**
     * Поиск транзакций по комментарию
     * @param $tr
     * @param $comment
     * @return array
     */
    public function findTransactionByComment($tr, $comment) {
        return $this->findTransaction($tr, false, $comment);
    }

    /**
     * Загрузить историю транзакций
     * @param $url
     * @return array|bool
     */
    private function loadHistory($url) {
        $this->trace("[QIWI] Loading history $url ...");
        if (!($content = $this->getUrl($url, QIWI_URL_REPORTACTION))) {
            $this->lastErrorStr = "Failed to load history page data";
            return false;
        }

        if (!($dom = str_get_html($content))) {
            $this->lastErrorStr = "Failed to parse DOM from content downloaded by url $url";
            return false;
        }

        $transactions = array();

        foreach ($dom->find("div[class=reports] div[data-container-name=item]") as $item) {
            $classes = $item->getAttribute("class");
            $classes = preg_split("/\\s+/", $classes);
            $status = false;
            $statusText = "Unknown";
            foreach ($classes as $class) {
                $class = trim($class);
                if (strcmp($class, QIWI_STATUS_SUCCESS) == 0) {
                    $status = 1;
                    $statusText = "Success";
                } else if (strcmp($class, QIWI_STATUS_ERROR) == 0) {
                    $status = 2;
                    $statusText = "Error";
                } else if (strcmp($class, QIWI_STATUS_PROCESSED) == 0) {
                    $status = 3;
                    $statusText = "Processed";
                }
            }
            $date = $item->find("div[class=DateWithTransaction] span[class=date]", 0);
            $date = trim($date->plaintext);
            $time = $item->find("div[class=DateWithTransaction] span[class=time]", 0);
            $time = trim($time->plaintext);

            $tmp = $item->find("div[class=ProvWithComment] div[class=provider] span");
            $prov = "";
            foreach ($tmp as $tmp_item) {
                $prov .= " " . trim($tmp_item->plaintext);
            }
            $prov = trim($prov);

            $tmp = $item->find("div[class=ProvWithComment] div[class=comment]", 0);
            $comment = html_entity_decode(trim($tmp->plaintext));

            $tmp = $item->find("div[class=IncomeWithExpend]", 0);
            $classes = $tmp->getAttribute("class");
            if (strpos($classes, "expenditure") > 0) {
                $income = false;
            } else {
                $income = true;
            }
            $tmp = $item->find("div[class=IncomeWithExpend] div[class=cash]", 0);
            $tmp = trim($tmp->plaintext);
            if (!($cash = $this->strParseMoney($tmp))) {
                $cash = 0;
            }
            $cur = $this->strGetCur($tmp);

            //Комиссия
            $tmp = $item->find("div[class=IncomeWithExpend] div[class=commission]", 0);
            $tmp = trim($tmp->plaintext);
            if ($commission = $this->strParseMoney($tmp)) {
                $commission = 0;
            }

            $transaction = $item->find("div[class=extra] div[class=item] span[class=value]", 0);
            $transaction = $transaction->plaintext;

            //$this->trace("[QIWI] Transaction found: $transaction");
            //$this->trace("[QIWI] Status: $statusText");
            //$this->trace("[QIWI] Datetime: $date $time");
            //$this->trace("[QIWI] Provider: $prov");
            //$this->trace("[QIWI] Comment: $comment");
            //$this->trace("[QIWI] Cash: " . ($income ? "+" : "-") . $cash);

            if (!$income) {
                $cash *= -1;
            }

            $transactions[] = array(
                    'id' => $transaction,
                    'status' => $status,
                    'statusText' => $statusText,
                    'date' => $date,
                    'time' => $time,
                    'datetime' => $date . " " . $time,
                    'provider' => $prov,
                    'comment' => $comment,
                    'cash' => $cash,
                    'commission' => $commission,
                    'cur' => $cur
            );
        }
        return $transactions;
    }

    /**
     * Выставить новый счет
     * @param string $to Кому (включая код страны +7 или другой)
     * @param float $amount сумма
     * @param string $currency валюта RUB | USD | EUR (другая поддерживаемая QIWI)
     * @param bool|false $comment Комментарий (не обязательно)
     * @return bool
     */
    public function newOrder($to, $amount, $currency, $comment = false) {
        $this->trace("[QIWI] Creating new order to $to with amount $amount $currency");
        $post_data = "amount=$amount&change=&currency=$currency&comment=$comment&to=" . urlencode($to) . "&value=$amount";

        $response = $this->ua->request(USERAGENT_METHOD_POST, QIWI_URL_CREATE_BILL, QIWI_URL_FORM_BILL,
                $post_data, [
                        'Content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With' => 'XMLHttpRequest',
                        'Accept' => 'application/vnd.qiwi.sso-v1+json'
                ]);
        if ($this->ua->getStatus() !== 200 || !$response) {
            $this->lastErrorStr = "Failed to create new bill order.";
            return false;
        }
        $json = json_decode($response, true);
        if (!isset($json['data']['token'])) {
            $this->trace("[QIWI] Failed to create new order.");
            return false;
        }
        $post_data .= "&token=" . $json['data']['token'];

        //Повторный постинг данных с комитом
        $response = $this->ua->request(USERAGENT_METHOD_POST, QIWI_URL_CREATE_BILL, QIWI_URL_FORM_BILL,
                $post_data, [
                        'Content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With' => 'XMLHttpRequest',
                        'Accept' => 'application/vnd.qiwi.sso-v1+json'
                ]);

        $json = json_decode($response, true);
        if ($json['code']['value'] == '0') {
            $this->trace("[QIWI] Order created.");
            return true;
        }
        $this->trace("[QIWI] Create order failed.");
        $this->lastErrorStr = "Create order failed.";
        return false;
    }

    /**
     * Получить список счетов
     * @param string $mode QIWI_BILLS_MODE_IN или QIWI_BILLS_MODE_OUT. Только с датами можно использовать QIWI_BILLS_MODE_INOUT.
     * @param bool|false $dateFrom Дата начала периода (Формат dd.mm.YYYY)
     * @param bool|false $dateTo Дата окончания периода (Формат dd.mm.YYYY)
     * @return array|bool
     */
    public function loadBills($mode, $dateFrom = false, $dateTo = false) {
        $url = QIWI_URL_BILLS;
        if ($dateFrom && $dateTo) {
            $url .= "?daterange=true&start=$dateFrom&finish=$dateTo";
            if ($mode == QIWI_BILLS_MODE_IN || $mode == QIWI_BILLS_MODE_INOUT) {
                $url .= "&conditions.directions=in";
            }
            if ($mode == QIWI_BILLS_MODE_OUT || $mode == QIWI_BILLS_MODE_INOUT) {
                $url .= "&conditions.directions=out";
            }
        } else {
            if ($mode == QIWI_BILLS_MODE_IN) {
                $url .= "?type=1";
            } else {
                $url .= "?type=2";
            }
        }
        $this->trace("[QIWI] Loading bills $url ...");

        $content = $this->ua->request(USERAGENT_METHOD_GET, $url, QIWI_URL_MAIN);

        if (!$content) {
            echo "[QIWI] ERROR: Failed to load bills page data.\n";
            return false;
        }

        $transactions = array();

        $dom = str_get_html($content);
        foreach ($dom->find("div[class=order] div[data-container-name=item]") as $item) {
            $classes = $item->getAttribute("class");
            $classes = preg_split("/\\s+/", $classes);
            $status = false;
            $statusText = "Unknown";
            foreach ($classes as $class) {
                $class = trim($class);
                if (strcmp($class, QIWI_STATUS_PAID) == 0) {
                    $status = 1;
                    $statusText = "Paid";
                } else if (strcmp($class, QIWI_STATUS_NOT_PAID) == 0) {
                    $status = 2;
                    $statusText = "Not paid";
                } else if (strcmp($class, QIWI_STATUS_PROCESSED) == 0) {
                    $status = 3;
                    $statusText = "Processed";
                } else if (strcmp($class, QIWI_STATUS_CANCELED) == 0) {
                    $status = 4;
                    $statusText = "Canceled";
                } else if (strcmp($class, QIWI_STATUS_AWAITING_CONFIRM) == 0) {
                    $status = 5;
                    $statusText = "Awaiting confirmation";
                }
            }
            $payDate = $item->find("div[class=PayDateWidthOrderCreationDateWrap] div[class=payDate]", 0);
            $payDate = trim($payDate->plaintext);
            $creationDate = $item->find("div[class=PayDateWidthOrderCreationDateWrap] div[class=orderCreationDate]", 0);
            $creationDate = trim($creationDate->plaintext);

            $tmp = $item->find("div[class=CommentWrap] span[class=commentItem]", 0);
            $comment = html_entity_decode(trim($tmp->plaintext));

            if ($mode == QIWI_BILLS_MODE_IN) {
                $tmp = $item->find("div[class=FromWithTransactionWrap] div[class=from] span", 0);
                $from = html_entity_decode(trim($tmp->plaintext));
                $to = '';
            } else if ($mode == QIWI_BILLS_MODE_OUT) {
                $tmp = $item->find("div[class=FromWithTransactionWrap] div[class=from] span", 0);
                $to = html_entity_decode(trim($tmp->plaintext));
                $from = '';
            } else if ($mode == QIWI_BILLS_MODE_INOUT) {
                $tmp = $item->find("div[class=ToWrap] div[class=to]", 0);
                $to = trim(html_entity_decode(trim($tmp->plaintext)));
                $tmp = $item->find("div[class=FromWithTransactionWrap] div[class=from] span", 0);
                $from = trim(html_entity_decode(trim($tmp->plaintext)));
                if ($from == ' ') {
                    $from = "";
                } else if ($to == ' ') {
                    $to = "";
                }
            } else {
                $this->lastErrorStr = "Undefined method $mode";
                return false;
            }

            $tmp = $item->find("div[class=FromWithTransactionWrap] div[class=transaction] span", 0);
            $transaction = html_entity_decode(trim($tmp->plaintext));

            $tmp = $item->find("div[class=AmountWithCommissionWrap] div[class=amount]", 0);
            $tmp = trim($tmp->plaintext);
            $cash = $this->strParseMoney($tmp);

            $this->trace("[QIWI] Transaction found: $transaction");
            $this->trace("[QIWI] Status: $statusText");
            $this->trace("[QIWI] From: $from");
            $this->trace("[QIWI] To: $to");
            $this->trace("[QIWI] Pay Date: $payDate");
            $this->trace("[QIWI] Creation Date: $creationDate");
            $this->trace("[QIWI] Comment: $comment");
            $this->trace("[QIWI] Cash: " . $cash);

            $transactions[] = array(
                    'id' => $transaction,
                    'status' => $status,
                    'statusText' => $statusText,
                    'payDate' => $payDate,
                    'from' => $from,
                    'to' => $to,
                    'creationDate' => $creationDate,
                    'comment' => $comment,
                    'cash' => $cash,
            );
        }
        return $transactions;
    }

    /**
     * Получить состояние кошелька
     * @param $ref
     * @return bool|mixed
     */
    private function getPersonState($ref) {
        $content = $this->ua->request(USERAGENT_METHOD_POST, QIWI_URL_MAIN . "/person/state.action", $ref,
                false, [
                        'Host' => QIWI_HOST,
                        'Origin' => QIWI_URL_MAIN,
                        'X-Requested-With' => 'XMLHttpRequest'
                ]);
        try {
            return json_decode($content, true);
        } catch (Exception $e) {
            $this->lastErrorStr = $e;
            $this->lastErrorNo = 1060;
        }
        return false;
    }

    /**
     * Получить баланс кошелька
     * @return bool|string
     */
    function loadBalance() {
        $this->trace("[QIWI] Loading balance ...");
        if (!($data = $this->getPersonState(QIWI_URL_MAINACTION))) {
            $this->trace("[QIWI] Failed to get personal data: " . $this->lastErrorStr);
            return false;
        }
        return $data['data']['balances'];
    }

    private function saveState() {
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
        $post_data = 'v=1&_v=j41&a=474145743&t=event&ni=0&_s=7&dl=https%3A%2F%2F' . QIWI_HOST . '%2F&ul=ru&de=UTF-8&' .
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

    /**
     * Отпарвить TGTS запрос
     * @param int $method Метод GET, POST или OPTIONS
     * @param bool|string $post_data Данные для отправки в случае POST или OPTIONS
     * @param array $a_headers Дополнительные заголовки
     * @param int $correct_status Ожидаемый статус
     * @return bool|string
     */
    private function doTGTS($method, $post_data = false, $a_headers = [], $correct_status = 200) {
        $headers = array(
                'Host' => 'sso.qiwi.com',
                'Origin' => QIWI_URL_MAIN,
                'Accept' => 'application/vnd.qiwi.sso-v1+json',
        );
        if ($a_headers) {
            foreach ($a_headers as $k => $v) {
                $headers[$k] = $v;
            }
        }

        $correct_statuses = preg_split("/\\|/", $correct_status);

        $content = $this->ua->request($method, "https://sso.qiwi.com/cas/tgts", "https://sso.qiwi.com/app/proxy?v=1",
                $post_data, $headers);

        if (!in_array($this->ua->getStatus(), $correct_statuses)) {
            $this->trace("[TGTS] ERROR: Expected status=$correct_status, returned status=" . $this->ua->getStatus());
            return false;
        }

        if ($content) {
            try {
                if ($data = json_decode($content, true)) {
                    if (isset($data['entity']['ticket'])) {
                        $this->auth_ticket = $data['entity']['ticket'];
                        $this->trace("[TGTS] Security ticket updated: {$this->auth_ticket}");
                    }
                }

            } catch (Exception $e) {
            }
        }

        return $content;
    }

    private function updateTGTSTicket() {
        return $this->doTGTS(USERAGENT_METHOD_GET, false, [
                'Content-Type' => 'application/json'
        ], 201);
    }

    private function doSTS($method, $post_data = false, $a_headers = [], $expected_status = 200) {
        $headers = array(
                'Accept' => 'application/vnd.qiwi.sso-v1+json',
                'Content-Type' => 'application/json',
                'Host' => 'sso.qiwi.com',
                'Origin' => 'https://sso.qiwi.com',
        );
        if ($a_headers) {
            foreach ($a_headers as $k => $v) {
                $headers[$k] = $v;
            }
        }
        $content = $this->ua->request(
                $method,
                'https://sso.qiwi.com/cas/sts', 'https://sso.qiwi.com/app/proxy?v=1',
                $post_data,
                $headers);
        if ($this->ua->getStatus() !== $expected_status) {
            $this->trace("[STS] Expected status $expected_status, but returned status is " . $this->ua->getStatus());
            return false;
        }
        if (empty($content)) {
            return true;
        }
        if ($content) {
            try {
                if ($data = json_decode($content, true)) {
                    if (isset($data['entity']['ticket'])) {
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
    private function updateSTSTicket() {
        if (!$this->doSTS(USERAGENT_METHOD_OPTIONS, false, [])) {
            return false;
        }
        $stsParams = array(
                "ticket" => $this->auth_ticket,
                "service" => QIWI_URL_MAIN . "/j_spring_cas_security_check"
        );
        $post_data = json_encode($stsParams);
        if (!$this->doSTS(USERAGENT_METHOD_POST, $post_data, [
                'Content-Type' => 'application/json'
        ])
        ) {
            return false;
        }
        return true;
    }

    /**
     * Загрузить с сервера киви настройки провайдера
     * @param int $provider Идентификатор провайдера
     * @return bool
     */
    function getProviderOptions($provider) {
        $content = $this->ua->request(USERAGENT_METHOD_GET, QIWI_URL_MAIN . "/sinap/providers/$provider/form/proxy.action", false, false, [
                'Content-Type' => 'application/json',
                'Host' => QIWI_HOST
        ]);
        if ($this->ua->getStatus() !== 200 || !$content) {
            return false;
        }
        $json = json_decode($content, true);
        if (isset($json['data']['body']['content']) && $json['data']['body']['id'] == $provider) {
            return $json['data']['body']['content'];
        }
        return false;
    }

    /**
     * Получить список полей для провайдера
     * @param $options
     * @return array
     */
    function getProviderFields($options) {
        $fields = array();
        foreach ($options['elements'] as $element) {
            $fields[] = array(
                    'name' => $element['name'],
                    'type' => $element['type'],
                    'title' => $element['view']['title'],
                    'mask' => $element['view']['widget']['mask'],
                    'valid' => $element['validator']['predicate']['pattern']
            );
        }
        return $fields;
    }

    function checkProviderFields($fields, $options) {
        //TODO: Проверить корректность ввода полей
    }

    /**
     * Конвертация номера в 10ти значный формат телефона
     * @param $phone
     * @return bool
     */
    function phoneToProviderPhoneNumber($phone) {
        if (preg_match("/([0-9]{10})$/", $phone, $m)) {
            return $m[1];
        }
        return false;
    }

    /**
     * Проверка провайдера на необходимость верификации полей
     * @param $provider int Код провайдера
     * @param string $ref string реферальная ссылка
     * @return bool
     */
    function checkProviderIdent($provider, $ref = QIWI_URL_MAINACTION) {
        //Check auth requirements
        $data = $this->ua->request(USERAGENT_METHOD_GET, QIWI_URL_MAIN . "/rest/providerident/$provider", $ref, false, [
                'Authorization' => "Token {$this->sts_auth_ticket}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Host' => 'qiwi.com'
        ]);
        if ($this->ua->getStatus() !== 200 || !$data) {
            $this->lastErrorStr = "Failed to check provider identification for #$provider. Status=" . $this->ua->getStatus();
            $this->debugData = $data;
            $this->trace($this->lastErrorStr);
            return false;
        }

        /*
        try{
            $json = json_decode($data, true);
            if($json['required']){
                $required = true;
            }else{
                $required = false;
            }
        }catch(Exception $e){
            $this->lastErrorStr = $e;
            $this->lastErrorNo = 1064;
        }
        */
        return true;
    }

    /**
     * Валидировать поля для передачи провайдеру
     * @param $amount float сумма
     * @param $currency int код валюты
     * @param $source string счет, с которого будут сниматься деньги
     * @param $paymentMethod array Тип платежа
     * @param $comment string Комментарий
     * @param $fields array Дополнительные поля
     * @param $provider int код провайдера
     * @param string|bool $ref mixed Реферальная ссылка
     * @return bool|mixed
     */
    function validateProviderFields($amount, $currency, $source, $paymentMethod, $comment, $fields, $provider, $ref = false) {
        $this->trace("[PAY] Validating provider $provider fields...");
        if (!$comment) {
            $comment = "";
        }
        $validateProviderFieldsUrl = QIWI_URL_MAIN . "/user/sinap/api/terms/$provider/validations/proxy.action";
        $post_data = array(
                'id' => "" . round(microtime(true) * 1000),
                'sum' => array(
                        'amount' => $amount,
                        'currency' => $currency, //Int val
                ),
                'source' => $source,
                'paymentMethod' => $paymentMethod,
                'comment' => "" . $comment,
                'fields' => $fields
        );
        $post_data = json_encode($post_data);
        $content = $this->ua->request(USERAGENT_METHOD_POST, $validateProviderFieldsUrl, $ref, $post_data, [
                "Accept" => "application/vnd.qiwi.v2+json",
                "Content-Type" => "application/json",
                "Content-length" => strlen($post_data),
                "Origin" => QIWI_URL_MAIN,
                'Host' => 'qiwi.com',
                "X-Requested-With" => "XMLHttpRequest"
        ]);

        if ($this->ua->getStatus() !== 200 || !$content) {
            $this->lastErrorStr = "Failed to verify provider fields";
            return false;
        }

        try {
            $res = json_decode($content, true);
            if (!isset($res['data']['status'])) {
                $this->trace("[PAY] Data status value not found.");
                return false;
            }
            if ($res['data']['status'] != 200) {
                $this->trace("[PAY] Invalid data status: " . $res['data']['status']);
                return false;
            }
            return true;
        } catch (Exception $e) {
            $this->lastErrorStr = $e;
            $this->lastErrorNo = 1064;
        }
        return false;
    }

    /**
     * Подтрведить платеж провайдеру
     * @param $amount
     * @param $currency
     * @param $source
     * @param $paymentMethod
     * @param $comment
     * @param $fields
     * @param $provider
     * @param bool $ref
     * @return bool|mixed
     */
    private function confirmProviderPayment($amount, $currency, $source, $paymentMethod, $comment, $fields, $provider, $ref = false) {
        $this->trace("[PAY] Confirming provider $provider payment for $amount [CUR:$currency]");
        $validateProviderFieldsUrl = QIWI_URL_MAIN . "/user/sinap/api/terms/$provider/payments/proxy.action";
        $paymentId = "" . round(microtime(true) * 1000);
        $post_data = array(
                'id' => $paymentId,
                'sum' => array(
                        'amount' => $amount,
                        'currency' => $currency, //Int val
                ),
                'source' => $source,
                'paymentMethod' => $paymentMethod,
                'comment' => "" . $comment,
                'fields' => $fields
        );

        $post_data = json_encode($post_data);
        $content = $this->ua->request(USERAGENT_METHOD_POST, $validateProviderFieldsUrl, $ref, $post_data, [
                "Accept" => "application/vnd.qiwi.v2+json",
                "Content-Type" => "application/json",
                "Origin" => QIWI_URL_MAIN,
                "Content-length" => strlen($post_data),
                "X-Requested-With" => "XMLHttpRequest"
        ]);

        $res = json_decode($content, true);
        if ($res['data']['status'] == 200) {
            if (isset($res['data']['body']['transaction']['state']['code'])
                    && $res['data']['body']['transaction']['state']['code'] == "AwaitingSMSConfirmation"
            ) {
                $this->responseData = "Нужно подтверждение по телефону";
                $this->lastErrorStr = $this->responseData;
                return false;
            }
        }
        if ($res['data']['status'] != 200) {
            $this->lastErrorStr = ['data']['body']['message'];
            return false;
        }
        if ($res['data']['status'] != 200) {
            $this->responseData = $content;
            return false;
        }


        if ($this->ua->getStatus() !== 200 || !$content) {
            $this->trace("Failed to confirm payment");
            return false;
        }

        try {
            $res = json_decode($content, true);
            if (!isset($res['data']['status'])) {
                $this->trace("[PAY] Data status value not found.");
                return false;
            }
            if ($res['data']['status'] != 200) {
                $this->trace("[PAY] Invalid data status: " . $res['data']['status']);
                return false;
            }

            $res['paymentId'] = $paymentId;
            return $res;
        } catch (Exception $e) {
            $this->lastErrorStr = $e;
            $this->lastErrorNo = 1064;
        }
        return false;
    }

    /**
     * Преобразовать строковый код валюты в цифровой
     * @param $currency string RUB | USD | EUR
     * @return int
     */
    private function currencyToId($currency) {
        if ($currency == "EUR") {
            return QIWI_CURRENCY_EUR;
        } else if ($currency == "RUB") {
            return QIWI_CURRENCY_RUB;
        } else if ($currency == "USD") {
            return QIWI_CURRENCY_USD;
        } else if ($currency == "KAZ") {
            return QIWI_CURRENCY_KAZ;
        }
        return false;
    }

    /**
     * Получить контент по ссылке через запрос формата GET
     * @param string $url Ссылка
     * @param string|bool $ref Реферал
     * @param int $status Ожидаемый HTTP статус
     * @return bool|string
     */
    function getUrl($url, $ref = false, $status = 200) {
        $content = $this->ua->request(USERAGENT_METHOD_GET, $url, $ref);
        if ($this->ua->getStatus() !== $status) {
            $this->lastErrorStr = "Failed to download page $url";
            return false;
        }
        return $content;
    }

    /**
     * Перевести средства на другой QIWI кошелек
     * @param $to
     * @param $currency
     * @param $amount
     * @param $comment
     * @return bool
     */
    function transferMoney($to, $currency, $amount, $comment = false) {
        $fields = array(
                "account" => $to
        );
        if (!($currencyId = $this->currencyToId($currency))) {
            $this->trace("[QIWI:TRANSFER] Currency $currency not supported.");
            return false;
        }
        return $this->payProvider(QIWI_PROVIDER_QIWI_WALLET, $currency, $amount, $fields, $comment);
    }

    function bindEmail($mail) {
        $post_data_arr = array(
                'mail' => $mail
        );
        $post_data = http_build_query($post_data_arr);

        $url = QIWI_URL_MAIN . "/user/person/email/create/send.action";
        $additionalHeaders = [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host' => QIWI_HOST,
                'Origin' => QIWI_URL_MAIN,
                'X-Requested-With' => 'XMLHttpRequest'
        ];
        $response = $this->ua->request(USERAGENT_METHOD_POST, $url, false, $post_data, $additionalHeaders);
        $response = json_decode($response, true);

        $token = null;
        if ($response['code']['_name'] == "TOKEN") {
            $token = $response['data']['token'];
        } else {
            $this->lastErrorStr = "Не удалось отослать подтверждение на email";
            $this->trace("[QIWI:TRANSFER] cannot send email verification not supported.");
            return false;
        }
        $post_data_arr = array(
                'mail' => $mail,
                'token' => $token
        );
        $post_data = http_build_query($post_data_arr);
        $response = $this->ua->request(USERAGENT_METHOD_POST, $url, false, $post_data, $additionalHeaders);


        //        return $token;
        return $response;
    }

    function emailUnbindingToken() {
        $post_data_arr = array(
                'code' => "0",
                "type" => "EMAIL_REMOVE"
        );
        $post_data = http_build_query($post_data_arr);

        $url = QIWI_URL_MAIN . "/user/person/email/content/message.action";
        $additionalHeaders = [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host' => QIWI_HOST,
                'Origin' => QIWI_URL_MAIN,
                'X-Requested-With' => 'XMLHttpRequest'
        ];
        $response = $this->ua->request(USERAGENT_METHOD_POST, $url, false, $post_data, $additionalHeaders);

        return $response;
    }

    function unbindEmail($code, $token) {
        $post_data_arr = array(
                'identifier' => $token,
                "type" => "EMAIL_REMOVE",
                "code" => "" . $code,
                "data['code']" => "0",
                "data['type']" => "EMAIL_REMOVE",
        );
        $post_data = http_build_query($post_data_arr);

        $url = QIWI_URL_MAIN . "/user/confirmation/confirm.action";
        $additionalHeaders = [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host' => QIWI_HOST,
                'Origin' => QIWI_URL_MAIN,
                'X-Requested-With' => 'XMLHttpRequest'
        ];

        return $this->ua->request(USERAGENT_METHOD_POST, $url, false, $post_data, $additionalHeaders);
    }


    /**
     * Приобрести ваучер
     * @param $amount
     * @return bool
     */
    function purchaseVoucher($amount) {
        // initial request
        $purchaseVoucherFirstStepUrl = QIWI_URL_MAIN . "/user/sinap/api/terms/" . QIWI_PROVIDER_EMAIL_VOUCHER . "/payments/proxy.action";
        $postData = $this->getPostDataForVoucherPurchase($amount);
        $additionalHeaders = $this->getHeadersForVoucherPurchase($postData);
        $paymentResponseJson = $this->ua->request(
                USERAGENT_METHOD_POST, $purchaseVoucherFirstStepUrl,
                false, $postData, $additionalHeaders);

        // return if error (not nuff moneyz)
        $paymentResponse = json_decode($paymentResponseJson);
        if ($paymentResponse->data->status != 200) {
            $this->lastErrorStr = $paymentResponse->data->body->message;
            $this->responseData = $this->lastErrorStr;
            return false;
        }

        // extract transaction id from first request
        $transactionId = $paymentResponse->data->body->transaction->id;

        // url for second request
        $purchaseVoucherSecondStepUrl = QIWI_URL_MAIN . "/payment/form/success.action";

        // prepare and send second (success ) request
        $postData = "provider=" . QIWI_PROVIDER_EMAIL_VOUCHER
                . "&transaction=$transactionId"
                . "&cashback=false"
                . "&binded=false"
                . "&identification=true";
        $additionalHeaders['Accept'] = "text/html, */*; q=0.01";
        $additionalHeaders['Content-Type'] = "application/x-www-form-urlencoded; charset=UTF-8";
        $additionalHeaders['Content-Length'] = strlen($postData);

        $successResponseHtml = $this->ua->request(
                USERAGENT_METHOD_POST, $purchaseVoucherSecondStepUrl,
                false, $postData, $additionalHeaders
        );
        $this->debugData = $successResponseHtml;

        $voucherCode = $this->extractVoucherCode($successResponseHtml);
        $this->responseData = $voucherCode;

        return true;
    }

    private function extractVoucherCode($html) {
        $crawler = new Crawler($html);
        $codeLine = $crawler->filter("p")->first()->html();
        $code = explode(":", $codeLine)[1];

        return trim($code);
    }

    private function getPostDataForVoucherPurchase($amount) {
        $fields = [
                "account" => "708",
                "to_account_type" => "undefind"
        ];
        $paymentMethod = [
                "accountId" => "643",
                "type" => "Account"
        ];
        $sum = [
                "amount" => $amount,
                "currency" => "643"
        ];
        $post_data = json_encode([
                "comment" => "",
                "fields" => $fields,
                "id" => "" . round(microtime(true) * 1000),
                "paymentMethod" => $paymentMethod,
                "source" => "account_643",
                "sum" => $sum
        ]);

        return $post_data;
    }

    private function getHeadersForVoucherPurchase($postData) {
        return [
                "Accept" => "application/vnd.qiwi.v2+json",
                "Content-Type" => "application/json",
                "Origin" => QIWI_URL_MAIN,
                "Content-Length" => strlen($postData),
                "X-Requested-With" => "XMLHttpRequest"
        ];
    }

    /**
     * Activate voucher
     * @param $code
     */
    public function activateVoucher($code) {
        // send first request to get to confirmation
        $voucherActivateUrl = QIWI_URL_MAIN . "/user/eggs/activate/content/form.action";
        $post_data = json_encode(['code' => $code]);
        $additionalHeaders = $this->getActivateVoucherHeaders($post_data);
        $voucherActivationFirstResponseHtml = $this->ua->request(
                USERAGENT_METHOD_POST, $voucherActivateUrl,
                false, $post_data, $additionalHeaders);

        // extract token from response
        $token = $this->extractToken($voucherActivationFirstResponseHtml);

        // send
        $post_data = "token=$token&code=$code";
        $additionalHeaders["Content-Length"] = strlen($post_data);
        $voucherActivationSecondResponseHtml = $this->ua->request(
                USERAGENT_METHOD_POST, $voucherActivateUrl,
                true, $post_data, $additionalHeaders);

        // response
        if ($this->isErrorActivatingVoucher($voucherActivationSecondResponseHtml)) {
            $this->lastErrorStr = $this->extractErrorFromVoucherActivation($voucherActivationSecondResponseHtml);
        } else {
            $activationResult = $this->activateVoucherStepTwo($code);
            if ($activationResult !== true) {
                $this->lastErrorStr = $activationResult;
            } else {
                $this->responseData = "Активация ваучера прошла успешно";
            }
        }
    }

    private function getActivateVoucherHeaders($postData) {
        return [
                "Accept" => "text/html, */*; q=0.01",
                "Content-Type" => "application/x-www-form-urlencoded; charset=UTF-8",
                "Origin" => QIWI_URL_MAIN,
                "Content-Length" => strlen($postData),
                "X-Requested-With" => "XMLHttpRequest"
        ];
    }


    private function activateVoucherStepTwo($code) {
        $activateVoucherStepTwoUrl = QIWI_URL_MAIN . "/user/eggs/activate/content/activate.action";
        $post_data = "code=$code";
        $additionalHeaders = [
                "Accept" => "application/json, text/javascript, */*; q=0.01",
                "Content-Type" => "application/x-www-form-urlencoded; charset=UTF-8",
                "Origin" => QIWI_URL_MAIN,
                "Content-Length" => strlen($post_data),
                "X-Requested-With" => "XMLHttpRequest"
        ];
        $responseJson = $this->ua->request(USERAGENT_METHOD_POST, $activateVoucherStepTwoUrl,
                false, $post_data, $additionalHeaders);

        $response = json_decode($responseJson);

        if (isset($response->code) && isset($response->code->_name) && $response->code->_name == "ERROR") {
            return $response->message;
        } else {
            return true;
        }
    }

    private function extractToken($html) {
        $crawler = new Crawler($html);
        $token = $crawler->filter("input[name='token']")->attr("value");

        return $token;
    }

    private function isErrorActivatingVoucher($html) {
        $word = "Активировать";

        return strpos($html, $word) === false;
    }

    private function extractErrorFromVoucherActivation($html) {
        $crawler = new Crawler($html);
        $error = $crawler->filter("p:nth-child(2)")->first()->html();

        return mb_convert_encoding($error, "ISO-8859-1", "UTF-8");
    }


    /**
     * Оплатить провайдера
     * @param $provider_id
     * @param $currency
     * @param $amount
     * @param $fields
     * @param $comment
     * @return bool
     */
    function payProvider($provider_id, $currency, $amount, $fields, $comment) {
        if (!($currencyId = $this->currencyToId($currency))) {
            $this->trace("[QIWI:PAYPROVIDER] Currency $currency not supported.");
            return false;
        }

        $this->trace("[PAY] Started.");

        $payProviderUrl = QIWI_URL_MAIN . "/payment/form.action?provider=$provider_id";
        if (!$this->doTGTS(USERAGENT_METHOD_GET, false, ['Content-Type' => 'application/json'], 201)) {
            return false;
        }

        //Balance
        if (!($balance = $this->loadBalance())) {
            return false;
        }

        //Proxy
        $this->getUrl("https://sso.qiwi.com/app/proxy?v=1");

        //Обновить ключи безопасности
        if (!$this->updateTGTSTicket()) {
            return false;
        }

        if (!$this->updateSTSTicket()) {
            return false;
        }

        //Проверить провайдера
        if (!$this->checkProviderIdent($provider_id, $payProviderUrl)) {
            $this->trace("[PAY] Failed to check provider credentials");
            return false;
        }

        $paymentMethod = array(
                'type' => "Account",
                'accountId' => "$currencyId"
        );

        if (!($this->validateProviderFields($amount, $currencyId, "account_$currencyId", $paymentMethod, $comment, $fields, $provider_id))) {
            $this->trace("[PAY] Failed to validate field.");
            return false;
        }

        if (!($paymentInfo = $this->confirmProviderPayment($amount, $currencyId, "account_$currencyId", $paymentMethod, $comment, $fields, $provider_id))) {
            $this->trace("[PAY] Failed to confirm payment.");
            return false;
        }

        if (!isset($paymentInfo['data']['body']['transaction'])) {
            $this->trace("[PAY] Failed to retrieve transaction information");
            return false;
        }

        $tr = $paymentInfo['data']['body']['transaction'];
        $tr['paymentId'] = $paymentInfo['paymentId'];
        return $tr;
    }

    /**
     * Пополнить счет билайн
     * @param $number
     * @param $currency
     * @param $amount
     * @param bool|false $comment
     * @return bool
     */
    function payBeeline($number, $currency, $amount, $comment = false) {
        if (preg_match("/([0-9]{3})([0-9]{3})([0-9]{2})([0-9]{2})$/", $number, $m)) {
            $number = "({$m[1]}){$m[2]}-{$m[3]}-{$m[4]}";
        }
        $fields = array(
                'account' => $number
        );
        return $this->payProvider(2, $currency, $amount, $fields, $comment);
    }

    /**
     * Определить провайдера для оплаты счета телефона по номеру телефона
     * @param string $phone Номер телефона. Например +79683331112
     * @return bool
     */
    function getProviderByPhone($phone) {
        $params = array(
                'phone' => $phone
        );
        $post_data = http_build_query($params);
        $data = $this->ua->request(USERAGENT_METHOD_POST, QIWI_URL_MAIN . "/mobile/detect.action",
                QIWI_URL_MAIN . "/payment/mobile.action", $post_data, [
                        'Accept' => 'application/json, text/javascript, */*; q=0.01',
                        'Accept-Encoding' => 'gzip, deflate',
                        'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
                        'Cache-Control' => 'no-cache',
                        'Connection' => 'keep-alive',
                        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                        'Host' => 'qiwi.com',
                        'Origin' => 'https://qiwi.com',
                        'Pragma' => 'no-cache',
                        'X-Requested-With' => 'XMLHttpRequest'
                ]);
        if ($this->ua->getStatus() !== 200 || !$data) {
            $this->lastErrorStr = "Failed to detect provider by phone number";
            return false;
        }
        $data = json_decode($data, true);
        return isset($data['message']) ? $data['message'] : false;
    }

    /**
     * Подтверждение по СМС
     * @param $paymentId
     * @param $smsCode
     * @return mixed
     */
    function confirmSMSCode($paymentId, $smsCode) {
        $confirmSMSUrl = QIWI_URL_MAIN . "/user/sinap/payments/$paymentId/confirm/proxy.action";
        $post_data = array(
                "smsCode" => "" . $smsCode
        );

        $post_data = json_encode($post_data);
        $res = $this->ua->request(USERAGENT_METHOD_POST, $confirmSMSUrl, false, $post_data, [
                "Accept" => "application/vnd.qiwi.v2+json",
                "Content-Type" => "application/json",
                "Origin" => QIWI_URL_MAIN,
                "Content-length" => strlen($post_data),
                "X-Requested-With" => "XMLHttpRequest"
        ]);
        $res = json_decode($res, true);
        if (!isset($res['data']['body']['status']) || $res['data']['body']['status'] != 200) {
            $this->lastErrorStr = $res['data']['body']['message'];
            $this->lastErrorNo = $res['data']['body']['code'];
            return false;
        }
        return true;
    }

    /**
     * Получить список настроек киви по безопасности
     * @return array|bool
     */
    function getQIWISecuritySettings() {
        $url = QIWI_URL_MAIN . "/settings/options/security.action";
        if (!($content = $this->ua->request(USERAGENT_METHOD_GET, $url, QIWI_URL_MAIN))) {
            $this->lastErrorStr = "Failed to load contents from url '$url'";
            return false;
        }
        if (!($dom = str_get_html($content))) {
            $this->lastErrorStr = "Failed to create DOM tree from contens from url '$url'";
            return false;
        }
        $opts = array();
        foreach ($dom->find("div[class=security-settings-item]") as $div) {
            $class = explode(" ", $div->getAttribute("class"));
            if (count($class) != 2) {
                continue;
            }
            $class = substr($class[1], strlen("type_"));
            if (!($subdiv = $div->find("div[class=pseudo-checkbox-active]", 0))) {
                continue;
            }
            foreach ($div->find("div[class=toggle]") as $toggle) {
                $name = $toggle->getAttribute("data-container-name");
                $style = $toggle->getAttribute("style");
                if ($name == 'option-enabled' && !$style) {
                    $opts[$class] = 1;
                    break;
                } else if ($name == 'option-disabled' && !$style) {
                    $opts[$class] = 0;
                    break;
                }
            }
        }

        return $opts;
    }

    /**
     * Получить тип ответа от QIWI
     * @param $response
     * @return bool
     */
    private function getResponseType($response) {
        if (isset($response['code']) && isset($response['code']['_name'])) {
            return $response['code']['_name'];
        }
        return false;
    }

    /**
     * Получить сообщение из ответа QIWI
     * @param $response
     * @return bool|string
     */
    private function getResponseMessage($response) {
        if (isset($response['message'])) {
            return $response['message'];
        }
        return false;
    }

    /**
     * Установить настройку киви безопасности
     * @param $key
     * @param $enabled
     * @return array|bool
     */
    function setQIWISecuritySetting($key, $enabled) {
        $this->trace("[SQSS] Setting $key=>$enabled");
        if (!($this->updateTGTSTicket())) {
            return false;
        }

        $post_data_arr = array(
                'type' => $key,
                'value' => $enabled ? 'true' : 'false'
        );
        $post_data = http_build_query($post_data_arr);

        $url = QIWI_URL_MAIN . "/user/person/change/security.action";
        $response = $this->ua->request(USERAGENT_METHOD_POST, $url, false, $post_data, [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host' => QIWI_HOST,
                'Origin' => QIWI_URL_MAIN,
                'X-Requested-With' => 'XMLHttpRequest'
        ]);

        if ($this->ua->getStatus() !== 200) {
            $this->lastErrorStr = "Failed to send security ticket set request. Status=" . $this->ua->getStatus();
            return false;
        }
        $response = json_decode($response, true);
        if (!($token = $this->getResponseType($response) == 'TOKEN' && isset($response['data']['token']) ? $response['data']['token'] : false)) {
            $this->lastErrorStr = "Failed to fetch security token. Expected code TOKEN, received: " . $this->getResponseType($response);
            return false;
        }
        $this->trace("[SQSS] Token received = $token");

        $post_data_arr['token'] = $token;
        $post_data = http_build_query($post_data_arr);
        $response = $this->ua->request(USERAGENT_METHOD_POST, $url, false, $post_data, [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host' => QIWI_HOST,
                'Origin' => QIWI_URL_MAIN,
                'X-Requested-With' => 'XMLHttpRequest'
        ]);


        $this->debugData = $response;
        if ($this->ua->getStatus() !== 200) {
            $this->lastErrorStr = "Failed to send security ticket update request. Status=" . $this->ua->getStatus();
            return false;
        }
        $response = json_decode($response, true);
        $this->trace("[SQSS] done.");
        if ($this->getResponseType($response) == 'CONFIRM') {
            $this->responseData = $response['identifier'];
            return array(
                    'status' => 'CONFIRM',
                    'data' => $response['identifier']
            );
        } else if ($this->getResponseType($response) == 'NORMAL') {
            return array(
                    'status' => 'NORMAL'
            );
        }
        return false;
    }

    function userConfirmBySMS($type, $data, $smsCode) {
        $url = QIWI_URL_MAIN . "/user/confirmation/confirm.action";
        $post_data = array(
                'identifier' => $data,
                'type' => $type,
                'code' => $smsCode
        );
        $post_data = http_build_query($post_data);
        $response = $this->ua->request(USERAGENT_METHOD_POST, $url, false, $post_data, [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host' => QIWI_HOST,
                'Origin' => QIWI_URL_MAIN,
                'X-Requested-With' => 'XMLHttpRequest'
        ]);
        if ($this->ua->getStatus() != 200) {
            $this->lastErrorStr = "Failed to send sms confirm request, response status=" . $this->ua->getStatus();
            return false;
        }
        $response = json_decode($response, true);
        if ($this->getResponseType($response) == 'NORMAL') {
            return true;
        }
        $this->lastErrorStr = "Unknown error. Expected status: NORMAL, actual status: UNKNOWN";
        return false;
    }

    /**
     * Определить провайдера по номеру карты
     * @param $cardNumber
     * @return bool|string
     */
    public function detectCardProvider($cardNumber) {
        $url = QIWI_URL_MAIN . "/card/detect.action";

        if (preg_match_all("/([\\d]+)/", $cardNumber, $matches)) {
            $cardNumber = implode("", $matches[1]);
        }

        $post_data = array(
                'cardNumber' => $cardNumber
        );
        $post_data = http_build_query($post_data);

        $this->updateTGTSTicket();

        $response = $this->ua->request(USERAGENT_METHOD_POST, $url, false, $post_data, [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host' => QIWI_HOST,
                'Origin' => QIWI_URL_MAIN,
                'X-Requested-With' => 'XMLHttpRequest'
        ]);
        $response = json_decode($response, true);

        if ($this->getResponseType($response) != 'NORMAL') {
            //            $this->lastErrorStr = "Error detecting card provider:" . $this->getResponseMessage($response);
            $this->lastErrorStr = $this->getResponseMessage($response);
            return false;
        }

        return trim($response['message']);
    }

    /**
     * Перевод денег с кошелька на карту VISA, MasterCard, etc... Провайдер автоматически определяется по номеру карты
     * @param $cardNumber
     * @param $firstName
     * @param $lastName
     * @param $sum
     * @param $cur
     * @param bool $comment
     * @return bool
     */
    public function transferMoneyToCard($cardNumber, $firstName, $lastName, $sum, $cur, $comment = false) {
        if (!preg_match_all("/([\\d]+)/", $cardNumber, $matches)) {
            $this->lastErrorStr = "Invalid card number. Must be XXXX XXXX XXXX XXXX";
            return false;
        }

        $cardNumber = implode("", $matches[1]);

        if (!($prov = $this->detectCardProvider($cardNumber))) {
            return false;
        }

        $cardNumber = implode(" ", $matches[1]);

        $fields = array(
                'account' => $cardNumber,
                'termsId' => $prov,
                'reg_name_f' => $firstName,
                'reg_name' => $lastName
        );

        return $this->payProvider($prov, $cur, $sum, $fields, $comment);
    }

    /**
     * Получить список моих карт QIWI
     * @return array|bool
     */
    public function getMyCardsList() {
        $url = QIWI_URL_MAIN . "/cards.action";
        if (!($content = $this->ua->request(USERAGENT_METHOD_GET, $url))) {
            $this->lastErrorStr = "Failed to download content from $url";
            return false;
        }
        if (!($dom = str_get_html($content))) {
            $this->lastErrorStr = "Failed to parse content from $url";
            return false;
        }
        $cards = array();
        foreach ($dom->find("div[class=qiwicards] ul[class=list] li div[class=data]") as $data) {
            if (!($header = $data->find("h4", 0))) {
                continue;
            }
            $card = array();
            $header = trim($header->plaintext);
            if (preg_match("#^(.*)\\s+([0-9]{2}\\.[0-9]{2}\\.[0-9]{4})$#", $header, $m)) {
                $card['title'] = $m[1];
                $card['date'] = $m[2];
            }
            foreach ($data->find("a[target=_self]") as $a) {
                $href = $a->getAttribute("href");
                if (preg_match("#^/([a-z]{3})/(?:info|details)\\.action\\?id=([0-9]+)#", $href, $m)) {
                    $card['id'] = $m[2];
                    $card['type'] = $m[1];
                    $cards[] = $card;
                }
            }
        }
        return $cards;
    }


    /**
     * Выслать реквизиты карты по СМС
     * @param $qvc_id
     * @return bool
     */
    function remindQVCCredentials($qvc_id) {
        $this->updateTGTSTicket();
        $url = QIWI_URL_MAIN . '/user/qvc/details.action';

        $data = array(
                'id' => $qvc_id
        );
        $post_data = http_build_query($data);
        $response = $this->ua->request(USERAGENT_METHOD_POST, $url, false, $post_data, [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host' => QIWI_HOST,
                'Origin' => QIWI_URL_MAIN,
                'X-Requested-With' => 'XMLHttpRequest'
        ]);

        if (($status = $this->ua->getStatus()) != 200) {
            $this->lastErrorStr = "Failed to send request. Status 200 expected, but $status received.";
            return false;
        }

        $response = json_decode($response, true);
        if ($this->getResponseType($response) != 'TOKEN') {
            $this->lastErrorStr = "Failed to process response. TOKEN expected.";
            return false;
        }

        $data['token'] = $response['data']['token'];

        $post_data = http_build_query($data);
        $response = $this->ua->request(USERAGENT_METHOD_POST, $url, false, $post_data, [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host' => QIWI_HOST,
                'Origin' => QIWI_URL_MAIN,
                'X-Requested-With' => 'XMLHttpRequest'
        ]);

        if (($status = $this->ua->getStatus()) != 200) {
            $this->lastErrorStr = "Failed to send request with token. Status 200 expected but $status received.";
            return false;
        }

        $response = json_decode($response, true);
        if ($this->getResponseType($response) != 'NORMAL') {
            $this->lastErrorStr = "Failed to process response. Exptected type NORMAL.";
            return false;
        }

        return true;
    }

    /**
     * Выгрести только цифры из строки
     * @param $in_str
     * @return bool|string
     */
    private function parseDigits($in_str) {
        if (preg_match_all("/(\\d+)/", $in_str, $m)) {
            return implode("", $m[1]);
        }
        return false;
    }

    /**
     * Считать персональные данные владельца кошелька
     * @return bool|mixed|string
     */
    function getQIWIWalletOwnerData() {
        $this->updateTGTSTicket();
        $this->updateSTSTicket();

        $url = QIWI_URL_MAIN . "/rest/identifications/" . $this->parseDigits($this->id);
        $response = $this->ua->request(USERAGENT_METHOD_GET, $url, false, false, [
                'Accept' => 'application/json',
                'Authorization' => 'Token ' . $this->sts_auth_ticket,
                'Content-Type' => 'application/json',
                'Host' => QIWI_HOST
        ]);

        if ($this->ua->getStatus() != 200) {
            $this->lastErrorStr = "Expected status 200, but " . $this->ua->getStatus() . ' received.';
            return false;
        }
        $response = json_decode($response, true);

        return $response;
    }

    function lsetQIWIWalletOwnerData($lastname, $firstname, $middlename, $birthdate, $passport, $snils, $inn, $oms) {
        $this->updateTGTSTicket();
        $this->updateSTSTicket();
        $url = QIWI_URL_MAIN . "/rest/identifications/" . $this->parseDigits($this->id);
        $ref = QIWI_URL_MAIN . '/settings/options/wallet/edit.action';
        $data = json_encode([
                'lastName' => $lastname,
                'firstName' => $firstname,
                'middleName' => $middlename,
                'birthDate' => $birthdate,
                'passport' => $passport
        ]);
        $response = $this->ua->request(USERAGENT_METHOD_PUT, $url, $ref, $data, [
                'Accept' => 'application/json',
                'Authorization' => "Token {$this->sts_auth_ticket}",
                'Content-Type' => 'application/json',
                'Host' => QIWI_HOST,
                'Origin' => QIWI_URL_MAIN
        ]);
        dump($response);
        if ($this->ua->getStatus() != 200) {
            $this->lastErrorStr = "Expected status 200, but " . $this->ua->getStatus() . ' received.';
            return false;
        }
        $response = json_decode($response, true);
        $this->updateTGTSTicket();
        $this->updateSTSTicket();
        $url = QIWI_URL_MAIN . "/rest/identifications/{$this->parseDigits($this->id)}/docs";
        $data = json_encode([
                'inn' => $inn,
                'snils' => $snils,
                'oms' => $oms,
        ]);
        $response = $this->ua->request(USERAGENT_METHOD_PUT, $url, $ref, $data, [
                'Accept' => 'application/json',
                'Authorization' => "Token {$this->sts_auth_ticket}",
                'Content-Type' => 'application/json',
                'Host' => QIWI_HOST,
                'Origin' => QIWI_URL_MAIN
        ]);
        if ($this->ua->getStatus() != 200) {
            $this->lastErrorStr = "Expected status 200, but " . $this->ua->getStatus() . ' received.';
            return false;
        }
        $response = json_decode($response, true);

        return $response;
    }

    /**
     * Установить персональные данные владельца кошелька
     * @param $lastname
     * @param $firstname
     * @param $middlename
     * @param $birthdate
     * @param $passport
     * @param $snils
     * @param $inn
     * @param $oms
     * @return bool|mixed|string
     */
    function setQIWIWalletOwnerData($lastname, $firstname, $middlename, $birthdate, $passport, $snils, $inn, $oms) {
        $this->updateTGTSTicket();
        $this->updateSTSTicket();

        //        $this->login();
        $url = QIWI_URL_MAIN . "/rest/identifications/" . $this->parseDigits($this->id);
        //        $url = QIWI_URL_REST . "/identification/v1/persons/{$this->parseDigits($this->id)}/identification";
        //        dump($url);
        $ref = QIWI_URL_MAIN . '/settings/identification/form';
        //        $ref = false;
        $data = json_encode([
                'lastName' => $lastname,
                'firstName' => $firstname,
                'middleName' => $middlename,
                'birthDate' => $birthdate,
                'passport' => $passport,
                'oms' => $oms,
                'snils' => $snils,
                'inn' => $inn,
        ]);

        $headers = [
                'Accept' => 'application/json',
                'Authorization' => "Token " . $this->sts_auth_ticket,
                'Content-Type' => 'application/json',
        ];
        $response = $this->ua->request(USERAGENT_METHOD_PUT, $url, $ref, $data, $headers, true);

//        return $response;

        if ($this->ua->getStatus() != 200) {
            $this->lastErrorStr = "Expected status 200, but " . $this->ua->getStatus() . ' received.';
            $this->lastErrorStr = $response;
            return false;
        }
        $response = json_decode($response, true);

        $this->updateTGTSTicket();
        $this->updateSTSTicket();

        $url = QIWI_URL_MAIN . "/rest/identifications/{$this->parseDigits($this->id)}/docs";
        $data = json_encode([
                'inn' => $inn,
                'snils' => $snils,
                'oms' => $oms,
        ]);
        $response = $this->ua->request(USERAGENT_METHOD_PUT, $url, $ref, $data, [
                'Accept' => 'application/json',
                'Authorization' => "Token {$this->sts_auth_ticket}",
                'Content-Type' => 'application/json',
                'Host' => QIWI_HOST,
                'Origin' => QIWI_URL_MAIN
        ]);
        if ($this->ua->getStatus() != 200) {
            $this->lastErrorStr = "Expected status 200, but " . $this->ua->getStatus() . ' received.';
            return false;
        }
        $response = json_decode($response, true);
        return $response;
    }

    function captcha() {

    }
}

<?php

namespace App\Services\Qiwi;

use Exception;
use Symfony\Component\DomCrawler\Crawler;

trait QiwiAuth {
    /**
     * Обновить статус входа в систему.
     * @return bool
     */
    private function updateLoginStatus() {
        $this->loggedIn = false;

        $response = $this->client->request('GET', 'https://qiwi.com/main.action', [
                'headers' => [
                        'Referer' => 'https://qiwi.com'
                ],
        ]);

        $crawler = new Crawler($response->getBody()->getContents());

        if (!$crawler) {
            return false;
        }

        $phone = $crawler->filter('.auth_login .phone');

        if ($phone->count() && trim($phone->first()->text()) === $this->login) {
            $this->loggedIn = true;
        } else {
            return false;
        }

        return $this->loggedIn;
    }

    /**
     * @return bool
     */
    public function login() {
        $this->updateLoginStatus();

        if ($this->loggedIn) {
            return true;
        }

        $this->auth();

        $this->updateLoginStatus();

        return $this->loggedIn;
    }

    /**
     * @return bool
     */
    protected function auth() {
        $headers = [
                'Accept' => 'application/vnd.qiwi.sso-v1+json',
                'Content-Type' => 'application/json; charset=UTF-8',
                'Referer' => 'https://sso.qiwi.com/app/proxy?v=1',
        ];

        $numOfAttempts = 1;
        $attempts = 0;

        do {
            try {
                $tgt = $this->getTgts($headers);
                $sts = $this->getSts($headers, $tgt);
                $this->checkAuth($sts);
                $attempts = $numOfAttempts;

            } catch (Exception $e) {
                $attempts++;
                sleep(1);
                continue;
            }

        } while ($attempts < $numOfAttempts);

        return true;
    }

    /**
     * @param $headers
     * @return string
     */
    protected function getTgts($headers): string {
        $response = $this->client->post('https://sso.qiwi.com/cas/tgts', [
                'headers' => $headers,
                'json' => [
                        'login' => $this->login,
                        'password' => $this->password,
                ],
        ]);

        return json_decode($response->getBody())->entity->ticket;
    }

    /**
     * @param $headers
     * @param $tgt
     * @return string
     */
    protected function getSts($headers, $tgt): string {
        $headers['Origin'] = 'https://sso.qiwi.com';
        $headers['Host'] = 'sso.qiwi.com';

        $response = $this->client->post('https://sso.qiwi.com/cas/sts', [
                'headers' => $headers,
                'json' => [
                        'service' => 'https://qiwi.com/j_spring_cas_security_check',
                        'ticket' => $tgt,
                ],
        ]);

        return json_decode($response->getBody())->entity->ticket;
    }

    /**
     * @param $ticket
     * @return bool
     */
    protected function checkAuth($ticket): bool {
        $response = $this->client->request('GET', 'https://qiwi.com/j_spring_cas_security_check', [
                'headers' => [
                        'Host' => 'qiwi.com',
                        'X-Requested-With' => 'XMLHttpRequest',
                        'Referer' => 'https://qiwi.com/',
                ],
                'query' => [
                        'ticket' => $ticket,
                ],
        ]);

        return $response->getStatusCode() == 200;
    }
}
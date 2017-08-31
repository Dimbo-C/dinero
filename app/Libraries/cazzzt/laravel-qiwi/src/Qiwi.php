<?php

namespace Cazzzt\Qiwi;

class Qiwi
{
	protected $id;
    protected $password;
    protected $auth_ticket;
    protected $sts_auth_ticket;
    protected $auth_links;
    protected $logged_in;
    protected $debug;
    protected $cookie_file;
    protected $proxy;
    protected $proxyAuth;
    protected $lastErrorNo;
    protected $lastErrorStr;
    protected $ua;

    protected function __construct()
    {
    	$this->login = $login;
        $this->password = $password;
        $this->auth_ticket = false;
        $this->sts_auth_ticket = false;
        $this->auth_links = false;
        $this->logged_in = false;
        $this->debug = $debug_mode;
        $this->proxy = $proxy;
        $this->proxyAuth = $proxyAuth;

        $this->client = new Client();
    }
}
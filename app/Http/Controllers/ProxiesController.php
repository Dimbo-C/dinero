<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\ProxyRepository;
use App\Http\Requests\CreateProxyRequest;
use Illuminate\Http\Request;

class ProxiesController extends Controller
{
    /**
     * @var ProxyRepository
     */
    protected $proxy;

    /**
     * ProxiesController constructor.
     * @param ProxyRepository $proxy
     */
    public function __construct(ProxyRepository $proxy)
    {
        $this->proxy = $proxy;
    }

    public function get(Request $request)
    {
        return $this->proxy->get($request->all());
    }

    /**
     * @param CreateProxyRequest $request
     * @return mixed
     */
    public function store(CreateProxyRequest $request)
    {
        return $this->proxy->insert($request->proxies);
    }
}

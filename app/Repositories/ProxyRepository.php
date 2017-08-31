<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProxyRepository as Contract;
use App\Events\Proxies\ProxiesInserted;
use App\Events\Proxies\ProxyCreated;
use App\Proxy;

class ProxyRepository implements Contract {
    /**
     * {@inheritDoc}
     */
    public function get(array $data) {
        if (isset($data['type'])) {
            return Proxy::where('type', $data['type'])->get();
        }

        return Proxy::all();
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data) {
        $proxy = new Proxy;

        $proxy->create([
                'host' => $data['host'],
                'port' => $data['port'],
                'login' => $data['login'],
                'password' => $data['password'],
                'type' => isset($data['type']) ? $data['type'] : "",
                'using_type' => isset($data['using_type']) ? $data['using_type'] : ""
        ]);

        event(new ProxyCreated($proxy));

        return $proxy;
    }


    /**
     * {@inheritDoc}
     */
    public function insert(array $data) {
        $proxy = new Proxy;

        $timestamp = date('Y-m-d H:i:s');

        foreach ($data as $key => $p) {
            $data[$key]['created_at'] = $timestamp;
        }

        $proxies = $proxy->insert($data);

        event(new ProxiesInserted($proxies));

        return $data;
    }
}

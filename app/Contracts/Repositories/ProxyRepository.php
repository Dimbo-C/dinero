<?php

namespace App\Contracts\Repositories;

interface ProxyRepository
{
    /**
     * Получаем прокси.
     * @param array $data
     * @return mixed
     */
    public function get(array $data);

    /**
     * Добавляем новый прокси.
     * @param array $data
     * @return
     */
    public function create(array $data);

    /**
     * Добавляем сразу несколько прокси.
     * @param array $data
     * @return
     */
    public function insert(array $data);
}
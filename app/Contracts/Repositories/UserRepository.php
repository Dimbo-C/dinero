<?php

namespace App\Contracts\Repositories;

interface UserRepository
{
    /**
     * Получаем текущего пользователя приложения.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function current();

    /**
     * Получаем пользователя по данному ID.
     *
     * @param  int $id
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function find($id);

    /**
     * Получаем всех сотрудников для данного пользователя.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function staffForUser($user);
}

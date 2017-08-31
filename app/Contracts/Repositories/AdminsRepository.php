<?php

namespace App\Contracts\Repositories;

interface AdminsRepository
{
    /**
     * Get the team matching the given ID.
     *
     * @param  string|int  $id
     * @return User
     */
    public function find($id);

    /**
     * Get all of the teams for a given user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function forUser($user);
}
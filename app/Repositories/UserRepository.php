<?php

namespace App\Repositories;

use App\User;
use Illuminate\Support\Facades\Auth;
use App\Contracts\Repositories\UserRepository as Contract;

class UserRepository implements Contract {
    /**
     * {@inheritdoc}
     */
    public function current() {
        if (Auth::check()) {
            return $this->find(Auth::id());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function find($id) {
        $user = User::where('id', $id)->with('roles', 'head')->first();

        return $user ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function staffForUser($user) {
        return $user->staff()->with('roles')->get();
    }
}

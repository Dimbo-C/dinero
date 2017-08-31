<?php

namespace App;

use App\Contracts\Repositories\UserRepository;
use App\Contracts\InitialFrontendState as Contract;

class InitialFrontendState implements Contract
{
    /**
     * {@inheritdoc}
     */
    public function forUser($user)
    {
        return [
            'user' => $this->currentUser(),
        ];
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function currentUser()
    {
        return app(UserRepository::class)->current();
    }
}

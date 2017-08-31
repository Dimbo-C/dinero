<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Contracts\Repositories\UserRepository;

class OwnAdminsController extends Controller
{
    public function all()
    {
        return app(UserRepository::class)->staffForUser(Auth::user());
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as UserController;


class Controller extends UserController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

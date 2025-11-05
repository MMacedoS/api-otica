<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Config\Request;

class AuthController
{
    public function __construct()
    {
        // Constructor code here
    }

    public function login(Request $request, $id, $token)
    {
        echo "Login endpoint";
        echo "ID: " . $id;
        echo "Token: " . $token;
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Config\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Users\IUsuarioRepository;

class UsuarioController extends Controller
{
    protected $usuarioRepository;

    public function __construct(IUsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function index(Request $request)
    {
        $users = $this->usuarioRepository->findAll();
        return json_response($users, 200);
    }
}

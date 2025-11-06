<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Config\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Users\IUsuarioRepository;
use App\Transformers\Users\UsuarioTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class UsuarioController extends Controller
{
    protected $usuarioRepository;
    protected $usuarioTransformer;

    public function __construct(IUsuarioRepository $usuarioRepository, UsuarioTransformer $usuarioTransformer)
    {
        $this->usuarioRepository = $usuarioRepository;
        $this->usuarioTransformer = $usuarioTransformer;
    }

    public function index(Request $request)
    {
        $users = $this->usuarioRepository->findAll();
        $perPage = $request->getParam('limit') ?? 10;
        $currentPage = $request->getParam('page') ?? 1;

        $paginator = new Paginator($users, $perPage, $currentPage);
        $transformed = $this->usuarioTransformer->transformCollection($paginator->getPaginatedItems());
        return json_response([
            'data' => $transformed,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->totalItems(),
                'last_page' => $paginator->lastPage(),
                'has_previous_page' => $paginator->hasPreviousPage(),
                'has_next_page' => $paginator->hasNextPage(),
            ]
        ], 200);
    }

    public function indexWithoutPagination(Request $request)
    {
        $users = $this->usuarioRepository->findAll();
        $transformed = $this->usuarioTransformer->transformCollection($users);

        return json_response(['data' => $transformed]);
    }

    public function store(Request $request)
    {
        $body = $request->getBodyParams() ?? $request->getJsonBody();

        $validator = new Validator($body);
        $rules = [
            'name' => 'required',
            'email' => 'required',
        ];

        if (!$validator->validate($rules)) {
            return json_response(['errors' => $validator->getErrors()], 422);
        }

        $params = $this->usuarioTransformer->transformArray($body);

        $usuario = $this->usuarioRepository->create($params);

        if (is_null($usuario)) {
            return json_response("não cadastrado", 422);
        }

        $transformed = $this->usuarioTransformer->transform($usuario);

        return json_response($transformed, 201);
    }
}

<?php

namespace App\Transformers\Users;

use App\Models\Users\Usuario;
use App\Transformers\Traits\TransformerTrait;

class UsuarioTransformer
{
    use TransformerTrait;

    public function transform(Usuario $data): array
    {
        return [
            'id' => $data->id_usuario ?? null,
            'uuid' => $data->uuid ?? null,
            'name' => $data->nome ?? null,
            'email' => $data->email ?? null,
            'access' => $data->acesso ?? null,
            'active' => $data->ativo ?? null,
            'created_at' => $data->criado_em ?? null,
            'updated_at' => $data->atualizado_em ?? null,
        ];
    }

    public function transformCollection(array $data): array
    {
        return array_map(function (Usuario $item) {
            return $this->transform($item);
        }, $data);
    }

    private function keysTransform(): array
    {
        return [
            'code' => 'id_usuario',
            'id' => 'uuid',
            'name' => 'nome',
            'email' => 'email',
            'access' => 'acesso',
            'active' => 'ativo',
            'password' => 'senha',
            'created_at' => 'criado_em',
            'updated_at' => 'atualizado_em',
        ];
    }

    public function transformArray(array $data)
    {
        return $this->transformKeys($data, $this->keysTransform());
    }
}

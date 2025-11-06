<?php

namespace App\Transformers\Users;

use App\Models\Users\Usuario;

class UsuarioTransformer
{
    public function transform(Usuario $data): array
    {
        return [
            'id' => $data->id ?? null,
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

    public function keysTransform(): array
    {
        return [
            'id' => 'uuid',
            'code' => 'id',
            'name' => 'nome',
            'email' => 'email',
            'access' => 'acesso',
            'active' => 'ativo',
            'created_at' => 'criado_em',
            'updated_at' => 'atualizado_em',
        ];
    }
}

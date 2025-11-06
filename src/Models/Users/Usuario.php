<?php

namespace App\Models\Users;

use App\Models\Traits\UuidTrait;

class Usuario
{
    use UuidTrait;

    public int $id_usuario;
    public string $uuid;
    public string $nome;
    public string $email;
    public string $senha;
    public string $acesso;
    public ?int $ativo;
    public ?string $criado_em;
    public ?string $atualizado_em;

    public function __construct() {}

    public function prepareCreate(array $fields): Usuario
    {
        $usuario = new Usuario();
        foreach ($fields as $key => $field) {
            if (array_key_exists($key, (array)$usuario)) {
                continue;
            }
            $usuario->$key = $field;
        }
        $usuario->uuid = $this->generateUuid();
        $usuario->senha = $this->generatePassword($fields);
        return $usuario;
    }

    public function prepareUpdate(array $field, Usuario $usuario,  bool $forceNewPassword = false): Usuario
    {
        foreach ($field as $key => $value) {
            if (array_key_exists($key, (array)$usuario)) {
                $usuario->$key = $value;
            }
        }
        if ($forceNewPassword) {
            $usuario->senha = $this->generatePassword($field);
        }

        return $usuario;
    }

    private function generatePassword(array $data): string
    {
        $password = !empty($data['senha']) ? $data['senha'] : 'password123';
        return password_hash($password, PASSWORD_BCRYPT);
    }
}

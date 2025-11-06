<?php

namespace App\Models\Users;

use App\Models\Traits\UuidTrait;

class Usuario
{
    use UuidTrait;

    public int $id;
    public string $uuid;
    public string $nome;
    public string $email;
    public string $senha;
    public string $acesso;
    public ?int $ativo;
    public ?string $criado_em;
    public ?string $atualizado_em;

    public function __construct() {}

    public function toObject(array $fields): Usuario
    {
        $data = [];
        foreach ($fields as $field) {
            if (property_exists($this, $field)) {
                $data[$field] = $this->$field;
            }
        }
        $usuario = new Usuario(...array_values($data));
        $usuario->uuid = $this->generateUuid();
        $usuario->senha = $this->generatePassword($fields);
        return $usuario;
    }

    public function toObjectExists(array $field, Usuario $usuario,  bool $forceNewPassword = false): Usuario
    {
        foreach ($field as $key => $value) {
            if (property_exists($usuario, $key)) {
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
        $password = !empty($data['password']) ? $data['password'] : 'password123';
        return password_hash($password, PASSWORD_BCRYPT);
    }
}

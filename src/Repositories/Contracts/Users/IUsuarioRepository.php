<?php

namespace App\Repositories\Contracts\Users;

interface IUsuarioRepository
{
    public function findById(int $id);
    public function findAll(array $filters = []): array;
    public function findByUuid(string $uuid);
    public function findByEmail(string $email);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}

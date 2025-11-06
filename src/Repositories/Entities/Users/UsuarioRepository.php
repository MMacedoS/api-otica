<?php

namespace App\Repositories\Entities\Users;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Users\Usuario;
use App\Repositories\Contracts\Users\IUsuarioRepository;
use App\Repositories\Entities\Traits\FindTrait;
use App\Utils\LoggerHelper;

class UsuarioRepository extends SingletonInstance implements IUsuarioRepository
{
    private const TABLE = 'usuario';
    private const CLASS_NAME = Usuario::class;

    use FindTrait;

    public function __construct()
    {
        $this->model = new Usuario();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function findAll()
    {
        // Implementation here
    }

    public function findByEmail(string $email) {}

    public function create(array $data)
    {
        // Implementation here
    }

    public function update(int $id, array $data)
    {
        // Implementation here
    }

    public function delete(int $id)
    {
        // Implementation here
    }

    public function login(string $email): ?Usuario
    {
        if (empty($email)) {
            return null;
        }

        try {
            $stmt = $this->conn->prepare(
                "SELECT id_usuario as id, senha, nome, email, acesso, ativo, uuid, criado_em, atualizado_em
                    FROM " . self::TABLE . " 
                    WHERE email = :email 
                    and ativo = 1"
            );
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
            $user = $stmt->fetch();

            if (!$user) {
                return null;
            }

            return $user;
        } catch (\Throwable $th) {
            LoggerHelper::logError("Error during login: " . $th->getMessage());
            return null;
        }
    }
}

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

    public function findAll(array $params = []): array
    {
        $sql = "SELECT id_usuario, nome, email, acesso, ativo, uuid, criado_em, atualizado_em
                FROM " . self::TABLE . " 
                WHERE ativo = 1";

        $conditions = [];
        $bindings = [];

        if (isset($params['name_email'])) {
            $conditions[] = "(nome LIKE :name_email or email LIKE :name_email)";
            $bindings[':name_email'] = '%' . $params['name_email'] . '%';
        }

        if (isset($params['access']) && $params['access'] != '') {
            $conditions[] = "acesso = :access";
            $bindings[':access'] = $params['access'];
        }

        if (isset($params['situation']) && $params['situation'] != '') {
            $conditions[] = "ativo = :situation";
            $bindings[':situation'] = $params['situation'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY nome DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        return $stmt->fetchAll();
    }

    public function findByEmail(string $email) {}

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        $data = $this->model->prepareCreate($data);
        $user = $this->user_exists($data);

        if ($user) {
            return $user;
        }

        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn
                ->prepare(
                    "INSERT INTO " . self::TABLE .
                        "
                    SET
                        uuid = :uuid,
                        nome = :nome,
                        email = :email,
                        acesso = :acesso,
                        senha = :senha
                "
                );

            $created = $stmt->execute(
                [
                    ":uuid" => (string)$data->uuid,
                    ":nome" => (string)$data->nome,
                    "email" => (string)$data->email,
                    "acesso" => (string)$data->acesso,
                    "senha" => (string)$data->senha
                ]
            );

            if ($created) {
                $this->conn->commit();
                return $this->findByUuid($data->uuid);
            }

            $this->conn->rollBack();
            return null;
        } catch (\Throwable $th) {
            $this->conn->rollBack();
            LoggerHelper::logError('error ao cadastrar usuario ==> ' . $th->getMessage());
            return null;
        }
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
                "SELECT id_usuario, senha, nome, email, acesso, ativo, uuid, criado_em, atualizado_em
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

    public function user_exists(Usuario $user)
    {
        if (is_null($user)) {
            return false;
        }

        $stmt = $this->conn
            ->prepare(
                "SELECT * FROM " . self::TABLE .
                    " WHERE nome = :nome AND email = :email"
            );

        $stmt->bindParam(':email', $user->email, \PDO::PARAM_STR);
        $stmt->bindParam(':nome', $user->nome, \PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        return $stmt->fetch();
    }
}

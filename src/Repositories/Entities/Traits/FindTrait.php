<?php

namespace App\Repositories\Entities\Traits;

use App\Utils\LoggerHelper;
use PDO;

trait FindTrait
{
    protected ?PDO $conn;
    protected $model;

    protected function ensureRepositoryConstants(): void
    {
        if (!defined(static::class . '::TABLE') || !defined(static::class . '::CLASS_NAME')) {
            LoggerHelper::logError(static::class . ' deve definir as constantes TABLE e CLASS_NAME.');
            throw new \RuntimeException(static::class . ' deve definir as constantes TABLE e CLASS_NAME.');
        }
    }

    public function findById(int $id)
    {
        $this->ensureRepositoryConstants();

        $stmt = $this->conn->prepare("SELECT * FROM " . static::TABLE . " WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, static::CLASS_NAME);
        $register = $stmt->fetch();
        if (is_null($register)) {
            return null;
        }

        return $register;
    }

    public function findByUuid(string $uuid)
    {
        $this->ensureRepositoryConstants();

        $stmt = $this->conn->prepare("SELECT * FROM " . static::TABLE . " WHERE uuid = :uuid");
        $stmt->bindParam(':uuid', $uuid, \PDO::PARAM_STR);
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, static::CLASS_NAME);
        $register = $stmt->fetch();
        if (is_null($register)) {
            return null;
        }

        return $register;
    }
}

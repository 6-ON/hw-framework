<?php

namespace app\core\db;
use app\core\Application;

class Database
{
    public \PDO $pdo;

    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['usr'] ?? '';
        $psd = $config['psd'] ?? '';
        $this->pdo = new \PDO($dsn, $user, $psd);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $mig_dir = Application::$ROOT_DIR . '/migrations/';
        $files = scandir($mig_dir);
        $migrationsToApply = array_diff($files, $appliedMigrations);
        $newMigrations = [];
        foreach ($migrationsToApply as $migration) {
            if ($migration !== '.' && $migration !== '..') {

                $mig_path = $mig_dir . $migration;
                require_once $mig_path;
                $className = pathinfo($mig_path, PATHINFO_FILENAME);
                $instance = new $className();
                $this->log("Applying Migration $migration");
                $instance->up();
                $this->log("Applied Migration  $migration");

                $newMigrations[] = $migration;

            }
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("all Migrations are Applied");
        }
    }

    public function createMigrationsTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )  ENGINE=INNODB;");
    }


    private function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_COLUMN);

    }

    private function saveMigrations(array $migrations)
    {
        $migs = implode(',', array_map(fn($m) => "('$m')", $migrations));
        $stmt = $this->pdo->prepare("INSERT INTO migrations(migration) VALUES $migs");
        $stmt->execute();
    }


    public function prepare($sql): \PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    private function log($message)
    {
        echo "[" . date("Y-m-d H:i:s") . "] - " . $message . PHP_EOL;
    }

    public function exec($sql)
    {
        return $this->pdo->exec($sql);
    }


}
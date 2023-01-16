<?php

namespace sixon\hwFramework\db;

use sixon\hwFramework\Application;
use sixon\hwFramework\Model;
use PDO;
use PDOStatement;

abstract class DbModel extends Model
{
    private static function prepare($sql): PDOStatement
    {
        return Application::$app->db->prepare($sql);
    }

    abstract public static function tableName(): string;

    public static function ViewName(): string
    {
        return '';
    }

    abstract public function attributes(): array;

    abstract public static function primaryKey(): string;

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $SQL = "INSERT INTO $tableName(" . implode(',', $attributes) . ")
            VALUES(" . implode(',', $params) . ")";

        $stmt = self::prepare($SQL);

        foreach ($attributes as $attr) {
            $stmt->bindValue(":$attr", $this->{$attr});
        }
        $stmt->execute();
        return true;
    }

    public static function findOne($where,bool $useView = false)
    {
        $tableName = static::tableName();
        if ($useView){
            $tableName = static::ViewName();
        }
        $attributes = array_keys($where);
        $sql = "SELECT * FROM $tableName WHERE" . implode(' AND', array_map(fn($attr) => " $attr = :$attr", $attributes));
        $stmt = self::prepare($sql);
        foreach ($where as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $stmt->fetchObject(static::class);
    }

    public static function getAll(bool $useView = false, array $where = [])
    {
        if ($useView) {
            $tableName = static::ViewName();
        } else {
            $tableName = static::tableName();
        }
        $sql = "SELECT * FROM $tableName";
        if (!empty($where)) {
            $params = array_map(fn($prm) => "$prm = :$prm", array_keys($where));
            $sql .= (' WHERE ' . implode(' AND ', $params));
        }
        $stmt = self::prepare($sql);
        if (!empty($where)) {
            foreach ($where as $param => $value) {
                $stmt->bindValue(":$param",$value);
            }
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    public static function delete($where)
    {

        $tableName = static::tableName();
        $attributes = array_keys($where);
        $params = array_map(fn($attr) => "$attr = :$attr ", $attributes);
        $sql = "DELETE FROM $tableName WHERE " . implode('AND ', $params) . ';';
        $stmt = self::prepare($sql);

        foreach ($where as $key => $value) {
            $value = is_string($value) ? $value : strval($value);
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return true;
    }

    public static function update($columns, $where)
    {
        if (empty($columns)) {
            return false;
        }
        $tableName = static::tableName();
        $updateCols = array_keys($columns);
        $whereCols = array_keys($where);
        $sql = "UPDATE $tableName SET "
            . implode(", ", array_map(fn($col) => "$col = :$col", $updateCols))
            . " WHERE " . implode('AND ', array_map(fn($col) => "$col = :$col", $whereCols)) . ";";

        $stmt = self::prepare($sql);
        foreach ($columns as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        foreach ($where as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        return true;
    }

}

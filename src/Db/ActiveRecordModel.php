<?php

namespace Geega\Micro\Db;

use PDO;
use PDOExecption;

class ActiveRecordModel
{
    /**
     * @var \PDO
     */
    public $connect;

    public function __construct()
    {
        $charset = 'utf8mb4';
        $host = getenv('PDO_HOST');
        $dbname = getenv('PDO_DATABASE');
        $user = getenv('PDO_USER');
        $password = getenv('PDO_PASSWORD');

        if (getenv('PDO_CHARSET')) {
            $charset = getenv('PDO_CHARSET');
        }

        $connect = "mysql:dbname={$dbname};host={$host}";
        $this->connect = new PDO($connect, $user, $password);
        $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connect->exec("SET CHARACTER SET {$charset}");
        $this->connect->exec("SET names {$charset}");
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        if ($this->table) {
            return $this->table;
        }

        $array = explode('\\', get_called_class());

        return array_pop($array);
    }

    /**
     * @param string $sql
     * @param array $params
     *
     * @return array
     */
    public function execute($sql, $params = [])
    {
        $attributes = array_flip($this->attributes);
        $result = [];
        $statement = $this->connect->prepare($sql);
        $statement->execute($params);
        foreach ($statement as $row) {
            $result[] = array_filter(
                $row, function ($data) use ($attributes) {
                return isset($attributes[$data]);
            }, ARRAY_FILTER_USE_KEY
            );
        }
        return $result;
    }

    /**
     * @param $sqlQuery
     * @param array $placeHolders
     *
     * @return array|false
     */
    protected function bindExecute($sqlQuery, array $placeHolders = array())
    {
        $statement = $this->connect->prepare($sqlQuery);
        foreach ($placeHolders as $column => $value) {
            $statement->bindValue(':' . $column, $value);
        }
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @depricated please use findAll method.
     *
     * @return mixed
     */
    static public function getAll()
    {
        $model = new static;
        $result = $model->execute('SELECT * FROM ' . $model->table);

        return $result;
    }

    /**
     * @return mixed
     */
    static public function findAll()
    {
        return static::getAll();
    }

    /**
     * @param integer $id
     *  a
     * @return mixed
     */
    static public function findOne($id)
    {
        $model = new static;
        $result = $model->execute('SELECT * FROM ' . $model->getTableName() . ' WHERE ' . $model->key . ' = ?', [$id]);
        if (is_array($result)) {
            $result = current($result);
        }
        return $result;
    }

    /**
     * @param array $condition
     * @return mixed
     */
    static public function find(array $condition = array())
    {
        if (empty($condition)) {
            return static::findAll();
        }

        $model = new static;
        $placeHolders = [];

        $sqlQuery = 'SELECT * FROM ' . $model->getTableName() . ' WHERE  1=1 ';
        foreach ($condition as $param) {
            $key = $keyPlaceholder = $param[0];
            $value = $param[1];
            $sign = '=';
            if (isset($param[2])) {
                $sign = $param[2];
                if ($sign == '>=' || $sign == '>') {
                    $keyPlaceholder .= '_more';
                } elseif ($sign == '<=' || $sign == '<') {
                    $keyPlaceholder .= '_less';
                }
            }

            $sqlQuery .= " AND `{$key}` {$sign} :{$keyPlaceholder} ";
            $placeHolders[$keyPlaceholder] = $value;
        }

        return $model->bindExecute($sqlQuery, $placeHolders);
    }

    /**
     * @param array $data
     * @return string
     */
    static public function add($data)
    {
        try {
            $model = new static;
            $attributes = array_flip($model->attributes);
            $data = array_filter(
                $data, function ($data) use ($attributes) {
                return isset($attributes[$data]);
            }, ARRAY_FILTER_USE_KEY
            );
            $sql = 'INSERT INTO ' . $model->table . ' (';
            foreach ($data as $column => $value) {
                $sql .= '`' . $column . '`, ';
            }
            $sql = trim($sql, ', ');
            $sql .= ') VALUES (';
            foreach ($data as $column => $value) {
                $sql .= ':' . $column . ', ';
            }
            $sql = trim($sql, ', ');
            $sql .= ')';
            $statement = $model->connect->prepare($sql);
            foreach ($data as $column => $value) {
                $statement->bindValue(':' . $column, $value);
            }
            try {
                $statement->execute();
                return $model->connect->lastInsertId();
            } catch (PDOExecption $e) {
                $model->connect->rollback();
                return "Error" . $e->getMessage();
            }
        } catch (PDOExecption $e) {
            return "Error" . $e->getMessage();
        }
    }

    /**
     * @param array $data
     * @return int
     *
     * @throws \Exception
     */
    static public function updateByPk(array $data, $id = null)
    {
        $model = new static;
        $attributes = array_flip($model->attributes);

        $data = array_filter(
            $data, function ($data) use ($attributes) {
            return isset($attributes[$data]);
        }, ARRAY_FILTER_USE_KEY
        );

        $sql = 'UPDATE ' . $model->getTableName() . ' SET ';
        if (empty($data[$model->key])) {
            throw new \Exception('Not found primary key');
        }

        $primary_key = $id;
        if ($id === null && isset($data[$model->key])) {
            $primary_key = $data[$model->key];
            unset($data[$model->key]);
        } elseif ($id === null) {
            throw new \Exception( sprintf('Not found primary key (%s) in update data', $model->key));
        }

        foreach ($data as $key => $value) {
            $sql .= '' . $key . ' = :' . $key . ', ';
        }
        $sql = trim($sql, ', ');
        $sql .= ' WHERE ' . $model->key . ' = :' . $model->key;
        $statement = $model->connect->prepare($sql);
        foreach ($data as $column => $value) {
            $statement->bindValue(':' . $column, $value);
        }
        $statement->bindValue(':' . $model->key, $primary_key);
        try {
            $statement->execute();
            return $statement->rowCount();
        } catch (PDOExecption $e) {
            $model->connect->rollback();
            throw new \PDOException($e->getMessage());
        }
    }

    /**
     * @deprecated Due to BC break, please use deleteByPk method.
     *
     * @param mixed $pk
     * 
     * @return int
     */
    static public function delteByPk($pk)
    {
        return static::deleteByPk($pk);
    }

    /**
     * @param $pk
     * @return int
     */
    static public function deleteByPk($pk)
    {
        $model = new static;

        $sqlTpl = 'DELETE FROM `%s` WHERE `%s`.`%s` = :%s';
        $sql = sprintf($sqlTpl, $model->getTableName(), $model->getTableName(), $model->key, $model->key);

        $statement = $model->connect->prepare($sql);
        $statement->bindValue(':' . $model->key, $pk);

        try {
            $statement->execute();
            return $statement->rowCount();
        } catch (PDOExecption $e) {
            $model->connect->rollback();
            throw new \PDOException($e->getMessage());
        }
    }
}
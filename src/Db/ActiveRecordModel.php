<?php

namespace Geega\Micro\Db;

class ActiveRecordModel
{
    public $connect;

    public function __construct()
    {
        $charset = 'utf8mb4';
        $host = getenv('PDO_HOST');
        $dbname = getenv('PDO_DATABASE');
        $user = getenv('PDO_USER');
        $password = getenv('PDO_PASSWORD');

        if(getenv('PDO_CHARSET')) {
            $charset = getenv('PDO_CHARSET');
        }
        
        $connect = "mysql:dbname={$dbname};host={$host}";
        $this->connect = new \PDO($connect, $user, $password);
        $this->connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->connect->exec("SET CHARACTER SET {$charset}");
        $this->connect->exec("SET names {$charset}");
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        if($this->table) {
            return $this->table;
        }

        $array = explode('\\', get_called_class());

        return array_pop($array);
    }

    /**
     * @param string $sql
     * @param array  $params
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
     * @return mixed
     */
    static public function getAll()
    {
        $model = new static;
        $result = $model->execute('SELECT * FROM ' . $model->table);

        return $result;
    }

    /**
     * @param  integer $id
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
     * @param  array $data
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
            } catch (\PDOExecption $e) {
                $model->connect->rollback();
                return "Error" . $e->getMessage();
            }
        } catch (\PDOExecption $e) {
            return "Error" . $e->getMessage();
        }
    }

    /**
     * @param  array $data
     * @return string
     *
     * @throws \Exception
     */
    static public function updateByPk(array $data)
    {
        try {
            $model = new static;
            $attributes = array_flip($model->attributes);

            $data = array_filter(
                $data, function ($data) use ($attributes) {
                    return isset($attributes[$data]);
                }, ARRAY_FILTER_USE_KEY
            );

            $sql = 'UPDATE '.$model->getTableName().' SET ';
            if (empty($data[$model->key])) {
                throw new \Exception('Not found primary key');
            }
            $primary_key = $data[$model->key];
            unset($data[$model->key]);
            foreach ($data as $key => $value) {
                $sql .= ''.$key.' = :'.$key.', ';
            }
            $sql = trim($sql, ', ');
            $sql .= ' WHERE '.$model->key.' = :'.$model->key;
            $statement = $model->connect->prepare($sql);
            foreach ($data as $column => $value) {
                $statement->bindValue(':'.$column, $value);
            }
            $statement->bindValue(':'.$model->key, $primary_key);
            try {
                $statement->execute();
                return $model->connect->lastInsertId();
            } catch(\PDOExecption $e) {
                $model->connect->rollback();
                return "Error" . $e->getMessage();
            }
        } catch( \PDOExecption $e ) {
            return "Error" . $e->getMessage();
        }
    }
}
<?php

namespace core;

use PDO;

class Database
{
    public $connector;

    public $statment;

    public function __construct($config, $user = 'root', $password = null)
    {
        $this->connector = new PDO('mysql:'.http_build_query($config, '', ';'),
            $user,
            $password
        );
    }

    public function query($query, $param = [])
    {
        $this->statment = $this->connector->prepare($query);
        $this->statment->execute($param);

        return $this;

        // return $statment->fetch(PDO::FETCH_ASSOC);
    }



    public function find(array $params = [], int $fetchStyle = PDO::FETCH_ASSOC)
    {
        foreach ($params as $key => $value) {
            $this->statment->bindValue($key, $value);
        }

        $this->statment->execute();
        return $this->statment->fetch($fetchStyle);
    }

    public function get()
    {
        return $this->statment->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findOrFail()
    {
        $result = $this->statment->fetch(PDO::FETCH_ASSOC);

        if (! $result) {
            abort(404, 'Todo innexistante');
            exit();
        }

        return $result;
    }

    public function findColumn($columnIndex = 0)
    {
        return $this->statment->fetchColumn($columnIndex);
    }

    public function execute($query, $param = [])
    {
        $statment = $this->connector->prepare($query);

        return $statment->execute($param);
    }
}

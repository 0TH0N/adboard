<?php

namespace AdBoard\Config;

class DBConnection
{
    public static function getConnection()
    {
        $opt = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new \PDO('sqlite:db.sqlite', 'test', 'test', $opt);
        return $pdo;
    }
}

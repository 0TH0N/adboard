<?php

namespace AdBoard;

class DBHelper
{
    public static function fetchSQLAll($sql, $data = [])
    {
        $pdo = \AdBoard\Config\DBConnection::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        return $stmt->fetchAll();
    }

    public static function getSQLBoolResult($sql, $data = [])
    {
        $pdo = \AdBoard\Config\DBConnection::getConnection();
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($data);
    }
}

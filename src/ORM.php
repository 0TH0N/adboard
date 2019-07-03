<?php

namespace AdBoard;

class ORM
{
    public static function getDBConnection()
    {
        return \AdBoard\Config\DBConnection::getConnection();
    }

    public static function insertAd($ad)
    {
        $pdo = self::getDBConnection();
        $sql = "INSERT INTO ads (id, name, phone, ad_text, password, post_date) VALUES (:id, :name, :phone, :ad_text, :password, :post_date)";
        $stmt = $pdo->prepare($sql);
        $data = [
            'id' => $ad->getId(),
            'ad_text' => $ad->getAdText(),
            'name' => $ad->getUserName(),
            'password' => $ad->getPassword(),
            'phone' => $ad->getPhone(),
            'post_date' => $ad->getPostDate(),
        ];
        return $stmt->execute($data);
    }

    public static function readAd($id)
    {
        $pdo = self::getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM ads WHERE id = :id");
        $data = ['id' => $id];
        $stmt->execute($data);
        return $stmt->fetchAll();
    }

    public static function executeSQLQuery($sql, $data = [])
    {
        $pdo = self::getDBConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        return $stmt->fetchAll();
    }

    public static function getAllAds()
    {
        $pdo = self::getDBConnection();
        $stmt = $pdo->query("SELECT * FROM ads");
        $adsData = $stmt->fetchAll();
        usort($adsData, function ($a, $b) {
            return $b['post_date'] <=> $a['post_date'];
        });
        $ads = array_map(function ($ad) {
            return new Ad($ad['id'], $ad['ad_text'], $ad['name'], $ad['password'], $ad['phone'], $ad['post_date']);
        }, $adsData);
        return $ads;
    }
}

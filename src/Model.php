<?php

namespace AdBoard;

class Model
{
    public static function getDBConnection()
    {
        return \AdBoard\Config\DBConnection::getConnection();
    }

    public static function createTableAds()
    {
        $pdo = self::getDBConnection();
        $pdo->exec("CREATE TABLE ads (
        id bigint NOT NULL,
        ad_text text NOT NULL,
        name varchar(255) NOT NULL,
        password varchar(255) NOT NULL,
        phone varchar(255) NOT NULL,
        post_date datetime NOT NULL,
        PRIMARY KEY (id)
        )");
    }

    public static function destroyTableAds()
    {
        $pdo = self::getDBConnection();
        $pdo->exec("DROP TABLE IF EXISTS ads");
    }

    public static function fillAds()
    {
        $pdo = self::getDBConnection();
        $faker = \Faker\Factory::create();
        $sql = "INSERT INTO ads (id, name, phone, ad_text, password, post_date) VALUES (:id, :name, :phone, :ad_text, :password, :post_date)";
        $stmt = $pdo->prepare($sql);
        for ($i = 0; $i < 100; $i++) {
            $data = [
                'id' => hexdec(uniqid()),
                'ad_text' => $faker->text,
                'name' => $faker->name,
                'password' => crypt('123'),
                'phone' => $faker->phoneNumber,
                'post_date' => date_format($faker->dateTimeBetween('-1 week'), 'Y-m-d H:i:s'),
            ];
            echo $data;
            $stmt->execute($data);
        }
    }

    public static function getAds()
    {
        $pdo = self::getDBConnection();
        $stmt = $pdo->query("SELECT * FROM ads");
        $ads = $stmt->fetchAll();
        usort($ads, function ($a, $b) {
            return $b['post_date'] <=> $a['post_date'];
        });
        $adsObj = array_map(function ($ad) {
            return new Ad($ad['id'], $ad['ad_text'], $ad['name'], $ad['password'], $ad['phone'], $ad['post_date']);
        }, $ads);
        return $adsObj;
    }
}

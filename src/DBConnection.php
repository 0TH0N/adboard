<?php

namespace AdBoard;

class DBConnection
{
    public static function getConnection()
    {
        $opt = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new \PDO('mysql:host=localhost;dbname=test', 'test', 'test', $opt);
        return $pdo;
    }

    public static function createTableAds()
    {
        $pdo = self::getConnection();
        $pdo->exec("CREATE TABLE ads (
        id int NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        phone varchar(255) NOT NULL,
        ad_text text NOT NULL,
        password text NOT NULL,
        post_date datetime NOT NULL,
        PRIMARY KEY (id)
        )");
    }

    public static function destroyTableAds()
    {
        $pdo = self::getConnection();
        $pdo->exec("DROP TABLE ads");
    }

    public static function fillAds()
    {
        $pdo = self::getConnection();
        $faker = \Faker\Factory::create();
        $sql = "INSERT INTO ads (name, phone, ad_text, password, post_date) VALUES (:name, :phone, :ad_text, :password, :post_date)";
        $stmt = $pdo->prepare($sql);
        for ($i = 0; $i < 100; $i++) {
            $data = [
                'name' => $faker->name,
                'phone' => $faker->phoneNumber,
                'ad_text' => $faker->text,
                'password' => crypt('123'),
                'post_date' => date_format($faker->dateTimeBetween('-1 week'), 'Y-m-d H:i:s'),
            ];
            $stmt->execute($data);
        }
    }

    public static function getAds()
    {
        $pdo = self::getConnection();
        $stmt = $pdo->query("SELECT * FROM ads");
        $ads = $stmt->fetchAll();
        usort($ads, function ($a, $b) {
            return $b['post_date'] <=> $a['post_date'];
        });
        return $ads;
    }
}

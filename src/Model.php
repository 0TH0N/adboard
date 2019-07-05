<?php

namespace AdBoard;

class Model
{
    public static function executeSQLQuery($sql, $data = [])
    {
        $pdo = \AdBoard\Config\DBConnection::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        return $stmt->fetchAll();
    }

    public static function getAds($page = 1, $perPage = 12)
    {
        $offset = $perPage * ($page - 1);
        $sql = "SELECT * FROM ads ORDER BY post_date DESC LIMIT ? OFFSET ?";
        $data = [$perPage, $offset];
        $adsData = self::executeSQLQuery($sql, $data);
        $ads = collect($adsData)->map(function ($ad) {
            return new Ad($ad['id'], $ad['ad_text'], $ad['name'], $ad['password'], $ad['phone'], $ad['post_date']);
        });
        return $ads->all();
    }

    public static function getMaxPageNumber($perPage = 12)
    {
        $sql = "SELECT COUNT(id) FROM ads";
        $numberOfAds = self::executeSQLQuery($sql)[0]['COUNT(id)'];
        $maxPage = ceil($numberOfAds / $perPage);
        return $maxPage;
    }

    public static function createAd($adData, $innerDate = null)
    {
        $sql = "SELECT * FROM ads WHERE ad_text = ? AND name = ? AND phone = ?";
        $data = [$adData['ad-text'], $adData['user-name'], $adData['phone']];
        $existedAds = self::executeSQLQuery($sql, $data);
        
        if (!empty($existedAds[0])) {
            return false;
        }
        
        $id = hexdec(uniqid());
        $password = crypt($adData['password']);
        $date = $innerDate ?? date_format(\Carbon\Carbon::now(), 'Y-m-d H:i:s');
        $ad = new Ad($id, $adData['ad-text'], $adData['user-name'], $password, $adData['phone'], $date);
        $sql = "INSERT INTO ads (id, ad_text, name, password, phone, post_date) VALUES (?, ?, ?, ?, ?, ?)";
        $data = [
            $ad->getId(),
            $ad->getAdText(),
            $ad->getUserName(),
            $ad->getPassword(),
            $ad->getPhone(),
            $ad->getPostDate(),
        ];
        return self::executeSQLQuery($sql, $data);
    }

    public static function getAd($id)
    {
        $sql = "SELECT * FROM ads WHERE id = ?";
        $data = [$id];
        $adData = self::executeSQLQuery($sql, $data)[0];
        if (empty($adData)) {
            return false;
        }
        $ad = new Ad(
            $adData['id'],
            $adData['ad_text'],
            $adData['name'],
            $adData['password'],
            $adData['phone'],
            $adData['post_date']
        );
        return $ad;
    }

    public static function createTableAds()
    {
        $sql = "CREATE TABLE IF NOT EXISTS ads (
            id bigint NOT NULL,
            ad_text text NOT NULL,
            name varchar(255) NOT NULL,
            password varchar(255) NOT NULL,
            phone varchar(255) NOT NULL,
            post_date datetime NOT NULL,
            PRIMARY KEY (id)
            )";
        self::executeSQLQuery($sql);
    }

    public static function destroyTableAds()
    {
        $sql = "DROP TABLE IF EXISTS ads";
        self::executeSQLQuery($sql);
    }

    public static function fillAds($number)
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < $number; $i++) {
            self::createAd([
                'ad-text' => $faker->text,
                'user-name' => $faker->name,
                'password' => '123',
                'phone' => $faker->phoneNumber,
            ], date_format($faker->dateTimeBetween('-1 week'), 'Y-m-d H:i:s'));
        }
    }
}

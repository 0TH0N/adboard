<?php

namespace AdBoard;

class AdRepository
{
    public static function cryptPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function checkPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

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

    public static function getAds($page = 1, $perPage = 12)
    {
        self::createTableAds();
        $offset = $perPage * ($page - 1);
        $sql = "SELECT * FROM ads ORDER BY post_date DESC LIMIT ? OFFSET ?";
        $data = [$perPage, $offset];
        $adsData = self::fetchSQLAll($sql, $data);
        $ads = collect($adsData)->map(
            function ($ad) {
                return new Ad($ad['id'], $ad['ad_text'], $ad['name'], $ad['password'], $ad['phone'], $ad['post_date']);
            }
        );
        return $ads->all();
    }

    public static function getMaxPageNumber($perPage = 12)
    {
        $sql = "SELECT COUNT(id) FROM ads";
        $numberOfAds = self::fetchSQLAll($sql)[0]['COUNT(id)'];
        $maxPage = ceil($numberOfAds / $perPage);
        return $maxPage;
    }

    public static function createAd($adData, $innerDate = null)
    {
        $sql = "SELECT * FROM ads WHERE ad_text = ? AND name = ? AND phone = ?";
        $data = [$adData['adText'], $adData['userName'], $adData['phone']];
        $existedAds = self::fetchSQLAll($sql, $data);
        
        if (!empty($existedAds[0])) {
            return 'Similar ad is exist!';
        }
        
        $id = hexdec(uniqid());
        $password = self::cryptPassword($adData['password']);
        $date = $innerDate ?? date_format(\Carbon\Carbon::now(), 'Y-m-d H:i:s');
        $ad = new Ad($id, $adData['adText'], $adData['userName'], $password, $adData['phone'], $date);
        $sql = "INSERT INTO ads (id, ad_text, name, password, phone, post_date) VALUES (?, ?, ?, ?, ?, ?)";
        $data = [
            $ad->getId(),
            $ad->getAdText(),
            $ad->getUserName(),
            $ad->getPassword(),
            $ad->getPhone(),
            $ad->getPostDate(),
        ];
        return self::getSQLBoolResult($sql, $data) ? 'true' : 'false';
    }

    public static function getAd($id)
    {
        $sql = "SELECT * FROM ads WHERE id = ?";
        $data = [$id];
        $adData = self::fetchSQLAll($sql, $data);
        if (empty($adData)) {
            return false;
        }
        $ad = new Ad(
            $adData[0]['id'],
            $adData[0]['ad_text'],
            $adData[0]['name'],
            $adData[0]['password'],
            $adData[0]['phone'],
            $adData[0]['post_date']
        );
        return $ad;
    }

    public static function updateAd($newAdData)
    {
        $sql = "UPDATE ads SET ad_text = ?, name = ?, phone = ? WHERE id = ?";
        $data = [
            $newAdData['adText'],
            $newAdData['userName'],
            $newAdData['phone'],
            $newAdData['id']
        ];
        return self::getSQLBoolResult($sql, $data);
    }

    public static function deleteAd($id)
    {
        $sql = "DELETE FROM ads WHERE id = ?";
        $data = [$id];
        return self::getSQLBoolResult($sql, $data);
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
        return self::getSQLBoolResult($sql);
    }

    public static function destroyTableAds()
    {
        $sql = "DROP TABLE IF EXISTS ads";
        return self::getSQLBoolResult($sql);
    }

    public static function fillAds($number)
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < $number; $i++) {
            $adData = [
                'adText' => $faker->text,
                'userName' => $faker->name,
                'password' => '123456',
                'phone' => $faker->phoneNumber,
            ];
            $date = date_format($faker->dateTimeBetween('-1 week'), 'Y-m-d H:i:s');
            $queryBoolResult = self::createAd($adData, $date);
            if (!$queryBoolResult) {
                return false;
            }
        }
        return true;
    }
}

<?php

namespace AdBoard;

class Model
{
    public static function getAllAds()
    {
        return ORM::getAllAds();
    }

    public static function createAd($adData, $innerDate = null)
    {
        $id = hexdec(uniqid());
        $date = $innerDate !== null ? $innerDate : date_format(\Carbon\Carbon::now(), 'Y-m-d H:i:s');
        $hashPassword = crypt($adData['password']);
        $ad = new Ad($id, $adData['ad-text'], $adData['user-name'], $hashPassword, $adData['phone'], $date);
        return ORM::insertAd($ad);
    }

    public static function getAd($id)
    {
        $adData = ORM::readAd($id)[0];
        $ad = new Ad($adData['id'], $adData['ad_text'], $adData['name'],
            $adData['password'], $adData['phone'], $adData['post_date']);
        return $ad;
    }

    public static function createTableAds()
    {
        $sql = "CREATE TABLE ads (
            id bigint NOT NULL,
            ad_text text NOT NULL,
            name varchar(255) NOT NULL,
            password varchar(255) NOT NULL,
            phone varchar(255) NOT NULL,
            post_date datetime NOT NULL,
            PRIMARY KEY (id)
            )";
        return ORM::executeSQLQuery($sql);
    }

    public static function destroyTableAds()
    {
        $sql = "DROP TABLE IF EXISTS ads";
        return ORM::executeSQLQuery($sql);
    }

    public static function fillAds()
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 100; $i++) {
            $requestResult = self::createAd([
                'ad-text' => $faker->text,
                'user-name' => $faker->name,
                'password' => '123',
                'phone' => $faker->phoneNumber,
            ], date_format($faker->dateTimeBetween('-1 week'), 'Y-m-d H:i:s'));
            if (!$requestResult) {
                return false;
            }
        }
        return true;
    }
}

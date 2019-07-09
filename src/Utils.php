<?php

namespace AdBoard\Utils;

function cryptPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}

function checkPassword($password, $hash)
{
    return password_verify($password, $hash);
}

function fillAds($number)
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
        $queryBoolResult = \AdBoard\AdRepository::createAd($adData, $date);
        if (!$queryBoolResult) {
            return false;
        }
    }
    return true;
}

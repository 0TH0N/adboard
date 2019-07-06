<?php

namespace AdBoard\Tests;

use PHPUnit\Framework\TestCase;
use AdBoard\AdRepository;

class AdRepositoryTest extends TestCase
{
    public function testTableFunctions()
    {
        AdRepository::destroyTableAds();
        AdRepository::createTableAds();
        $ads = AdRepository::getAds();
        $this->assertEmpty($ads);
    }

    public function testOtherFunctions()
    {
        AdRepository::destroyTableAds();
        AdRepository::createTableAds();
        $adData = [
            'adText' => '123',
            'userName' => '123',
            'password' => '123',
            'phone' => '123'
        ];
        AdRepository::createAd($adData);
        $ads = AdRepository::getAds();
        $this->assertEquals(1, count($ads));

        $id = $ads[0]->getId();
        $ad = AdRepository::getAd($id);
        $this->assertEquals($ad->getId(), $id);

        $maxPage = AdRepository::getMaxPageNumber();
        $this->assertEquals(1, $maxPage);

        AdRepository::destroyTableAds();
        AdRepository::createTableAds();
        $ad = AdRepository::getAd($id);
        $this->assertFalse($ad);

        $maxPage = AdRepository::getMaxPageNumber();
        $this->assertEquals(0, $maxPage);

        AdRepository::createAd($adData);
        $ads = AdRepository::getAds();
        $id = $ads[0]->getId();
        $newAdData = [
            'id' => $id,
            'adText' => '555',
            'userName' => '555',
            'phone' => '555'
        ];
        AdRepository::updateAd($newAdData);
        $ad = AdRepository::getAds()[0];
        $this->assertEquals('555', $ad->getUserName());
    }
}

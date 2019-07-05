<?php

namespace AdBoard\Tests;

use PHPUnit\Framework\TestCase;
use AdBoard\Model;

class ModelTest extends TestCase
{
    public function testTableFunctions()
    {
        Model::destroyTableAds();
        Model::createTableAds();
        $ads = Model::getAds();
        $this->assertEmpty($ads);
    }

    public function testOtherFunctions()
    {
        Model::destroyTableAds();
        Model::createTableAds();
        $adData = [
            'ad-text' => '123',
            'user-name' => '123',
            'password' => '123',
            'phone' => '123'
        ];
        Model::createAd($adData);
        $ads = Model::getAds();
        $this->assertEquals(1, count($ads));

        $id = $ads[0]->getId();
        $ad = Model::getAd($id);
        $this->assertEquals($ad->getId(), $id);

        $maxPage = Model::getMaxPageNumber();
        $this->assertEquals(1, $maxPage);

        Model::destroyTableAds();
        Model::createTableAds();
        $ad = Model::getAd($id);
        $this->assertFalse($ad);

        $maxPage = Model::getMaxPageNumber();
        $this->assertEquals(0, $maxPage);
    }
}

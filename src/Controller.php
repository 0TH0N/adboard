<?php

namespace AdBoard;

class Controller
{

    public static function index()
    {
        $ads = \AdBoard\Model::getAds();
        return $ads;
    }

    public static function remakeTable()
    {
        \AdBoard\Model::destroyTableAds();
        \AdBoard\Model::createTableAds();
        \AdBoard\Model::fillAds();
    }
}

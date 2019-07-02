<?php

namespace AdBoard\Tests;

require __DIR__ . "/../public/index.php";
require __DIR__ . "/../vendor/autoload.php";

use PHPUnit\Framework\TestCase;

class PublicIndexTest extends TestCase
{
    protected $client;

    public function setUp(): void
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:9002',
        ]);
    }

    public function testPublicIndex()
    {
        $responce = $this->client->get('/');
        $this->assertEquals($responce->getStatusCode(), 200);
        $this->assertTrue(strpos($responce->getBody(), 'Advertisment board') !== false);
    }
}

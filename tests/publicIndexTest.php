<?php

namespace AdBoard\Tests;

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
        $this->assertTrue(strpos($responce->getBody(), 'Advertisement board') !== false);
    }

    public function testNewAdForm()
    {
        $responce = $this->client->get("/adform/new");
        $this->assertEquals($responce->getStatusCode(), 200);
        $this->assertTrue(strpos($responce->getBody(), 'Make new advertisement') !== false);
    }
}

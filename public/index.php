<?php

namespace AdBoard\publicIndex;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/DBConnection.php';

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new \Slim\App($configuration);

$container = $app->getContainer();
$container['renderer'] = new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');

$app->get('/', function ($request, $response) {
    $ads = \AdBoard\DBConnection::getAds();
    $params = [
        'ads' => $ads,
    ];
    return $this->renderer->render($response, 'index.phtml', $params);
});

$app->get('/destroy-table', function ($request, $response) {
    \AdBoard\DBConnection::destroyTableAds();
    return $response->withRedirect('/', 301);
});

$app->get('/create-table', function ($request, $response) {
    \AdBoard\DBConnection::createTableAds();
    \AdBoard\DBConnection::fillAds();
    return $response->withRedirect('/', 301);
});

$app->run();

<?php

namespace AdBoard\Index;

require_once __DIR__ . '/../vendor/autoload.php';

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new \Slim\App($configuration);

$container = $app->getContainer();
$container['renderer'] = new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');

$app->get('/', function ($request, $response) {
    $ads = \AdBoard\Controller::index();
    $params = [
        'ads' => $ads,
    ];
    return $this->renderer->render($response, 'index.phtml', $params);
});

$app->patch('/remake-table', function ($request, $response) {
    \AdBoard\Controller::remakeTable();
    return $response->withRedirect('/', 301);
});

$app->run();

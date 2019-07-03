<?php

namespace AdBoard\Index;

require_once __DIR__ . '/../vendor/autoload.php';

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

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

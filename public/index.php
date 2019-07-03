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
    $ads = \AdBoard\Model::getAllAds();
    $params = [
        'ads' => $ads,
    ];
    return $this->renderer->render($response, 'index.phtml', $params);
});

$app->get('/remake-table', function ($request, $response) {
    \AdBoard\Model::destroyTableAds();
    \AdBoard\Model::createTableAds();
    \AdBoard\Model::fillAds();
    return $response->withRedirect('/', 301);
});

$app->get('/new-ad-form', function ($request, $response) {
    return $this->renderer->render($response, 'new-ad-form.phtml');
});

$app->post('/new-ad-post', function ($request, $response) {
    $adData = $request->getParsedBodyParam('ad');
    $requestResult = \AdBoard\Model::createAd($adData);
    return $response->withRedirect('/', 301);
});

$app->get('/show-ad/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $ad = \AdBoard\Model::getAd($id);
    $params = ['ad' => $ad];
    return $this->renderer->render($response, 'show-ad.phtml', $params);
});

$app->run();

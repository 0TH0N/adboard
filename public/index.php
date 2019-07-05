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

\session_start();

$app = new \Slim\App($configuration);

$container = $app->getContainer();
$container['renderer'] = new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');

$app->get('/', function ($request, $response) {
    $page = $request->getQueryParam('page', 1);
    $ads = \AdBoard\Model::getAds($page);
    $maxPage = \AdBoard\Model::getMaxPageNumber();
    $params = [
        'ads' => $ads,
        'page' => $page,
        'maxPage' => $maxPage
    ];
    return $this->renderer->render($response, 'index.phtml', $params);
});

$app->delete('/remake-table', function ($request, $response) {
    \AdBoard\Model::destroyTableAds();
    \AdBoard\Model::createTableAds();
    return $response->withRedirect('/', 301);
});

$app->post('/make-fake-ads', function ($request, $response) {
    $number = $request->getParsedBodyParam('count');
    \AdBoard\Model::fillAds($number);
    return $response->withRedirect('/', 301);
});

$app->get('/adform/new', function ($request, $response) {
    $prevPage = $request->getQueryParam('prevpage', 1);
    $params = ['prevPage' => $prevPage];
    return $this->renderer->render($response, 'new-ad-form.phtml', $params);
});

$app->post('/adform', function ($request, $response) {
    $adData = $request->getParsedBodyParam('ad');
    $requestResult = \AdBoard\Model::createAd($adData);
    return $response->withRedirect('/', 301);
});

$app->get('/ads/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $prevPage = $request->getQueryParam('prevpage', 1);
    $ad = \AdBoard\Model::getAd($id);
    $params = ['ad' => $ad, 'prevPage' => $prevPage];
    return $this->renderer->render($response, 'show-ad.phtml', $params);
});

$app->run();

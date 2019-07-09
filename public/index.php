<?php

namespace AdBoard\Index;

require_once __DIR__ . '/../vendor/autoload.php';
use \AdBoard\AdRepository;
use \AdBoard\Validator;
use \AdBoard\Utils;

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
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

// Start main page
$app->get('/', \AdBoard\HomeController::class . ':home');

// Destroy database table ads and create new
$app->delete('/remake-table', \AdBoard\TableAdsController::class . ':remakeTable');

// Fill database fake ads
$app->post('/make-fake-ads', \AdBoard\TableAdsController::class . ':makeFakeAds');

// Form for new ad
$app->get('/adform/new', \AdBoard\NewAdController::class . ':showFormForNewAd');

// Validation and creation new ad
$app->post('/adform', \AdBoard\NewAdController::class . ':makeNewAd');

// Show specific ad
$app->get('/ads/{id}', \AdBoard\AdController::class . ':showAd');

// Show form for edit specific ad
$app->get('/ads/edit/{id}', \AdBoard\AdController::class . ':showEditFormForAd');

// Route for update specific ad
$app->patch('/ads/edit', \AdBoard\AdController::class . ':updateAd');

// Route for delete specific ad
$app->delete('/ads/delete/{id}', \AdBoard\AdController::class . ':deleteAd');

$app->run();

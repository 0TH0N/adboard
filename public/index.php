<?php

namespace AdBoard\Index;

require_once __DIR__ . '/../vendor/autoload.php';
use \AdBoard\AdRepository;
use \AdBoard\Validator;

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

$app->get('/', function ($request, $response) {
    $page = $request->getQueryParam('page', 1);
    $ads = AdRepository::getAds($page);
    $maxPage = AdRepository::getMaxPageNumber();
    $flashMessages = $this->flash->getMessages();
    $params = [
        'ads' => $ads,
        'page' => $page,
        'maxPage' => $maxPage,
        'flashMessages' => $flashMessages
    ];

    if (($request->hasHeader('X-Status-Reason')) && ($request->getHeader('X-Status-Reason')) === 'Validation failed') {
        $response->withStatus(403);
        $response->withHeader('X-Status-Reason', 'Validation failed');
    }
    
    return $this->renderer->render($response, 'index.phtml', $params);
});

$app->delete('/remake-table', function ($request, $response) {
    AdRepository::destroyTableAds();
    AdRepository::createTableAds();
    return $response->withRedirect('/', 301);
});

$app->post('/make-fake-ads', function ($request, $response) {
    $number = $request->getParsedBodyParam('count');
    $violations = Validator::validateNumberFakeAds($number);
    $allViolations = [];
    foreach ($violations as $violation) {
        $allViolations[] = $violation;
    }

    if (!empty($allViolations)) {
        $this->flash->addMessage('error', "Minimum 1 fake ad, maximum 30 fake ads for one time! Inputed {$number}.");
        $response->withHeader('X-Status-Reason', 'Validation failed');
        return $response->withRedirect('/', 301);
    }

    if (AdRepository::fillAds($number)) {
        $this->flash->addMessage('success', 'Successful added fake ads!');
    } else {
        $this->flash->addMessage('error', 'Error(-s) occured during making fake ads');
    }
    return $response->withRedirect('/', 301);
});

$app->get('/adform/new', function ($request, $response) {
    $previousPage = $request->getQueryParam('previousPage', 1);
    $params = ['previousPage' => $previousPage];
    return $this->renderer->render($response, 'new-ad-form.phtml', $params);
});

$app->post('/adform', function ($request, $response) {
    $adData = $request->getParsedBodyParam('ad');
    $previousPage = $request->getParsedBodyParam('previousPage');
    $violations = Validator::validateAdData($adData);
    $allViolations = [];
    foreach ($violations as $group) {
        foreach ($group as $violation) {
            $allViolations[] = $violation;
        }
    }
    
    if (empty($allViolations)) {
        $requestResult = AdRepository::createAd($adData);
        switch ($requestResult) {
            case 'Similar ad is exist!':
                $this->flash->addMessage('error', 'Similar ad is exist!');
                break;
    
            case 'true':
                $this->flash->addMessage('success', 'Ad added successful!');
                break;
            
            case 'false':
                $this->flash->addMessage('error', 'Some error(-s) occured during adding ad!');
                break;
        }
        return $response->withRedirect('/', 301);
    }

    $params = [
        'adData' => $adData,
        'errors' => $violations,
        'previousPage' => $previousPage
    ];
    $response->withStatus(403);
    $response->withHeader('X-Status-Reason', 'Validation failed');
    return $this->renderer->render($response, 'new-ad-form.phtml', $params);
});

$app->get('/ads/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $previousPage = $request->getQueryParam('previousPage', 1);
    $ad = AdRepository::getAd($id);
    $params = ['ad' => $ad, 'previousPage' => $previousPage];
    return $this->renderer->render($response, 'show-ad.phtml', $params);
});

$app->run();

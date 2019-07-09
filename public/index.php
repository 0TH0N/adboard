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
$app->get('/', function ($request, $response) {
    $page = $request->getQueryParam('page', 1);
    $maxPage = AdRepository::getMaxPageNumber();
    $page = $page < $maxPage ? $page : $maxPage;
    $ads = AdRepository::getAds($page);
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

// Destroy database table ads and create new
$app->delete('/remake-table', function ($request, $response) {
    AdRepository::destroyTableAds();
    AdRepository::createTableAds();
    return $response->withRedirect('/', 301);
});

// Fill database fake ads
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

    if (Utils\fillAds($number)) {
        $this->flash->addMessage('success', 'Successful added fake ads!');
    } else {
        $this->flash->addMessage('error', 'Error(-s) occured during making fake ads');
    }
    return $response->withRedirect('/', 301);
});

// Form for new ad
$app->get('/adform/new', function ($request, $response) {
    $page = $request->getQueryParam('page', 1);
    $params = ['page' => $page];
    return $this->renderer->render($response, 'new-ad-form.phtml', $params);
});

// Validation and creation new ad
$app->post('/adform', function ($request, $response) {
    $adData = $request->getParsedBodyParam('ad');
    $page = $request->getParsedBodyParam('page');
    $violations = Validator::validateAdData($adData);
    $allViolations = [];
    foreach ($violations as $group) {
        foreach ($group as $violation) {
            $allViolations[] = $violation;
        }
    }
    
    // If validation OK go creation ad
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

    // If validation NOT OK return to repeat filling form
    $params = [
        'adData' => $adData,
        'errors' => $violations,
        'page' => $page
    ];
    $response->withStatus(403);
    $response->withHeader('X-Status-Reason', 'Validation failed');
    return $this->renderer->render($response, 'new-ad-form.phtml', $params);
});

// Show specific ad
$app->get('/ads/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $page = $request->getQueryParam('page', 1);
    $wrongPassword = $request->getQueryParam('wrongPassword', false);
    $ad = AdRepository::getAd($id);
    $params = [
        'ad' => $ad,
        'page' => $page,
        'wrongPassword' => $wrongPassword
    ];
    return $this->renderer->render($response, 'show-ad.phtml', $params);
});

// Show form for edit specific ad
$app->get('/ads/edit/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $page = $request->getQueryParam('page', 1);
    $ad = AdRepository::getAd($id);
    $adData = [
        'id' => $id,
        'adText' => $ad->getAdText(),
        'userName' => $ad->getUserName(),
        'phone' => $ad->getPhone(),
    ];
    $params = ['adData' => $adData, 'page' => $page];
    return $this->renderer->render($response, 'show-ad-edit.phtml', $params);
});

// Route for update specific ad
$app->patch('/ads/edit', function ($request, $response) {
    $adData = $request->getParsedBodyParam('ad');
    $ad = AdRepository::getAd($adData['id']);
    $wrongPassword = true;
    if (Utils\checkPassword($adData['password'], $ad->getPassword())) {
        $wrongPassword = false;
    }
    $adData['password'] = '123456';
    $page = $request->getParsedBodyParam('page', 1);
    $violations = Validator::validateAdData($adData);
    $allViolations = [];
    foreach ($violations as $group) {
        foreach ($group as $violation) {
            $allViolations[] = $violation;
        }
    }
    
    // If validation OK and password is right go to updating procudure
    if (empty($allViolations) && (!$wrongPassword)) {
        $requestResult = AdRepository::updateAd($adData);
        switch ($requestResult) {
            case 'true':
                $this->flash->addMessage('success', 'Ad updated successful!');
                break;

            case 'false':
                $this->flash->addMessage('error', 'Some error(-s) occured during updating ad!');
                break;
        }
        return $response->withRedirect("/?page={$page}", 301);
    }
    
    // If updating is not OK return to form and repeat filling form
    $params = [
        'adData' => $adData,
        'errors' => $violations,
        'wrongPassword' => $wrongPassword,
        'page' => $page
    ];
    $response->withStatus(403);
    $response->withHeader('X-Status-Reason', 'Validation failed');
    return $this->renderer->render($response, 'show-ad-edit.phtml', $params);
});

// Route for delete specific ad
$app->delete('/ads/delete/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $ad = AdRepository::getAd($id);
    $page = $request->getParsedBodyParam('page', 1);
    $password = $request->getParsedBodyParam('password');

    // If password is right then delete ad
    if (Utils\checkPassword($password, $ad->getPassword())) {
        $resultQueryBool = AdRepository::deleteAd($id);
        if ($resultQueryBool) {
            $this->flash->addMessage('success', 'Ad deleted successful!');
        } else {
            $this->flash->addMessage('error', 'Something went wrong during ad deletion!');
        }
        return $response->withRedirect("/?page={$page}", 301);
    }

    // If wrong password redirect to show this ad page for new attempt
    return $response->withRedirect("/ads/{$id}?page={$page}&wrongPassword=1", 301);
});

$app->run();

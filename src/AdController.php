<?php

namespace AdBoard;

class AdController
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function showAd($request, $response, $args)
    {
        $id = $args['id'];
        $page = $request->getQueryParam('page', 1);
        $wrongPassword = $request->getQueryParam('wrongPassword', false);
        $ad = AdRepository::getAd($id);
        $params = [
            'ad' => $ad,
            'page' => $page,
            'wrongPassword' => $wrongPassword
        ];
        return $this->container->renderer->render($response, 'show-ad.phtml', $params);
    }

    public function showEditFormForAd($request, $response, $args)
    {
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
        return $this->container->renderer->render($response, 'show-ad-edit.phtml', $params);
    }

    public function updateAd($request, $response)
    {
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
                    $this->container->flash->addMessage('success', 'Ad updated successful!');
                    break;
    
                case 'false':
                    $this->container->flash->addMessage('error', 'Some error(-s) occured during updating ad!');
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
        return $this->container->renderer->render($response, 'show-ad-edit.phtml', $params);
    }

    public function deleteAd($request, $response, $args)
    {
        $id = $args['id'];
        $ad = AdRepository::getAd($id);
        $page = $request->getParsedBodyParam('page', 1);
        $password = $request->getParsedBodyParam('password');
    
        // If password is right then delete ad
        if (Utils\checkPassword($password, $ad->getPassword())) {
            $resultQueryBool = AdRepository::deleteAd($id);
            if ($resultQueryBool) {
                $this->container->flash->addMessage('success', 'Ad deleted successful!');
            } else {
                $this->container->flash->addMessage('error', 'Something went wrong during ad deletion!');
            }
            return $response->withRedirect("/?page={$page}", 301);
        }
    
        // If wrong password redirect to show this ad page for new attempt
        return $response->withRedirect("/ads/{$id}?page={$page}&wrongPassword=1", 301);
    }
}

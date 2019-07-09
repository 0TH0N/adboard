<?php

namespace AdBoard;

class NewAdController
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function showFormForNewAd($request, $response)
    {
        $page = $request->getQueryParam('page', 1);
        $params = ['page' => $page];
        return $this->container->renderer->render($response, 'new-ad-form.phtml', $params);
    }

    public function makeNewAd($request, $response)
    {
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
                    $this->container->flash->addMessage('error', 'Similar ad is exist!');
                    break;
        
                case 'true':
                    $this->container->flash->addMessage('success', 'Ad added successful!');
                    break;
                
                case 'false':
                    $this->container->flash->addMessage('error', 'Some error(-s) occured during adding ad!');
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
        return $this->container->renderer->render($response, 'new-ad-form.phtml', $params);
    }
}

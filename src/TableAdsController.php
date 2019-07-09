<?php

namespace AdBoard;

class TableAdsController
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function remakeTable($request, $response)
    {
        AdRepository::destroyTableAds();
        AdRepository::createTableAds();
        return $response->withRedirect('/', 301);
    }

    public function makeFakeAds($request, $response)
    {
        $number = $request->getParsedBodyParam('count');
        $violations = Validator::validateNumberFakeAds($number);
        $allViolations = [];
        foreach ($violations as $violation) {
            $allViolations[] = $violation;
        }
    
        if (!empty($allViolations)) {
            $this->container->flash->addMessage('error', "Minimum 1 fake ad, maximum 30 fake ads for one time! Inputed {$number}.");
            $response->withHeader('X-Status-Reason', 'Validation failed');
            return $response->withRedirect('/', 301);
        }
    
        if (Utils\fillAds($number)) {
            $this->container->flash->addMessage('success', 'Successful added fake ads!');
        } else {
            $this->container->flash->addMessage('error', 'Error(-s) occured during making fake ads');
        }
        return $response->withRedirect('/', 301);
    }
}

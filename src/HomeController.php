<?php

namespace AdBoard;

class HomeController
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function home($request, $response)
    {
        $page = $request->getQueryParam('page', 1);
        $maxPage = AdRepository::getMaxPageNumber();
        $page = $page < $maxPage ? $page : $maxPage;
        $ads = AdRepository::getAds($page);
        $flashMessages = $this->container->flash->getMessages();
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
        
        return $this->container->renderer->render($response, 'index.phtml', $params);
    }
}

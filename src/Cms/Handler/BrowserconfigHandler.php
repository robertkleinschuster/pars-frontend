<?php

namespace Pars\Frontend\Cms\Handler;

use Laminas\Diactoros\Response\XmlResponse;
use Pars\Frontend\Base\Handler\FrontendHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class BrowserconfigHandler
 * @package Pars\Frontend\Cms\Handler
 */
class BrowserconfigHandler extends FrontendHandler
{
    /***
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->initDefaultVars($request);
        return new XmlResponse($this->renderer->render('meta::browserconfig'));
    }
}

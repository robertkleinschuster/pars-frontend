<?php


namespace Pars\Frontend\Cms\Handler;


use Laminas\Diactoros\Response\TextResponse;
use Pars\Frontend\Base\Handler\FrontendHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RobotsHandler extends FrontendHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->initDefaultVars($request);
        return new TextResponse($this->renderer->render('meta::robots'));
    }

}

<?php


namespace Pars\Frontend\Cms\Handler;


use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\XmlResponse;
use Pars\Frontend\Base\Handler\FrontendHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SitemapHandler extends FrontendHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->initDefaultVars($request);
        return new XmlResponse($this->renderer->render('meta::sitemap'));
    }

}

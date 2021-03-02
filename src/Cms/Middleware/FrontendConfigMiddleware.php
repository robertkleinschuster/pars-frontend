<?php


namespace Pars\Frontend\Cms\Middleware;


use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Helper\Template\TemplateVariableContainer;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Pars\Core\Bundles\BundlesMiddleware;
use Pars\Frontend\Cms\Helper\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FrontendConfigMiddleware implements MiddlewareInterface
{
    protected Config $config;
    protected array $applicationConfig;

    /**
     * FrontendConfigMiddleware constructor.
     * @param Config $config
     * @param array $applicationConfig
     */
    public function __construct(Config $config, array $applicationConfig)
    {
        $this->config = $config;
        $this->applicationConfig = $applicationConfig;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $container = $request->getAttribute(TemplateVariableContainer::class, new TemplateVariableContainer());
        $vars = [];
        $vars['hash'] = $request->getAttribute(BundlesMiddleware::class)['hash'] ?? '';
        $vars['brand'] = $this->config->get('frontend.brand');
        $vars['keywords'] = $this->config->get('frontend.keywords');
        $vars['author'] = $this->config->get('frontend.author');
        $vars['static'] = $this->config->get('asset.domain');
        $vars['key'] = $this->config->get('asset.key');
        $vars['domain'] = $this->config->get('frontend.domain');
        $vars['charset'] = $this->config->get('frontend.charset');
        $vars['favicon'] = $this->config->get('frontend.favicon');
        $vars['logo'] = $this->config->get('frontend.logo');
        $vars['googleKey'] = $this->config->get('frontend.google-key');
        $vars['googleMapsKey'] = $this->config->get('frontend.google-maps-key');
        $vars['color'] = $this->config->get('frontend.color');
        $vars['data-privacy-email'] = $this->config->get('frontend.data-privacy-email');
        $vars['host'] = $request->getUri()->getHost();
        return $handler->handle($request
            ->withAttribute(Config::class, $this->config)
            ->withAttribute(TemplateVariableContainer::class, $container->merge($vars)));
    }

}

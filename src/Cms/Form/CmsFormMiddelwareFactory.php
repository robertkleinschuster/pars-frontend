<?php


namespace Pars\Frontend\Cms\Form;


use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessagesInterface;
use Mezzio\Helper\Template\TemplateVariableContainer;
use Mezzio\Session\SessionMiddleware;
use Pars\Core\Database\DatabaseMiddleware;
use Pars\Core\Localization\LocaleInterface;
use Pars\Core\Logging\LoggingMiddleware;
use Pars\Core\Translation\TranslatorMiddleware;
use Pars\Frontend\Cms\Helper\Config;
use Pars\Model\Article\Translation\ArticleTranslationBeanFinder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CmsFormMiddelwareFactory
{

    /**
     * @param ContainerInterface $container
     * @return CmsFormMiddelware
     */
    public function __invoke(ContainerInterface $container)
    {
        return new CmsFormMiddelware(new Config($container->get(AdapterInterface::class)));
    }
}

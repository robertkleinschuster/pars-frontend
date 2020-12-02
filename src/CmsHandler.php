<?php


namespace Pars\Frontend;


use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Helper\UrlHelper;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Minifier\TinyMinify;

use Pars\Core\Localization\LocaleInterface;
use Pars\Core\Localization\LocalizationMiddleware;
use Pars\Core\Translation\TranslatorMiddleware;
use Pars\Model\Cms\Menu\CmsMenuBeanFinder;
use Pars\Model\Cms\Page\CmsPageBeanFinder;
use Pars\Model\Config\ConfigBeanFinder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CmsHandler implements \Psr\Http\Server\RequestHandlerInterface
{


    private TemplateRendererInterface $renderer;

    private $urlHelper;

    /**
     * CmsHandler constructor.
     * @param TemplateRendererInterface $renderer
     */
    public function __construct(TemplateRendererInterface $renderer, UrlHelper $urlHelper)
    {
        $this->renderer = $renderer;
        $this->urlHelper = $urlHelper;
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $adapter = $request->getAttribute(\Pars\Core\Database\DatabaseMiddleware::ADAPTER_ATTRIBUTE);
        $locale = $request->getAttribute(LocaleInterface::class);
        $translator = $request->getAttribute(TranslatorMiddleware::TRANSLATOR_ATTRIBUTE);
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $code = $request->getAttribute('code', '/');
        $placeholder = new CmsPlaceholder($locale->getLocale_Code());
        $placeholder->setTranslator($translator);

        $menuFinder = new CmsMenuBeanFinder($adapter);
        $menuFinder->setCmsMenuState_Code('active');
        $menuFinder->findByLocaleWithFallback($locale->getLocale_Code(), 'de_AT');
        $configFinder = new ConfigBeanFinder($adapter);
        $configFinder->setConfig_Code('asset.domain');
        $config = $configFinder->getBean();

        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'menu', $menuFinder->getBeanListDecorator());
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'code', $code);
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'static', $config->get('Config_Value'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'placeholder', $placeholder);
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'url', function ($code) {
            if (trim($code) == '/' || trim($code) == '') {
                return $this->urlHelper->generate(null, ['code' => null]);
            }
            return $this->urlHelper->generate(null, ['code' => str_replace('/', '', $code)]);
        });
        $pageFinder = new CmsPageBeanFinder($adapter);
        $pageFinder->setCmsPageState_Code('active');
        $pageFinder->setArticleTranslation_Code($code);
        if ($pageFinder->findByLocaleWithFallback($locale->getLocale_Code(), 'de_AT') === 1) {
            $bean = $pageFinder->getBean();
                $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'voted', $session->get('voted'
                .$bean->get('Article_Code')));

            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'bean', $bean);
            return new HtmlResponse(TinyMinify::html($this->renderer->render($bean->get('CmsPageType_Template'))));
        }
        return new HtmlResponse(TinyMinify::html($this->renderer->render('error::404')), 404);
    }

}

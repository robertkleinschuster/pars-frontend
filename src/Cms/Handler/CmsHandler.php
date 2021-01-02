<?php


namespace Pars\Frontend\Cms\Handler;


use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Helper\UrlHelper;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Pars\Core\Localization\LocaleInterface;
use Pars\Core\Translation\TranslatorMiddleware;
use Pars\Frontend\Cms\Form\Base\AbstractForm;
use Pars\Frontend\Cms\Helper\CmsPlaceholder;
use Pars\Frontend\Cms\Helper\Config;
use Pars\Model\Cms\Menu\CmsMenuBeanFinder;
use Pars\Model\Cms\Page\CmsPageBeanFinder;
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
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        $code = $request->getAttribute('code', '/');
        $config = new Config($adapter);
        $placeholder = new CmsPlaceholder($locale->getLocale_Code());
        $placeholder->setTranslator($translator);

        $menuFinder = new CmsMenuBeanFinder($adapter);
        $menuFinder->setCmsMenuState_Code('active');
        $menuFinder->order(['CmsMenuType_Code']);
        $menuFinder->setCmsMenu_ID_Parent(null);
        $menuFinder->findByLocaleWithFallback($locale->getLocale_Code(), 'de_AT');
        $menuFinder->addLinkedFinder(new CmsMenuBeanFinder($adapter), 'Menu_BeanList', 'CmsMenu_ID', 'CmsMenu_ID_Parent');
        if ($session->has('poll_name')) {
            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'name', $session->get('poll_name'));
        } else {
            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'name', '');
        }
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'menu', $menuFinder->getBeanListDecorator());
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'code', $code);
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'brand', $config->get('frontend.brand'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'static', $config->get('asset.domain'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'placeholder', $placeholder);
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'token', $guard->generateToken(AbstractForm::PARAMETER_TOKEN));
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
            if (!$bean->empty('CmsPage_ID_Redirect')) {
                $pageFinder = new CmsPageBeanFinder($adapter);
                $pageFinder->setCmsPage_ID($bean->get('CmsPage_ID_Redirect'));
                if ($pageFinder->findByLocaleWithFallback($locale->getLocale_Code(), 'de_AT') === 1) {
                    $redirectCode = $pageFinder->getBean()->get('ArticleTranslation_Code');
                    if ($redirectCode == '/') {
                        return new RedirectResponse($this->urlHelper->generate('cms', ['code' => null]));
                    }
                    return new RedirectResponse($this->urlHelper->generate('cms', ['code' => $redirectCode]));
                }
            }
            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'voted', $session->get('voted'
                . $bean->get('Article_Code')));

            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'bean', $bean);
            return new HtmlResponse($this->renderer->render('index::index'));
        }
        return new HtmlResponse($this->renderer->render('error::404'), 404);
    }

}

<?php


namespace Pars\Frontend\Cms\Handler;


use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Helper\UrlHelper;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Pars\Core\Localization\LocaleInterface;
use Pars\Core\Logging\LoggingMiddleware;
use Pars\Core\Translation\TranslatorMiddleware;
use Pars\Frontend\Cms\Form\Base\AbstractForm;
use Pars\Frontend\Cms\Helper\Config;
use Pars\Frontend\Cms\Helper\ImportLoader;
use Pars\Frontend\Cms\Model\LocaleModel;
use Pars\Frontend\Cms\Model\MenuModel;
use Pars\Frontend\Cms\Model\PageModel;
use Pars\Frontend\Cms\Model\ParagraphModel;
use Pars\Model\Import\ImportBeanFinder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CmsHandler implements \Psr\Http\Server\RequestHandlerInterface
{


    private TemplateRendererInterface $renderer;

    private $urlHelper;

    protected array $config;

    /**
     * CmsHandler constructor.
     * @param TemplateRendererInterface $renderer
     */
    public function __construct(TemplateRendererInterface $renderer, UrlHelper $urlHelper, array $config)
    {
        $this->renderer = $renderer;
        $this->urlHelper = $urlHelper;
        $this->config = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $adapter = $request->getAttribute(\Pars\Core\Database\DatabaseMiddleware::ADAPTER_ATTRIBUTE);
        $locale = $request->getAttribute(LocaleInterface::class);
        $translator = $request->getAttribute(TranslatorMiddleware::TRANSLATOR_ATTRIBUTE);
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        $logger = $request->getAttribute(LoggingMiddleware::LOGGER_ATTRIBUTE);
        $code = $request->getAttribute('code', '/');
        $config = new Config($adapter);

        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'code', $code);
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'hash', $this->config['bundles']['hash'] ?? '');
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'session', $session);
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'brand', $config->get('frontend.brand'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'keywords', $config->get('frontend.keywords'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'author', $config->get('frontend.author'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'static', $config->get('asset.domain'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'domain', $config->get('frontend.domain'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'charset', $config->get('frontend.charset'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'favicon', $config->get('frontend.favicon'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'import', new ImportLoader(new ImportBeanFinder($adapter)));

        $localeModel = new LocaleModel($adapter, $translator, $session, $locale, $code, $logger, $config);
        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'localelist',
            $localeModel->getLocaleList()
        );

        $menuModel = new MenuModel($adapter, $translator, $session, $locale, $code, $logger, $config);
        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'menu',
            $menuModel->getMenuList()
        );

        $pageModel = new PageModel($adapter, $translator, $session, $locale, $code, $logger, $config);
        $page = $pageModel->getPage();
        if ($page != null) {
            if (!$page->empty('CmsPage_ID_Redirect')) {
                $redirect = $pageModel->getPage(null, $page->empty('CmsPage_ID'));
                $redirectCode = $redirect->get('ArticleTranslation_Code');
                if ($redirectCode == '/') {
                    $redirectCode = null;
                }
                return new RedirectResponse($this->urlHelper->generate('cms', ['code' => $redirectCode]));
            }
            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'page', $page);
            return new HtmlResponse($this->renderer->render('index::index'));
        }
        $paragraphModel = new ParagraphModel($adapter, $translator, $session, $locale, $code, $logger, $config);
        $paragraph = $paragraphModel->getPage();
        if ($paragraph != null) {
            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'paragraph', $paragraph);
            return new HtmlResponse($this->renderer->render('index::paragraph'));
        }

        return new HtmlResponse($this->renderer->render('error::404'), 404);
    }

}

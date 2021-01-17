<?php


namespace Pars\Frontend\Cms\Handler;


use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\Diactoros\CallbackStream;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\TextResponse;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Helper\Template\TemplateVariableContainer;
use Mezzio\Helper\UrlHelper;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Pars\Core\Cache\ParsCache;
use Pars\Core\Localization\LocaleInterface;
use Pars\Core\Logging\LoggingMiddleware;
use Pars\Core\Translation\TranslatorMiddleware;
use Pars\Frontend\Cms\Helper\Config;
use Pars\Frontend\Cms\Helper\ImportLoader;
use Pars\Frontend\Cms\Model\LocaleModel;
use Pars\Frontend\Cms\Model\MenuModel;
use Pars\Frontend\Cms\Model\ModelFactory;
use Pars\Frontend\Cms\Model\PageModel;
use Pars\Frontend\Cms\Model\ParagraphModel;
use Pars\Frontend\Cms\Model\PostModel;
use Pars\Model\Import\ImportBeanFinder;
use Psr\Http\Message\RequestInterface;
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
        $cacheID =  md5($request->getUri());
        $cache = new ParsCache('site');
        if ($cache->has($cacheID) && $this->config[ConfigAggregator::ENABLE_CACHE]) {
            return new HtmlResponse(new CallbackStream(function () use ($cache, $cacheID){
                return $cache->get($cacheID);
            }));
        }
        $adapter = $request->getAttribute(\Pars\Core\Database\DatabaseMiddleware::ADAPTER_ATTRIBUTE);
        $locale = $request->getAttribute(LocaleInterface::class);
        $translator = $request->getAttribute(TranslatorMiddleware::TRANSLATOR_ATTRIBUTE);
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        $logger = $request->getAttribute(LoggingMiddleware::LOGGER_ATTRIBUTE);
        $config = $request->getAttribute(Config::class);
        $code = $request->getAttribute('code', '/');

        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'code', $code);
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'locale', $locale->getLocale_Code());
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'session', $session);
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'import', new ImportLoader(new ImportBeanFinder($adapter)));

        $container = $request->getAttribute(TemplateVariableContainer::class, new TemplateVariableContainer());
        foreach ($container->mergeForTemplate([]) as $key => $value) {
            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, $key, $value);
        }

        if ($code == 'browserconfig' || $code == 'browserconfig.xml') {
            return new HtmlResponse($this->renderer->render('meta::browserconfig'));
        }

        if ($code == 'sitemap' || $code == 'sitemap.xml') {
            return new HtmlResponse($this->renderer->render('meta::sitemap'));
        }

        if ($code == 'robots' || $code == 'robots.txt') {
            return new TextResponse($this->renderer->render('meta::robots'));
        }

        $pageModel = (new ModelFactory())($request, PageModel::class);
        $page = $pageModel->getPage();
        if ($page != null && ($page->empty('ArticleTranslation_Host') || trim($page->get('ArticleTranslation_Host')) == $request->getUri()->getHost())) {
            if (!$page->empty('CmsPage_ID_Redirect')) {
                $redirect = $pageModel->getPage(null, $page->empty('CmsPage_ID'));
                $redirectCode = $redirect->get('ArticleTranslation_Code');
                if ($redirectCode == '/') {
                    $redirectCode = null;
                }
                return new RedirectResponse($this->urlHelper->generate('cms', ['code' => $redirectCode]));
            }
            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'form', $pageModel->getForm());
            return new HtmlResponse(new CallbackStream(function () use ($request, $pageModel, $page, $cache, $cacheID, $adapter, $translator, $session, $locale, $code, $logger, $config, $guard) {
                $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'page', $page);
                $this->initTemplateVars($request);
                $out = $this->renderer->render('index::index');
                if (in_array(
                    $page->get('CmsPageType_Code'),
                    ['home', 'gallery', 'about', 'faq', 'tiles', 'blog', 'columns']
                )) {
                    $cache->set($cacheID, $out, 300);
                }
                return $out;
            }));
        }
        $this->initTemplateVars($request);
        $paragraphModel = new ParagraphModel($adapter, $translator, $session, $locale, $code, $logger, $config, $guard);
        $paragraph = $paragraphModel->getParagraph();
        if ($paragraph != null) {
            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'paragraph', $paragraph);
            return new HtmlResponse($this->renderer->render('index::paragraph'));
        }
        $postModel = new PostModel($adapter, $translator, $session, $locale, $code, $logger, $config, $guard);
        $post = $postModel->getPost();
        if ($post != null) {
            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'post', $post);
            $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'similarList', $postModel->getSimilarPosts($post));
            return new HtmlResponse($this->renderer->render('index::post'));
        }

        return new HtmlResponse($this->renderer->render('error::404'), 404);
    }

    protected function initTemplateVars(RequestInterface $request)
    {
        $localeModel = (new ModelFactory())($request, LocaleModel::class);
        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'localelist',
            $localeModel->getLocaleList()
        );

        $menuModel = (new ModelFactory())($request, MenuModel::class);
        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'menu',
            $menuModel->getMenuList()
        );
    }

}

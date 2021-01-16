<?php


namespace Pars\Frontend\Cms\Handler;


use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\Diactoros\CallbackStream;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
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
use Pars\Frontend\Cms\Model\PageModel;
use Pars\Frontend\Cms\Model\ParagraphModel;
use Pars\Frontend\Cms\Model\PostModel;
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
        $code = $request->getAttribute('code', '/');
        $config = new Config($adapter);
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'code', $code);
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'hash', $this->config['bundles']['hash'] ?? '');
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'session', $session);
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'brand', $config->get('frontend.brand'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'keywords', $config->get('frontend.keywords'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'author', $config->get('frontend.author'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'static', $config->get('asset.domain'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'key', $config->get('asset.key'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'domain', $config->get('frontend.domain'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'charset', $config->get('frontend.charset'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'favicon', $config->get('frontend.favicon'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'color', $config->get('frontend.color'));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'import', new ImportLoader(new ImportBeanFinder($adapter)));
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'host', $request->getUri()->getHost());

        if ($code == 'browserconfig') {
            return new HtmlResponse($this->renderer->render('meta::browserconfig'));
        }

        $localeModel = new LocaleModel($adapter, $translator, $session, $locale, $code, $logger, $config, $guard);
        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'localelist',
            $localeModel->getLocaleList()
        );

        $menuModel = new MenuModel($adapter, $translator, $session, $locale, $code, $logger, $config, $guard);
        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'menu',
            $menuModel->getMenuList()
        );

        $pageModel = new PageModel($adapter, $translator, $session, $locale, $code, $logger, $config, $guard);
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

            return new HtmlResponse(new CallbackStream(function () use ($request, $pageModel, $page, $cache, $cacheID, $adapter, $translator, $session, $locale, $code, $logger, $config, $guard) {
                $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'page', $page);
                $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'form', $pageModel->getForm());
                $container = $request->getAttribute(TemplateVariableContainer::class, new TemplateVariableContainer());
                foreach ($container->mergeForTemplate([]) as $key => $value) {
                    $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, $key, $value);
                }
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

}

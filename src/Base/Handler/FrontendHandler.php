<?php


namespace Pars\Frontend\Base\Handler;


use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Helper\Template\TemplateVariableContainer;
use Mezzio\Helper\UrlHelper;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Pars\Core\Localization\LocaleInterface;
use Pars\Frontend\Cms\Helper\ImportLoader;
use Pars\Frontend\Cms\Model\LocaleModel;
use Pars\Frontend\Cms\Model\MenuModel;
use Pars\Frontend\Cms\Model\ModelFactory;
use Pars\Model\Import\ImportBeanFinder;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class FrontendHandler implements RequestHandlerInterface
{
    protected TemplateRendererInterface $renderer;
    protected $urlHelper;
    protected array $config;

    /**
     * FrontendHandler constructor.
     * @param TemplateRendererInterface $renderer
     */
    public function __construct(TemplateRendererInterface $renderer, UrlHelper $urlHelper, array $config)
    {
        $this->renderer = $renderer;
        $this->urlHelper = $urlHelper;
        $this->config = $config;
    }

    /**
     * @param string $name
     * @param $value
     */
    protected function assign(string $name, $value)
    {
        $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, $name, $value);
    }

    /**
     * @param RequestInterface $request
     */
    protected function initDefaultVars(RequestInterface $request)
    {
        $adapter = $request->getAttribute(\Pars\Core\Database\DatabaseMiddleware::ADAPTER_ATTRIBUTE);
        $locale = $request->getAttribute(LocaleInterface::class);
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $code = $request->getAttribute('code', '/');

        $this->assign('code', $code);

        if ($locale instanceof LocaleInterface) {
            $this->assign('locale', $locale->getLocale_Code());
        }

        $this->assign('session', $session);
        $this->assign('import', new ImportLoader(new ImportBeanFinder($adapter)));
        $this->assign('guard', $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE));

        $container = $request->getAttribute(TemplateVariableContainer::class, new TemplateVariableContainer());
        foreach ($container->mergeForTemplate([]) as $key => $value) {
            $this->assign($key, $value);
        }

        $localeModel = (new ModelFactory())($request, LocaleModel::class);
        $this->assign('localelist', $localeModel->getLocaleList());
        $menuModel = (new ModelFactory())($request, MenuModel::class);
        $this->assign('menu', $menuModel->getMenuList());
    }
}

<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Helper\UrlHelper;
use Pars\Model\Article\Translation\ArticleTranslationBean;

class PathExtension implements ExtensionInterface
{
    protected UrlHelper $urlHelper;
    protected ServerUrlHelper $serverUrlHelper;

    /**
     * PathExtension constructor.
     * @param UrlHelper $urlHelper
     * @param ServerUrlHelper $serverUrlHelper
     */
    public function __construct(UrlHelper $urlHelper, ServerUrlHelper $serverUrlHelper)
    {
        $this->urlHelper = $urlHelper;
        $this->serverUrlHelper = $serverUrlHelper;
    }

    /**
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction(
            'cmspath',
            function ($code = null, ?array $params = [], ?string $id = null, ?string $locale = null) {
                if ($code instanceof ArticleTranslationBean) {
                    $code = $code->get('ArticleTranslation_Code');
                }
                return $this->generatePath($code, $locale, $params, $id);
            }
        );
        $engine->registerFunction(
            'cmspaths',
            function ($code = null, array $params = [], ?string $id = null, ?string $locale = null) {
                if ($code instanceof ArticleTranslationBean) {
                    $code = $code->get('ArticleTranslation_Code');
                }
                return $this->generateServerPath($code, $locale, $params, $id);
            }
        );
    }

    /**
     * @param string|null $code
     * @param string|null $locale
     * @param array|null $params
     * @param string|null $id
     * @return string
     */
    protected function generatePath(
        ?string $code = null,
        ?string $locale = null,
        ?array $params = [],
        ?string $id = null
    )
    {
        $route = [];
        if ($code != null) {
            if ($code == '/') {
                $code = null;
            }
            $route['code'] = $code;
        }
        if ($locale != null) {
            $route['locale'] = $locale;
        }
        if ($this->urlHelper->getRouteResult()->isSuccess()) {
            return $this->urlHelper->generate(null, $route, $params, $id);
        }
        return '';
    }

    /**
     * @param string|null $code
     * @param string|null $locale
     * @param array|null $params
     * @param string|null $id
     * @return string
     */
    protected function generateServerPath(
        ?string $code = null,
        ?string $locale = null,
        ?array $params = [],
        ?string $id = null
    )
    {
        return $this->serverUrlHelper->generate($this->generatePath($code, $locale, $params, $id));
    }
}

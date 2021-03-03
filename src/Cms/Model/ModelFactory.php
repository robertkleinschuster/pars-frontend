<?php

namespace Pars\Frontend\Cms\Model;

use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Session\SessionMiddleware;
use Pars\Core\Localization\LocaleInterface;
use Pars\Core\Logging\LoggingMiddleware;
use Pars\Core\Translation\TranslatorMiddleware;
use Pars\Frontend\Cms\Helper\Config;
use Psr\Http\Message\RequestInterface;

/**
 * Class ModelFactory
 * @package Pars\Frontend\Cms\Model
 */
class ModelFactory
{
    /**
     * @param RequestInterface $request
     * @param string $class
     * @return BaseModel
     */
    public function __invoke(RequestInterface $request, string $class): BaseModel
    {
        $adapter = $request->getAttribute(\Pars\Core\Database\DatabaseMiddleware::ADAPTER_ATTRIBUTE);
        $locale = $request->getAttribute(LocaleInterface::class);
        $translator = $request->getAttribute(TranslatorMiddleware::TRANSLATOR_ATTRIBUTE);
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        $logger = $request->getAttribute(LoggingMiddleware::LOGGER_ATTRIBUTE);
        $config = $request->getAttribute(Config::class);
        $code = $request->getAttribute('code', '/');
        return new $class($adapter, $translator, $session, $locale, $code, $logger, $config, $guard);
    }
}

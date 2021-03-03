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
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class CmsFormMiddelware
 * @package Pars\Frontend\Cms\Form
 */
class CmsFormMiddelware implements MiddlewareInterface
{

    protected Config $config;

    /**
     * CmsFormMiddelware constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param ContainerInterface $container
     * @return CmsFormMiddelware
     */
    public function __invoke(ContainerInterface $container)
    {
        return new CmsFormMiddelware(new Config($container->get(AdapterInterface::class)));
    }


    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * @var $flash FlashMessagesInterface
         */
        $flash = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);
        /**
         * @var $container TemplateVariableContainer
         */
        $container = $request->getAttribute(TemplateVariableContainer::class);
        $vars = [];
        if ($request->getMethod() === 'POST') {
            $adapter = $request->getAttribute(DatabaseMiddleware::ADAPTER_ATTRIBUTE);
            $translator = $request->getAttribute(TranslatorMiddleware::TRANSLATOR_ATTRIBUTE);
            $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
            $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
            $log = $request->getAttribute(LoggingMiddleware::LOGGER_ATTRIBUTE);
            $locale = $request->getAttribute(LocaleInterface::class);
            $code = $request->getAttribute('code', '/');
            try {
                $finder = new ArticleTranslationBeanFinder($adapter);
                $data = $request->getParsedBody();
                if (isset($data['Article_ID'])) {
                    $finder->setArticle_ID($data['Article_ID']);
                } else {
                    $finder->setArticleTranslation_Code($code);
                }
                $form = (new FormFactory())($data, $adapter, $session, $guard, $translator);
                if ($finder->findByLocaleWithFallback($locale->getLocale_Code(), $this->config->get('locale.default'))  == 1) {
                    $form->setBean($finder->getBean());
                }
                $form->submit();
                if ($form->getValidationHelper()->hasError()) {
                    $flash->flash('validationErrorMap', $form->getValidationHelper()->getErrorFieldMap());
                    $flash->flash('previousAttributes', $request->getParsedBody());
                } else {
                    $flash->flash('formSuccess', true);
                }
            } catch (\Exception $exception) {
                $log->error('Form submit error.', ['exception' => $exception]);
                $flash->flash('error', $translator->translate('form.error.exception', 'frontend'));
            }
            return new RedirectResponse($request->getUri());
        } else {
            $validationErrorMap = $flash->getFlash('validationErrorMap');
            if ($validationErrorMap) {
                $vars['errors'] = $validationErrorMap;
            }
            $previousAttributes = $flash->getFlash('previousAttributes');
            if ($previousAttributes) {
                $vars['attributes'] = $previousAttributes;
            }
            $formSuccess = $flash->getFlash('formSuccess');
            if ($formSuccess) {
                $vars['success'] = $formSuccess;
            }
        }
        return $handler->handle($request->withAttribute(TemplateVariableContainer::class, $container->merge($vars)));
    }
}

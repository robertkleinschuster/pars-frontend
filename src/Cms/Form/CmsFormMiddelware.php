<?php


namespace Pars\Frontend\Cms\Form;


use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Csrf\CsrfMiddleware;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessagesInterface;
use Mezzio\Helper\Template\TemplateVariableContainer;
use Mezzio\Session\SessionMiddleware;
use Pars\Core\Database\DatabaseMiddleware;
use Pars\Core\Logging\LoggingMiddleware;
use Pars\Core\Translation\TranslatorMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CmsFormMiddelware implements MiddlewareInterface
{
    /**
     * @param ContainerInterface $container
     * @return CmsFormMiddelware
     */
    public function __invoke(ContainerInterface $container)
    {
        return new CmsFormMiddelware();
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
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
            try {
                $form = (new FormFactory())($request->getParsedBody(), $adapter, $session, $guard, $translator);
                $form->submit();
                if ($form->getValidationHelper()->hasError()) {
                    $flash->flash('validationErrorMap', $form->getValidationHelper()->getErrorFieldMap());
                    $flash->flash('previousAttributes', $request->getParsedBody());
                }
            } catch (\Exception $exception) {
                $log->error('Form submit error.', ['exception' => $exception]);
                $flash->flash('error', $translator->translate('form.error.exception'));
            }
            return new RedirectResponse($request->getUri());
        } else {
            $validationErrorMap = $flash->getFlash('validationErrorMap');
            if ($validationErrorMap) {
                $vars['validationErrorMap'] = $validationErrorMap;
            }
            $previousAttributes = $flash->getFlash('previousAttributes');
            if ($previousAttributes) {
                $vars['previousAttributes'] = $previousAttributes;
            }
        }
        return $handler->handle($request->withAttribute(TemplateVariableContainer::class, $container->merge($vars)));
    }

}

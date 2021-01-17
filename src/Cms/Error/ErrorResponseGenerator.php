<?php


namespace Pars\Frontend\Cms\Error;


use Laminas\Stratigility\Utils;
use Mezzio\Helper\Template\TemplateVariableContainer;
use Mezzio\Response\ErrorResponseGeneratorTrait;
use Mezzio\Template\TemplateRendererInterface;
use Pars\Frontend\Cms\Model\LocaleModel;
use Pars\Frontend\Cms\Model\MenuModel;
use Pars\Frontend\Cms\Model\ModelFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ErrorResponseGenerator
{

    public const TEMPLATE_DEFAULT = 'error::error';
    public const LAYOUT_DEFAULT = 'layout::default';

    use ErrorResponseGeneratorTrait;


    public function __construct(
        bool $isDevelopmentMode = false,
        TemplateRendererInterface $renderer = null,
        string $template = self::TEMPLATE_DEFAULT,
        string $layout = self::LAYOUT_DEFAULT
    ) {
        $this->debug     = $isDevelopmentMode;
        $this->renderer  = $renderer;
        $this->template  = $template;
        $this->layout    = $layout;
    }

    public function __invoke(
        Throwable $e,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) : ResponseInterface {
        $response = $response->withStatus(Utils::getStatusCode($e, $response));


        if ($this->renderer) {
            try {
                $container = $request->getAttribute(TemplateVariableContainer::class, new TemplateVariableContainer());
                foreach ($container->mergeForTemplate([]) as $key => $value) {
                    $this->renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, $key, $value);
                }
                $localeModel = (new ModelFactory())($request, LocaleModel::class);
                $this->renderer->addDefaultParam(
                    TemplateRendererInterface::TEMPLATE_ALL,
                    'localelist',
                    $localeModel->getLocaleList()
                );

                $menuModel =  (new ModelFactory())($request, MenuModel::class);
                $this->renderer->addDefaultParam(
                    TemplateRendererInterface::TEMPLATE_ALL,
                    'menu',
                    $menuModel->getMenuList()
                );

                $this->renderer->addDefaultParam(
                    TemplateRendererInterface::TEMPLATE_ALL,
                    'code',
                    ''
                );

                $this->renderer->addDefaultParam(
                    TemplateRendererInterface::TEMPLATE_ALL,
                    'locale',
                    $localeModel->getLocale()->getLocale_Code()
                );
            } catch (\Throwable $t) {

            }

            return $this->prepareTemplatedResponse(
                $e,
                $this->renderer,
                [
                    'response' => $response,
                    'request'  => $request,
                    'uri'      => (string) $request->getUri(),
                    'status'   => $response->getStatusCode(),
                    'reason'   => $response->getReasonPhrase(),
                    'layout'   => $this->layout,
                ],
                $this->debug,
                $response
            );
        }

        return $this->prepareDefaultResponse($e, $this->debug, $response);
    }
}

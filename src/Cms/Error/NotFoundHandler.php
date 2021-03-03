<?php

/**
 * @see       https://github.com/mezzio/mezzio for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Pars\Frontend\Cms\Error;

use Fig\Http\Message\StatusCodeInterface;
use Mezzio\Helper\Template\TemplateVariableContainer;
use Mezzio\Template\TemplateRendererInterface;
use Pars\Frontend\Cms\Model\LocaleModel;
use Pars\Frontend\Cms\Model\MenuModel;
use Pars\Frontend\Cms\Model\ModelFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function sprintf;

class NotFoundHandler implements RequestHandlerInterface
{
    public const TEMPLATE_DEFAULT = 'error::404';
    public const LAYOUT_DEFAULT = 'layout::default';

    /**
     * @var TemplateRendererInterface|null
     */
    private $renderer;

    /**
     * @var callable
     */
    private $responseFactory;

    /**
     * @var string
     */
    private $template;

    /**
     * @var string
     */
    private $layout;

    /**
     * @todo Allow nullable $layout
     */
    public function __construct(
        callable $responseFactory,
        TemplateRendererInterface $renderer = null,
        string $template = self::TEMPLATE_DEFAULT,
        string $layout = self::LAYOUT_DEFAULT
    ) {
        // Factory cast to closure in order to provide return type safety.
        $this->responseFactory = function () use ($responseFactory): ResponseInterface {
            return $responseFactory();
        };
        $this->renderer = $renderer;
        $this->template = $template;
        $this->layout = $layout;
    }

    /**
     * Creates and returns a 404 response.
     *
     * @param ServerRequestInterface $request Passed to internal handler
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->renderer === null) {
            return $this->generatePlainTextResponse($request);
        }

        return $this->generateTemplatedResponse($this->renderer, $request);
    }

    /**
     * Generates a plain text response indicating the request method and URI.
     */
    private function generatePlainTextResponse(ServerRequestInterface $request): ResponseInterface
    {
        $response = ($this->responseFactory)()->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        $response->getBody()
            ->write(sprintf(
                'Cannot %s %s',
                $request->getMethod(),
                (string) $request->getUri()
            ));

        return $response;
    }

    /**
     * Generates a response using a template.
     *
     * Template will receive the current request via the "request" variable.
     */
    private function generateTemplatedResponse(
        TemplateRendererInterface $renderer,
        ServerRequestInterface $request
    ): ResponseInterface {

        $container = $request->getAttribute(TemplateVariableContainer::class, new TemplateVariableContainer());
        foreach ($container->mergeForTemplate([]) as $key => $value) {
            $renderer->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, $key, $value);
        }
        $localeModel = (new ModelFactory())($request, LocaleModel::class);
        $renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'localelist',
            $localeModel->getLocaleList()
        );

        $menuModel =  (new ModelFactory())($request, MenuModel::class);
        $renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'menu',
            $menuModel->getMenuList()
        );

        $renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'code',
            ''
        );

        $this->renderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'locale',
            $localeModel->getLocale()->getLocale_Code()
        );


        $response = ($this->responseFactory)()->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        $response->getBody()->write(
            $renderer->render($this->template, ['request' => $request, 'layout' => $this->layout])
        );

        return $response;
    }
}

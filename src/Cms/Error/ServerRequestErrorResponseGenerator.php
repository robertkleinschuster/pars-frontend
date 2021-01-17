<?php


namespace Pars\Frontend\Cms\Error;


use Laminas\Stratigility\Utils;
use Mezzio\Response\ErrorResponseGeneratorTrait;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

class ServerRequestErrorResponseGenerator
{
    use ErrorResponseGeneratorTrait;

    public const TEMPLATE_DEFAULT = 'error::error';

    protected array $config;

    /**
     * Factory capable of generating a ResponseInterface instance.
     *
     * @var callable
     */
    private $responseFactory;

    public function __construct(
        callable $responseFactory,
        array $config,
        bool $isDevelopmentMode = false,
        TemplateRendererInterface $renderer = null,
        string $template = self::TEMPLATE_DEFAULT
    ) {
        $this->responseFactory = function () use ($responseFactory) : ResponseInterface {
            return $responseFactory();
        };
        $this->config = $config;
        $this->debug     = $isDevelopmentMode;
        $this->renderer  = $renderer;
        $this->template  = $template;
    }

    public function __invoke(\Throwable $e) : ResponseInterface
    {
        $response = ($this->responseFactory)();
        $response = $response->withStatus(Utils::getStatusCode($e, $response));
        if ($this->renderer) {
            return $this->prepareTemplatedResponse(
                $e,
                $this->renderer,
                [
                    'response' => $response,
                    'status'   => $response->getStatusCode(),
                    'reason'   => $response->getReasonPhrase(),
                    'hash' => $this->config['bundles']['hash'] ?? ''
                ],
                $this->debug,
                $response
            );
        }

        return $this->prepareDefaultResponse($e, $this->debug, $response);
    }
}

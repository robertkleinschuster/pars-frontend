<?php


namespace Pars\Frontend\Cms\Model;


use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterAwareInterface;
use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Mezzio\Csrf\CsrfGuardInterface;
use Mezzio\Session\SessionInterface;
use Pars\Core\Localization\LocaleInterface;
use Pars\Frontend\Cms\Helper\Config;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class BaseModel implements AdapterAwareInterface, TranslatorAwareInterface, LoggerAwareInterface
{
    use AdapterAwareTrait;
    use TranslatorAwareTrait;
    use LoggerAwareTrait;

    protected SessionInterface $session;
    protected LocaleInterface $locale;
    protected CsrfGuardInterface $guard;
    protected Config $config;
    protected string $code;

    /**
     * BaseModel constructor.
     * @param SessionInterface $session
     * @param LocaleInterface $locale
     * @param Config $config
     * @param string $code
     */
    public function __construct(
        Adapter $adapter,
        Translator $translator,
        SessionInterface $session,
        LocaleInterface $locale,
        string $code,
        LoggerInterface $logger,
        Config $config,
    CsrfGuardInterface $guard
    )
    {
        $this->session = $session;
        $this->locale = $locale;
        $this->code = $code;
        $this->config = $config;
        $this->guard = $guard;
        $this->setLogger($logger);
        $this->setDbAdapter($adapter);
        $this->setTranslator($translator);
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @return CsrfGuardInterface
     */
    public function getGuard(): CsrfGuardInterface
    {
        return $this->guard;
    }


    /**
     * @param string $message
     * @return string
     */
    protected function translate(string $message): string
    {
        return $this->getTranslator()->translate($message, 'frontend');
    }

    /**
     * @return SessionInterface
     */
    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * @return LocaleInterface
     */
    public function getLocale(): LocaleInterface
    {
        return $this->locale;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        if (!isset($this->config)) {
            $this->config = new Config($this->getAdapter());
        }
        return $this->config;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return Adapter
     */
    public function getAdapter(): Adapter
    {
        return $this->adapter;
    }




}

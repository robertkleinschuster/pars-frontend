<?php


namespace Pars\Frontend\Cms\Form\Base;


use Laminas\Db\Adapter\AdapterInterface;
use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Laminas\I18n\Translator\TranslatorInterface;
use Mezzio\Csrf\CsrfGuardInterface;
use Mezzio\Session\SessionInterface;
use Pars\Helper\Validation\ValidationHelperAwareInterface;
use Pars\Helper\Validation\ValidationHelperAwareTrait;

abstract class AbstractForm implements ValidationHelperAwareInterface, TranslatorAwareInterface
{
    use ValidationHelperAwareTrait;
    use TranslatorAwareTrait;

    protected array $data;
    protected AdapterInterface $adapter;
    protected SessionInterface $session;
    protected CsrfGuardInterface $guard;

    public const PARAMETER_TOKEN = 'form_token';
    public const PARAMETER_ID = 'form_id';

    /**
     * AbstractForm constructor.
     * @param array $data
     * @param AdapterInterface $adapter
     * @param SessionInterface $session
     * @param CsrfGuardInterface $guard
     * @param TranslatorInterface $translator
     */
    public function __construct(
        array $data,
        AdapterInterface $adapter,
        SessionInterface $session,
        CsrfGuardInterface $guard,
        TranslatorInterface $translator
    )
    {
        $this->data = $data;
        $this->adapter = $adapter;
        $this->session = $session;
        $this->guard = $guard;
        $this->setTranslator($translator);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }


    /**
     * @return AdapterInterface
     */
    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @return SessionInterface
     */
    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * @return CsrfGuardInterface
     */
    public function getGuard(): CsrfGuardInterface
    {
        return $this->guard;
    }

    /**
     * @return string
     */
    protected function generateToken(): string
    {
        if (!$this->getSession()->get(self::PARAMETER_TOKEN, false)) {
            return $this->getGuard()->generateToken(self::PARAMETER_TOKEN);
        } else {
            return $this->getSession()->get(self::PARAMETER_TOKEN);
        }
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function validateToken(array $data): bool
    {
        $result = $this->getGuard()->validateToken($data[self::PARAMETER_TOKEN], self::PARAMETER_TOKEN);
        $this->generateToken();
        return $result;
    }

    /**
     * @param array $data
     */
    public function submit()
    {
        if ($this->validateToken($this->getData())) {
            if ($this->validate($this->getData())) {
                $this->save($this->sanitize($this->getData()));
            }
        } else {
            $this->getValidationHelper()->addError(
                self::PARAMETER_TOKEN,
                $this->getTranslator()->translate('form.error.form_token.invalid', 'frontend')
            );
        }
    }


    /**
     * @return string
     */
    public abstract static function id(): string;


    /**
     * @param array $data
     * @return array
     */
    protected abstract function sanitize(array $data): array;

    /**
     * @param array $data
     * @return bool
     */
    protected abstract function validate(array $data): bool;

    /**
     * @param array $data
     * @return bool
     */
    protected abstract function save(array $data): bool;
}

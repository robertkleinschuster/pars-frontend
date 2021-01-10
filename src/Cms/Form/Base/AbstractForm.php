<?php


namespace Pars\Frontend\Cms\Form\Base;


use Laminas\Db\Adapter\AdapterInterface;
use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Laminas\I18n\Translator\TranslatorInterface;
use Mezzio\Csrf\CsrfGuardInterface;
use Mezzio\Session\SessionInterface;
use Niceshops\Bean\Type\Base\BeanAwareInterface;
use Niceshops\Bean\Type\Base\BeanAwareTrait;
use Pars\Helper\Validation\ValidationHelperAwareInterface;
use Pars\Helper\Validation\ValidationHelperAwareTrait;

abstract class AbstractForm implements ValidationHelperAwareInterface, TranslatorAwareInterface, BeanAwareInterface
{
    use ValidationHelperAwareTrait;
    use TranslatorAwareTrait;
    use BeanAwareTrait;

    protected array $data;
    protected AdapterInterface $adapter;
    protected SessionInterface $session;
    protected CsrfGuardInterface $guard;
    protected string $method = 'post';
    protected ?string $action = null;
    protected ?Validate $validate = null;
    protected ?Sanitize $sanitize = null;

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
        $this->initialize();
    }

    protected function initialize() {

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
    public function generateToken(): string
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

    protected function translate(string $message) {
        return $this->getTranslator()->translate($message, 'frontend');
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
    * @return ?string
    */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
    * @param ?string $action
    *
    * @return $this
    */
    public function setAction(?string $action): self
    {
        $this->action = $action;
        return $this;
    }

    /**
    * @return bool
    */
    public function hasAction(): bool
    {
        return isset($this->action);
    }

    /**
     * @return Validate|null
     */
    public function getValidate(): ?Validate
    {
        if ($this->validate == null) {
            $this->validate = new Validate();
        }
        return $this->validate;
    }

    /**
     * @return Sanitize|null
     */
    public function getSanitize(): ?Sanitize
    {
        if ($this->sanitize == null) {
            $this->sanitize = new Sanitize();
        }
        return $this->sanitize;
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

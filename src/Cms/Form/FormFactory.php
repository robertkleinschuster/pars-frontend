<?php


namespace Pars\Frontend\Cms\Form;


use Laminas\Db\Adapter\AdapterInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Mezzio\Csrf\CsrfGuardInterface;
use Mezzio\Session\SessionInterface;
use Niceshops\Core\Exception\CoreException;
use Pars\Frontend\Cms\Form\Base\AbstractForm;
use Pars\Frontend\Cms\Form\Contact\ContactForm;
use Pars\Frontend\Cms\Form\Poll\PollForm;

class FormFactory
{
    public function __invoke(
        array $data,
        AdapterInterface $adapter,
        SessionInterface $session,
        CsrfGuardInterface $guard,
        TranslatorInterface $translator
    ): AbstractForm {
        switch ($data[AbstractForm::PARAMETER_ID]) {
            case ContactForm::id():
                return new ContactForm($data, $adapter, $session, $guard, $translator);
            case PollForm::id():
                return new PollForm($data, $adapter, $session, $guard, $translator);
        }
        throw new CoreException('Invalid form id parameter!');
    }

}

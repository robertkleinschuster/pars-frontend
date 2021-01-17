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
use Pars\Model\Cms\Page\CmsPageBean;

class FormFactory
{
    public function __invoke(
        array $data,
        AdapterInterface $adapter,
        SessionInterface $session,
        CsrfGuardInterface $guard,
        TranslatorInterface $translator
    ) {
        return $this->createForm($data[AbstractForm::PARAMETER_ID], $data, $adapter, $session, $guard, $translator);
    }

    public function createFormForPage(
        CmsPageBean $page,
        AdapterInterface $adapter,
        SessionInterface $session,
        CsrfGuardInterface $guard,
        TranslatorInterface $translator
    ) {
        $id = null;
        switch ($page->get('CmsPageType_Code')) {
            case 'contact':
                $id = ContactForm::id();
                break;
            case 'poll':
                $id = PollForm::id();
        }
        if ($id !== null) {
            $form = $this->createForm($id, [], $adapter, $session, $guard, $translator);
            $form->generateToken();
            return $form;
        }
        return null;
    }

    public function createForm(
        string $id,
        array $data,
        AdapterInterface $adapter,
        SessionInterface $session,
        CsrfGuardInterface $guard,
        TranslatorInterface $translator
    ) {
        switch ($id) {
            case ContactForm::id():
                return new ContactForm($data, $adapter, $session, $guard, $translator);
            case PollForm::id():
                return new PollForm($data, $adapter, $session, $guard, $translator);
        }
        return null;
    }
}

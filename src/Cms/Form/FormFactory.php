<?php


namespace Pars\Frontend\Cms\Form;


use Laminas\Db\Adapter\AdapterInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Mezzio\Csrf\CsrfGuardInterface;
use Mezzio\Session\SessionInterface;
use Pars\Frontend\Cms\Form\Base\AbstractForm;
use Pars\Frontend\Cms\Form\Contact\ContactForm;
use Pars\Frontend\Cms\Form\Poll\PollForm;
use Pars\Model\Article\ArticleBean;
use Pars\Model\Cms\Block\CmsBlockBean;
use Pars\Model\Cms\Page\CmsPageBean;

/**
 * Class FormFactory
 * @package Pars\Frontend\Cms\Form
 */
class FormFactory
{
    /**
     * @param array $data
     * @param AdapterInterface $adapter
     * @param SessionInterface $session
     * @param CsrfGuardInterface $guard
     * @param TranslatorInterface $translator
     * @return ContactForm|PollForm|null
     */
    public function __invoke(
        array $data,
        AdapterInterface $adapter,
        SessionInterface $session,
        CsrfGuardInterface $guard,
        TranslatorInterface $translator
    ) {
        return $this->createForm($data[AbstractForm::PARAMETER_ID], $data, $adapter, $session, $guard, $translator);
    }

    /**
     * @param CmsPageBean $page
     * @param AdapterInterface $adapter
     * @param SessionInterface $session
     * @param CsrfGuardInterface $guard
     * @param TranslatorInterface $translator
     * @return ContactForm|PollForm|null
     * @throws \Niceshops\Bean\Type\Base\BeanException
     */
    public function createFormForPage(
        ?CmsPageBean $page,
        AdapterInterface $adapter,
        SessionInterface $session,
        CsrfGuardInterface $guard,
        TranslatorInterface $translator
    ) {
        if (!$page) {
            return null;
        }
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
            $form->setBean($page);
            $form->generateToken();
            return $form;
        }
        return null;
    }

    /**
     * @param CmsBlockBean $block
     * @param AdapterInterface $adapter
     * @param SessionInterface $session
     * @param CsrfGuardInterface $guard
     * @param TranslatorInterface $translator
     */
    public function createFormForBlock(
        ?CmsBlockBean $block,
        AdapterInterface $adapter,
        SessionInterface $session,
        CsrfGuardInterface $guard,
        TranslatorInterface $translator
    ) {
        if (!$block) {
            return null;
        }
        $id = null;
        switch ($block->get('CmsBlockType_Code')) {
            case 'contact':
                $id = ContactForm::id();
                break;
            case 'poll':
                $id = PollForm::id();
        }
        if ($id !== null) {
            $form = $this->createForm($id, [], $adapter, $session, $guard, $translator);
            $form->setBean($block);
            $form->generateToken();
            return $form;
        }
        return null;
    }

    /**
     * @param ArticleBean $bean
     * @param AdapterInterface $adapter
     * @param SessionInterface $session
     * @param CsrfGuardInterface $guard
     * @param TranslatorInterface $translator
     * @return ContactForm|PollForm|void|null
     */
    public function createFormForBean(
        ArticleBean $bean,
        AdapterInterface $adapter,
        SessionInterface $session,
        CsrfGuardInterface $guard,
        TranslatorInterface $translator
    ) {
        if ($bean instanceof CmsBlockBean) {
            return $this->createFormForBlock($bean, $adapter, $session, $guard, $translator);
        }
        if ($bean instanceof CmsPageBean) {
            return $this->createFormForPage($bean, $adapter, $session, $guard, $translator);
        }
    }

    /**
     * @param string $id
     * @param array $data
     * @param AdapterInterface $adapter
     * @param SessionInterface $session
     * @param CsrfGuardInterface $guard
     * @param TranslatorInterface $translator
     * @return ContactForm|PollForm|null
     */
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

<?php


namespace Pars\Frontend\Cms\Form\Contact;


use Pars\Frontend\Cms\Form\Base\AbstractForm;
use Pars\Model\Article\Data\ArticleDataBeanFinder;
use Pars\Model\Article\Data\ArticleDataBeanProcessor;

class ContactForm extends AbstractForm
{
    public static function id(): string
    {
        return 'contact';
    }

    protected function sanitize(array $data): array
    {
        $sanitize['ArticleData_Data']['name'] = $this->getSanitize()->string($data['name']);
        $sanitize['ArticleData_Data']['email'] = $this->getSanitize()->email($data['email']);
        $sanitize['ArticleData_Data']['subject'] = $this->getSanitize()->string($data['subject']);
        $sanitize['ArticleData_Data']['message'] = $this->getSanitize()->string($data['message']);
        return $sanitize;
    }


    protected function validate(array $data): bool
    {
        if (empty($data['email'])) {
            $this->getValidationHelper()->addError('email', $this->translate('contact.form.email.empty'));
        } elseif (!$this->getValidate()->email($data['email'])) {
            $this->getValidationHelper()->addError('email', $this->translate('contact.form.email.invalid'));
        }
        if (empty($data['message'])) {
            $this->getValidationHelper()->addError('message', $this->translate('contact.form.message.empty'));
        }
        return !$this->getValidationHelper()->hasError();
    }

    protected function save(array $data): bool
    {
        if ($this->hasBean()) {
            $processor = new ArticleDataBeanProcessor($this->getAdapter());
            $finder = new ArticleDataBeanFinder($this->getAdapter());
            $bean = $finder->getBeanFactory()->getEmptyBean($data);
            $beanList = $finder->getBeanFactory()->getEmptyBeanList();
            $bean->set('Article_ID', $this->getBean()->get('Article_ID'));
            $bean->fromArray($data);
            $beanList->push($bean);
            $processor->setBeanList($beanList);
            $processor->save();
            $this->getValidationHelper()->merge($processor->getValidationHelper());
        } else {
            $this->getValidationHelper()->addError('general', $this->translate('contact.form.save.error'));
        }
        return !$this->getValidationHelper()->hasError();
    }

}

<?php


namespace Pars\Frontend\Cms\Form\Contact;


use Pars\Frontend\Cms\Form\Base\AbstractForm;

class ContactForm extends AbstractForm
{
    public static function id(): string
    {
        return 'contact';
    }

    protected function sanitize(array $data): array
    {
        $sanitize['ArticleData_Data']['name'] = $this->getSanitize()->string($data['name']) ?? '';
        $sanitize['ArticleData_Data']['email'] = $this->getSanitize()->email($data['email']) ?? '';
        $sanitize['ArticleData_Data']['subject'] = $this->getSanitize()->string($data['subject']) ?? '';
        $sanitize['ArticleData_Data']['message'] = $this->getSanitize()->string($data['message']) ?? '';
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
        if (empty($data['privacy'])) {
            $this->getValidationHelper()->addError('privacy', $this->translate('contact.form.privacy.empty'));
        }
        return !$this->getValidationHelper()->hasError();
    }
}

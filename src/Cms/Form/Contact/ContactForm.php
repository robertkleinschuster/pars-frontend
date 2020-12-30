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
        return $data;
    }

    protected function validate(array $data): bool
    {
        return true;
    }

    protected function save(array $data): bool
    {
        return true;
    }

}

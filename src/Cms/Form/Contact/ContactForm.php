<?php

namespace Pars\Frontend\Cms\Form\Contact;

use Laminas\Mail\Message;
use Laminas\Mail\Transport\Sendmail;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;
use Niceshops\Bean\Type\Base\BeanInterface;
use Pars\Frontend\Cms\Form\Base\AbstractForm;
use Pars\Frontend\Cms\Helper\Config;
use Pars\Model\Article\DataBean;

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

    protected function success(BeanInterface $bean)
    {
        $dataBean = $this->getBean()->get('Article_Data');
        if ($dataBean instanceof DataBean && !$dataBean->empty('contact_email')) {
            $email = $dataBean->get('contact_email');
            $config = new Config($this->getAdapter());
            $data = $bean->get('ArticleData_Data');
            $mail = new Message();
            $mail->setBody($data['message']);
            $mail->setFrom($data['email'], $data['name']);
            $mail->addTo($email, $email);
            $mail->setSubject($data['subject']);

            if ($config->get('meil.smtp') == 'true') {
                $transport = new Smtp();
                $options = [
                    'name' => $config->get('mail.smtp.name'),
                    'host' => $config->get('mail.smtp.host'),
                    'port' => intval($config->get('mail.smtp.port')),
                ];
                if (!empty($config->get('mail.smtp.authentication'))) {
                    $options['connection_class'] = $config->get('mail.smtp.authentication');
                    $options['connection_config'] = [
                        'username' => $config->get('mail.smtp.authentication.username'),
                        'password' => $config->get('mail.smtp.authentication.password'),
                    ];
                    if ($config->get('mail.smtp.authentication.ssl')) {
                        $options['connection_config']['ssl'] = $config->get('mail.smtp.authentication.ssl');
                    }
                }
                $options = new SmtpOptions($options);
                $transport->setOptions($options);
                $transport->send($mail);
            } else {
                $transport = new Sendmail();
                $transport->send($mail);
            }
        }
    }
}

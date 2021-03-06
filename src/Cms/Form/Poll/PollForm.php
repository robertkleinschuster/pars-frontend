<?php

namespace Pars\Frontend\Cms\Form\Poll;

use Niceshops\Bean\Type\Base\BeanInterface;
use Pars\Frontend\Cms\Form\Base\AbstractForm;

/**
 * Class PollForm
 * @package Pars\Frontend\Cms\Form\Poll
 */
class PollForm extends AbstractForm
{
    /**
     * @return string
     */
    public static function id(): string
    {
        return 'poll';
    }

    /**
     * @param array $data
     * @return array
     */
    protected function sanitize(array $data): array
    {
        $sanitize['ArticleData_Data']['option'] = $this->getSanitize()->string($data['option']);
        $sanitize['ArticleData_Data']['name'] = $this->getSanitize()->string($data['name']) ?? '';
        return $sanitize;
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function validate(array $data): bool
    {
        if (empty($data['option'])) {
            $this->getValidationHelper()->addError('option', $this->translate('poll.form.option.empty'));
        }
        if ($this->getBean()->get('Article_Data')['vote_once'] == 'true') {
            if ($this->getSession()->get('poll_vote_once', false)) {
                $this->getValidationHelper()->addError('option', $this->translate('poll.form.error.vote_once'));
            } else {
                $this->getSession()->set('poll_vote_once', true);
            }
        }
        return !$this->getValidationHelper()->hasError();
    }

    /**
     * @param BeanInterface $bean
     */
    protected function success(BeanInterface $bean)
    {
    }
}

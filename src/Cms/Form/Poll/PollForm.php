<?php


namespace Pars\Frontend\Cms\Form\Poll;


use Pars\Frontend\Cms\Form\Base\AbstractForm;

class PollForm extends AbstractForm
{
    public static function id(): string
    {
        return 'poll';
    }

    protected function sanitize(array $data): array
    {
        $sanitize['ArticleData_Data']['option'] = $this->getSanitize()->string($data['option']);
        $sanitize['ArticleData_Data']['name'] = $this->getSanitize()->string($data['name']) ?? '';
        return $sanitize;
    }

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

}

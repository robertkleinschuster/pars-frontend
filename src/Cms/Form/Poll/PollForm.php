<?php


namespace Pars\Frontend\Cms\Form\Poll;


use Pars\Frontend\Cms\Form\Base\AbstractForm;
use Pars\Model\Cms\Page\CmsPageBeanFinder;
use Pars\Model\Cms\Paragraph\CmsParagraphBean;
use Pars\Model\Cms\Paragraph\CmsParagraphBeanFinder;
use Pars\Model\Cms\Paragraph\CmsParagraphBeanProcessor;

class PollForm extends AbstractForm
{
    public static function id(): string
    {
        return 'poll';
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
        $paragraphFinder = new CmsParagraphBeanFinder($this->getAdapter());
        $paragraphFinder->setArticle_Code($data['Article_Code']);
        if ($paragraphFinder->count() === 1) {
            $bean = $paragraphFinder->getBean();
            if ($bean instanceof CmsParagraphBean) {
                if (!$bean->getArticle_Data()->exists('poll')) {
                    $bean->getArticle_Data()->set('poll', 1);
                } else {
                    $number = $bean->getArticle_Data()->get('poll');
                    $bean->getArticle_Data()->set('poll', $number + 1);
                }
                $name = $data['name'];
                if (!$bean->getArticle_Data()->exists('poll_names')) {
                    $bean->getArticle_Data()->set('poll_names', $data['name']);
                } else {
                    $names = $bean->getArticle_Data()->get('poll_names');
                    if (!empty($names)) {
                        $names = $names . ', ' . $name;
                    } else {
                        $names = $name;
                    }
                    $bean->getArticle_Data()->set('poll_names', $names);
                }
                $paragraphProcessor = new CmsParagraphBeanProcessor($this->getAdapter());
                $beanList = $paragraphFinder->getBeanFactory()->getEmptyBeanList();
                $beanList->push($bean);
                $paragraphProcessor->setBeanList($beanList);
                $paragraphProcessor->save();
                $pageFinder = new CmsPageBeanFinder($this->getAdapter());
                $pageFinder->setCmsPageState_Code('active');
                $pageFinder->setArticle_Code($data['Article_Code']);
                if ($pageFinder->count() === 1) {
                    $bean = $pageFinder->getBean();
                    if ($bean->getArticle_Data()->exists('vote_once')
                        && $bean->getArticle_Data()->get('vote_once') == 'true'
                    ) {
                        $this->getSession()->set('voted' . $bean->get('Article_Code'), true);
                    }
                }
            }
        }

        return true;
    }

}

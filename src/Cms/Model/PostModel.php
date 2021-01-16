<?php


namespace Pars\Frontend\Cms\Model;


use Pars\Frontend\Cms\Form\FormFactory;
use Pars\Model\Cms\Page\CmsPageBean;
use Pars\Model\Cms\Page\CmsPageBeanFinder;
use Pars\Model\Cms\Post\CmsPostBean;
use Pars\Model\Cms\Post\CmsPostBeanFinder;

class PostModel extends BaseModel
{
    protected ?CmsPostBean $page = null;

    /**
     * @param string|null $code
     * @param int|null $id
     * @return \Niceshops\Bean\Type\Base\BeanInterface|null
     */
    public function getPost(?string $code = null, int $id = null)
    {
        try {
            if (null === $this->page) {
                if ($code == null) {
                    $code = $this->getCode();
                }
                $finder = new CmsPostBeanFinder($this->getAdapter());
                $finder->initPublished($this->getConfig()->get('frontend.timezone'));
                $finder->setCmsPostState_Code('active');
                $finder->setArticleTranslation_Active(true);
                if ($id === null) {
                    $finder->setArticleTranslation_Code($code);
                } else {
                    $finder->setCmsPost_ID($id);
                }
                if ($finder->findByLocaleWithFallback($this->getLocale()->getLocale_Code(), 'de_AT') === 1) {
                    $bean = $finder->getBean();
                    if ($bean instanceof CmsPostBean) {
                        $this->page = $bean;
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->getLogger()->error('Error finding requested page.', ['exception' => $exception]);
        }
        return $this->page;
    }

    public function getSimilarPosts(CmsPostBean $bean)
    {
        $finder = new CmsPostBeanFinder($this->getAdapter());
        $finder->initPublished($this->getConfig()->get('frontend.timezone'));
        $finder->setCmsPostState_Code('active');
        $finder->setArticleTranslation_Active(true);
        $finder->findByLocaleWithFallback($this->getLocale()->getLocale_Code(), 'de_AT');
        $finder->limit(4, 0);
        $keywords = explode(',', $bean->ArticleTranslation_Keywords);
        foreach ($keywords as $keyword) {
            $finder->search(trim($keyword), ['ArticleTranslation_Keywords']);

        }
        return $finder->getBeanListDecorator();
    }

}

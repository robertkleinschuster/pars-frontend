<?php

namespace Pars\Frontend\Cms\Model;

use Niceshops\Bean\Finder\FinderBeanListDecorator;
use Niceshops\Bean\Type\Base\BeanException;
use Pars\Model\Cms\Post\CmsPostBean;
use Pars\Model\Cms\Post\CmsPostBeanFinder;

/**
 * Class PostModel
 * @package Pars\Frontend\Cms\Model
 */
class PostModel extends BaseModel
{
    protected ?CmsPostBean $page = null;

    /**
     * @param string|null $code
     * @param int|null $id
     * @return CmsPostBean|null
     */
    public function getPost(?string $code = null, int $id = null): ?CmsPostBean
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
                if ($finder->findByLocaleWithFallback($this->getLocale()->getLocale_Code(), $this->getConfig()->get('locale.default')) === 1) {
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

    /**
     * @param CmsPostBean $bean
     * @return FinderBeanListDecorator
     * @throws BeanException
     */
    public function getSimilarPosts(CmsPostBean $bean): FinderBeanListDecorator
    {
        $finder = new CmsPostBeanFinder($this->getAdapter());
        $finder->initPublished($this->getConfig()->get('frontend.timezone'));
        $finder->setArticle_ID($bean->Article_ID, true);
        $finder->setCmsPostState_Code('active');
        $finder->setArticleTranslation_Active(true);
        $finder->findByLocaleWithFallback(
            $this->getLocale()->getLocale_Code(),
            $this->getConfig()->get('locale.default')
        );
        $finder->limit(4, 0);
        $keywords = explode(',', $bean->ArticleTranslation_Keywords);
        foreach ($keywords as $keyword) {
            $finder->search(trim($keyword), ['ArticleTranslation_Keywords']);
        }
        return $finder->getBeanListDecorator();
    }
}

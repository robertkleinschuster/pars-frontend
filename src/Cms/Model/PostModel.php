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
                $pageFinder = new CmsPostBeanFinder($this->getAdapter());
                $pageFinder->initPublished($this->getConfig()->get('frontend.timezone'));
                $pageFinder->setCmsPostState_Code('active');
                $pageFinder->setArticleTranslation_Active(true);
                if ($id === null) {
                    $pageFinder->setArticleTranslation_Code($code);
                } else {
                    $pageFinder->setCmsPost_ID($id);
                }
                if ($pageFinder->findByLocaleWithFallback($this->getLocale()->getLocale_Code(), 'de_AT') === 1) {
                    $bean = $pageFinder->getBean();
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


    public function getForm()
    {
        $page = $this->getPage();
        $factory = new FormFactory();
        return $factory->createFormForPage(
            $page,
            $this->getAdapter(),
            $this->getSession(),
            $this->getGuard(),
            $this->getTranslator()
        );
    }
}

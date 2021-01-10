<?php


namespace Pars\Frontend\Cms\Model;


use Pars\Frontend\Cms\Form\FormFactory;
use Pars\Model\Cms\Page\CmsPageBean;
use Pars\Model\Cms\Page\CmsPageBeanFinder;

class PageModel extends BaseModel
{
    protected ?CmsPageBean $page = null;

    /**
     * @param string|null $code
     * @param int|null $id
     * @return \Niceshops\Bean\Type\Base\BeanInterface|null
     */
    public function getPage(?string $code = null, int $id = null)
    {
        try {
            if (null === $this->page) {
                if ($code == null) {
                    $code = $this->getCode();
                }
                $pageFinder = new CmsPageBeanFinder($this->getAdapter());
                $pageFinder->setCmsPageState_Code('active');
                if ($id === null) {
                    $pageFinder->setArticleTranslation_Code($code);
                } else {
                    $pageFinder->setCmsPage_ID($id);
                }
                if ($pageFinder->findByLocaleWithFallback($this->getLocale()->getLocale_Code(), 'de_AT') === 1) {
                    $bean = $pageFinder->getBean();
                    if ($bean instanceof CmsPageBean) {
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

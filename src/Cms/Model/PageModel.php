<?php


namespace Pars\Frontend\Cms\Model;


use Pars\Model\Cms\Page\CmsPageBeanFinder;

class PageModel extends BaseModel
{
    /**
     * @param string|null $code
     * @return \Niceshops\Bean\Type\Base\BeanInterface|null
     */
    public function getPage(?string $code = null, int $id = null)
    {
        try {
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
                return $pageFinder->getBean();
            }
        } catch (\Exception $exception) {
            $this->getLogger()->error('Error finding requested page.', ['exception' => $exception]);
        }
        return null;
    }
}

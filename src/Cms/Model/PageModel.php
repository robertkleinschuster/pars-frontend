<?php

namespace Pars\Frontend\Cms\Model;

use Niceshops\Bean\Type\Base\BeanException;
use Pars\Core\Cache\ParsCache;
use Pars\Frontend\Cms\Form\Contact\ContactForm;
use Pars\Frontend\Cms\Form\FormFactory;
use Pars\Frontend\Cms\Form\Poll\PollForm;
use Pars\Model\Cms\Page\CmsPageBean;
use Pars\Model\Cms\Page\CmsPageBeanFinder;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class PageModel
 * @package Pars\Frontend\Cms\Model
 */
class PageModel extends BaseModel
{
    protected ?CmsPageBean $page = null;

    /**
     * @param string|null $code
     * @param int|null $id
     * @return CmsPageBean|null
     * @throws InvalidArgumentException
     */
    public function getPage(?string $code = null, int $id = null): ?CmsPageBean
    {
        try {
            if (null === $this->page) {
                if ($code == null) {
                    $code = $this->getCode();
                }
                $cacheId = 'page';
                if ($code != '/') {
                    $cacheId .= $code;
                }
                if (isset($id)) {
                    $cacheId .= $id;
                }
                $cache = new ParsCache($cacheId);
                if ($cache->has($this->getLocale()->getLocale_Code())) {
                    return new CmsPageBean($cache->get($this->getLocale()->getLocale_Code()));
                }
                $pageFinder = new CmsPageBeanFinder($this->getAdapter());
                $pageFinder->initPublished($this->getConfig()->get('frontend.timezone'));
                $pageFinder->setCmsPageState_Code('active');
                $pageFinder->setArticleTranslation_Active(true);
                if ($id === null) {
                    $pageFinder->setArticleTranslation_Code($code);
                } else {
                    $pageFinder->setCmsPage_ID($id);
                }
                if (
                    $pageFinder->findByLocaleWithFallback(
                        $this->getLocale()->getLocale_Code(),
                        $this->getConfig()->get('locale.default')
                    ) === 1
                ) {
                    $bean = $pageFinder->getBean();
                    if ($bean instanceof CmsPageBean) {
                        $this->page = $bean;
                        if (!in_array($bean->CmsPageType_Code, ['tesla'])) {
                            $cache->set($this->getLocale()->getLocale_Code(), $bean->toArray(true), 3600);
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->getLogger()->error('Error finding requested page.', ['exception' => $exception]);
        }
        return $this->page;
    }

    /**
     * @return ContactForm|PollForm|null
     * @throws InvalidArgumentException
     * @throws BeanException
     */
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

<?php

namespace Pars\Frontend\Cms\Model;

use Pars\Core\Cache\ParsCache;
use Pars\Model\Cms\Menu\CmsMenuBeanFinder;
use Pars\Model\Cms\Menu\CmsMenuBeanList;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class MenuModel
 * @package Pars\Frontend\Cms\Model
 */
class MenuModel extends BaseModel
{
    /**
     * @return CmsMenuBeanList
     * @throws InvalidArgumentException
     */
    public function getMenuList(): CmsMenuBeanList
    {
        $cache = new ParsCache('menu');
        if ($cache->has($this->getLocale()->getLocale_Code())) {
            return new CmsMenuBeanList($cache->get($this->getLocale()->getLocale_Code()));
        }
        $menuFinder = new CmsMenuBeanFinder($this->getAdapter());
        $menuFinder->setCmsMenuState_Code('active');
        $menuFinder->order(['CmsMenuType_Code']);
        $menuFinder->setCmsMenu_ID_Parent(null);
        $menuFinder->setArticleTranslation_Active(true);
        $menuFinder->addLinkedFinder(
            new CmsMenuBeanFinder($this->getAdapter()),
            'Menu_BeanList',
            'CmsMenu_ID',
            'CmsMenu_ID_Parent'
        );
        $menuFinder->findByLocaleWithFallback($this->getLocale()->getLocale_Code(), $this->getConfig()->get('locale.default'));
        $list = $menuFinder->getBeanList();
        $cache->set($this->getLocale()->getLocale_Code(), $list->toArray(true), 86400);
        return $list;
    }
}

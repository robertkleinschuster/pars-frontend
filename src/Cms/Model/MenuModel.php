<?php


namespace Pars\Frontend\Cms\Model;


use Pars\Model\Cms\Menu\CmsMenuBeanFinder;

class MenuModel extends BaseModel
{
    public function getMenuList()
    {
        $menuFinder = new CmsMenuBeanFinder($this->getAdapter());
        $menuFinder->setCmsMenuState_Code('active');
        $menuFinder->order(['CmsMenuType_Code']);
        $menuFinder->setCmsMenu_ID_Parent(null);
        $menuFinder->setArticleTranslation_Active(true);
        $menuFinder->findByLocaleWithFallback($this->getLocale()->getLocale_Code(), 'de_AT');
        $menuFinder->addLinkedFinder(
            new CmsMenuBeanFinder($this->getAdapter()),
            'Menu_BeanList',
            'CmsMenu_ID',
            'CmsMenu_ID_Parent'
        );
        return $menuFinder->getBeanList();
    }
}

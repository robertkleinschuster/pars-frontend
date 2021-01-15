<?php


namespace Pars\Frontend\Cms\Model;


use Pars\Core\Cache\ParsCache;
use Pars\Model\Localization\Locale\LocaleBeanFinder;
use Pars\Model\Localization\Locale\LocaleBeanList;

class LocaleModel extends BaseModel
{
    public function getLocaleList()
    {
        $cache = new ParsCache('locale');
        if ($cache->has('list')) {
            return new LocaleBeanList($cache->get('list'));
        }
        $localeFinder = new LocaleBeanFinder($this->getAdapter());
        $localeFinder->setLocale_Active(true);
        $list = $localeFinder->getBeanList();
        $cache->set('list', $list->toArray(true), 86400);
        return $list;
    }
}

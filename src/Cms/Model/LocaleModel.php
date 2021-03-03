<?php

namespace Pars\Frontend\Cms\Model;

use Pars\Core\Cache\ParsCache;
use Pars\Model\Localization\Locale\LocaleBeanFinder;
use Pars\Model\Localization\Locale\LocaleBeanList;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class LocaleModel
 * @package Pars\Frontend\Cms\Model
 */
class LocaleModel extends BaseModel
{
    /**
     * @return LocaleBeanList
     * @throws InvalidArgumentException
     */
    public function getLocaleList(): LocaleBeanList
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

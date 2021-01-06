<?php


namespace Pars\Frontend\Cms\Model;


use Pars\Model\Localization\Locale\LocaleBeanFinder;

class LocaleModel extends BaseModel
{
    public function getLocaleList()
    {
        $localeFinder = new LocaleBeanFinder($this->getAdapter());
        $localeFinder->setLocale_Active(true);
        return $localeFinder->getBeanList();
    }
}

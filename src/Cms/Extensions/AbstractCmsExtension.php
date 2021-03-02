<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

abstract class AbstractCmsExtension implements ExtensionInterface
{
    protected Engine $engine;

    public function register(Engine $engine)
    {
        $this->engine = $engine;
    }

}

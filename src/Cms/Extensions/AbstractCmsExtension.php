<?php

namespace Pars\Frontend\Cms\Extensions;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use League\Plates\Template\Template;

abstract class AbstractCmsExtension implements ExtensionInterface
{
    protected Engine $engine;
    public ?Template $template = null;

    public function register(Engine $engine)
    {
        $this->engine = $engine;
    }
}

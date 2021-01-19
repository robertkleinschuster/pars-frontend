<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class BackgroundImageExtension implements ExtensionInterface
{
    protected $file = null;
    public function register(Engine $engine)
    {
        $engine->registerFunction('bg', function ($file) {
            $this->file = $file;
        });
        $engine->registerFunction('bgflush', function () {
           return $this->file;
        });
    }

}

<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class OpenGraphExtension implements ExtensionInterface
{

    protected array $data = [];

    public function register(Engine $engine)
    {
        $engine->registerFunction('og', function ($property, $content) {
            if (!isset($this->data[$property])) {
                $this->data[$property] = $content;
            }
        });
        $engine->registerFunction('ogflush', function () {
            $ret = '';
            foreach ($this->data as $property => $content) {
                $ret .= "<meta property=\"$property\" content=\"$content\" />";
            }
            return $ret;
        });
    }

}

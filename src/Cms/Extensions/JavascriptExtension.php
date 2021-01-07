<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class JavascriptExtension implements ExtensionInterface
{
    protected array $data = [];

    public function register(Engine $engine)
    {
        $engine->registerFunction('js', function ($file, $critical = false) {
            $this->data[$file] = $critical;
        });
        $engine->registerFunction('jsflush', function () {
            $ret = "";
            foreach ($this->data as $file => $critical) {
                if ($critical) {
                    $ret .= "<script src=\"$file\"></script>";
                } else {
                    $ret .= "<script class=\"script-insertion\" data-src=\"$file\"></script>";
                }
            }
            return $ret;
        });
    }

}

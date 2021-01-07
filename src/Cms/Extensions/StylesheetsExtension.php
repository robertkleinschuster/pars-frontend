<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class StylesheetsExtension implements ExtensionInterface
{

    protected array $data = [];

    public function register(Engine $engine)
    {
        $engine->registerFunction('css', function ($file, $critical = false) {
            $this->data[$file] = $critical;
        });
        $engine->registerFunction('cssflush', function () {
            $ret = "";
            foreach ($this->data as $file => $critical) {
                if ($critical) {
                    $ret .= "<link rel=\"stylesheet\" href=\"$file\">";
                } else {
                    $ret .= " 
    <link rel=\"preload\" href=\"$file\" as=\"style\" onload=\"this.onload=null;this.rel='stylesheet'\">
    <noscript>
        <link rel=\"stylesheet\" href=\"$file\">
    </noscript>";
                }
            }
            return $ret;
        });
    }

}

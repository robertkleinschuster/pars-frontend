<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Mezzio\Session\SessionInterface;

class StylesheetsExtension implements ExtensionInterface
{

    protected array $data = [];

    public function register(Engine $engine)
    {
        $engine->registerFunction('css', function ($file, $critical = false) {
            $this->data[$file] = $critical;
        });
        $engine->registerFunction('cssflush', function ($session = null) {
            $allcritical = false;
            if ($session instanceof SessionInterface) {
                $allcritical = $session->has('cssflush');
                if (!$allcritical && in_array(true, $this->data)) {
                    $session->set('cssflush', 'true');
                }
            }
            $ret = "";
            foreach ($this->data as $file => $critical) {
                if ($critical || $allcritical) {
                    $ret .= "<link rel=\"stylesheet\" href=\"$file\">";
                } else {
                    $ret .= " 
    <link rel=\"preload\" href=\"$file\" as=\"style\" class='style-insertion' onload=\"this.onload=null;this.rel='stylesheet'\">
    <noscript>
        <link rel=\"stylesheet\" href=\"$file\">
    </noscript>";
                }
            }
            return $ret;
        });
    }

}

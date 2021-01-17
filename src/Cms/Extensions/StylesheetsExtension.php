<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Mezzio\Session\SessionInterface;
use Pars\Core\Cache\ParsCache;

class StylesheetsExtension implements ExtensionInterface
{

    protected array $data = [];

    public function register(Engine $engine)
    {
        $engine->registerFunction('css', function ($file, $critical = false) {
            $this->data[$file] = $critical;
        });
        $engine->registerFunction('cssflush', function ($session = null) {
            $ret = "";
            $cache = new ParsCache('cssflush');
            foreach ($this->data as $file => $critical) {
                $cacheID = md5($file);
                if ($critical) {
                    if ($cache->has($cacheID)) {
                        $ret = $cache->get($cacheID);
                    } else {
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
                            $ret .= "<style>" . file_get_contents($_SERVER['DOCUMENT_ROOT'] . $file) . "</style>";
                            $cache->set($cacheID, $ret);
                        }
                    }
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

<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Pars\Core\Cache\ParsCache;

class StylesheetsExtension implements ExtensionInterface
{

    protected array $data = [];

    public function register(Engine $engine)
    {
        $engine->registerFunction('css', function ($file, $critical = false) use ($engine) {
            $hash = $engine->getData()['hash'];
            $file = $this->injectHash($file, $hash);
            if (strpos($file, '/') !== 0) {
                $file = "/$file";
            }
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
    protected function injectHash(string $filename, string $hash): string
    {
        $exp = explode('.', $filename);
        $result = '';
        $count = count($exp);
        foreach ($exp as $key => $item) {
            if ($key === $count - 1) {
                $result .= '_' . $hash;
                $result .= '.' . $item;

            } else {
                $result .= $item;
            }
        }
        return $result;
    }
}

<?php

namespace Pars\Frontend\Cms\Extensions;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Pars\Core\Cache\ParsCache;
use Pars\Helper\Filesystem\FilesystemHelper;

class JavascriptExtension implements ExtensionInterface
{
    protected array $data = [];

    public function register(Engine $engine)
    {
        $engine->registerFunction('js', function ($file, $critical = false) use ($engine) {
            $hash = $engine->getData()['hash'];
            $file = $this->injectHash($file, $hash);
            if (strpos($file, '/') !== 0) {
                $file = "/$file";
            }
            $this->data[$file] = $critical;
        });
        $engine->registerFunction('jsflush', function () {
            $ret = "";
            $cache = new ParsCache('jsflush');
            foreach ($this->data as $file => $critical) {
                $cacheID = md5($file);
                if ($critical) {
                    if ($cache->has($cacheID)) {
                        $ret = $cache->get($cacheID);
                    } else {
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
                            $ret .= "<script>" . file_get_contents($_SERVER['DOCUMENT_ROOT'] . $file) . "</script>";
                            $cache->set($cacheID, $ret);
                        }
                    }
                } else {
                    $ret .= "<script class=\"script-insertion\" data-src=\"$file\"></script>";
                }
            }
            return $ret;
        });
    }

    protected function injectHash(string $filename, string $hash): string
    {
        return FilesystemHelper::injectHash($filename, $hash);
    }
}

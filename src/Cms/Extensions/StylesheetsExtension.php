<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Pars\Core\Cache\ParsCache;
use Pars\Helper\Filesystem\FilesystemHelper;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class StylesheetsExtension
 * @package Pars\Frontend\Cms\Extensions
 */
class StylesheetsExtension implements ExtensionInterface
{
    /**
     * @var array
     */
    protected array $data = [];

    /**
     * @var Engine
     */
    protected Engine $engine;

    /**
     * @return Engine
     */
    public function getEngine(): Engine
    {
        return $this->engine;
    }

    /**
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $this->engine = $engine;
        $engine->registerFunction('css', [$this, 'css']);
        $engine->registerFunction('cssflush', [$this, 'cssflush']);
    }

    /**
     *
     * @param string $file
     * path to css file (name of bundle)
     * @param int $order
     * soring for stylesheets
     * Files with order from 0 - 9 will be output as inline all other in the specified oder
     * example:
     * $this->css("critical.css", 0)
     * $this->css("base.css", 10)
     * $this->css("cms.css", 20)
     * $this->css("noscript.css", 30)
     */
    public function css(string $file, int $order = 10)
    {
        $hash = $this->getEngine()->getData()['hash'];
        $file = $this->injectHash($file, $hash);
        if (strpos($file, '/') !== 0) {
            $file = "/$file";
        }
        $this->data[$file] = $order;
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public function cssflush(): string
    {
        $ret = "";
        asort($this->data, SORT_NATURAL);
        foreach ($this->data as $file => $order) {
            if ($order < 10) {
                $ret .= $this->getInlineFromCache($file);
            } else {
                if (strpos($file, 'noscript') !== false) {
                    $ret .= "<noscript><link rel=\"stylesheet\" href=\"$file\"></noscript>";
                } else {
                    $ret .= "<link rel=\"preload\" href=\"$file\" as=\"style\" class='style-insertion'>";
                    $ret .= "<noscript><link rel=\"stylesheet\" href=\"$file\"></noscript>";
                }

            }
        }
        return $ret;
    }

    /**
     * @param string $file
     * @return string
     * @throws InvalidArgumentException
     */
    protected function getInlineFromCache(string $file): string
    {
        $cache = new ParsCache('stylesheets');
        $ret = "";
        $cacheID = md5($file);
        if ($cache->has($cacheID)) {
            $ret = $cache->get($cacheID);
        } else {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
                $ret = "<style>" . file_get_contents($_SERVER['DOCUMENT_ROOT'] . $file) . "</style>";
                $cache->set($cacheID, $ret);
            }
        }
        return $ret;
    }

    /**
     * @param string $filename
     * @param string $hash
     * @return string
     */
    protected function injectHash(string $filename, string $hash): string
    {
        return FilesystemHelper::injectHash($filename, $hash);
    }
}

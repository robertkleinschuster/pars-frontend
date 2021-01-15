<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Glide\Signatures\SignatureFactory;
use League\Glide\Urls\UrlBuilderFactory;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Niceshops\Bean\Type\Base\BeanInterface;
use Pars\Component\Base\Grid\Container;
use Pars\Core\Cache\ParsCache;
use Psr\Container\ContainerInterface;

class ImagePathExtension implements ExtensionInterface
{
    public function register(Engine $engine)
    {
        $engine->registerFunction('cmsimg', function ($img, $static = null, $key = null, $width = null, $height = null, $density = null, $format = null, $fit = null) {
            if ($img instanceof BeanInterface) {
                $img = $img->FileDirectory_Code . '/' . $img->File_Code . '.' . $img->FileType_Code;
            }
            if (strpos($static, '/img') !== false) {
                $params = [];
                if ($width) {
                    $params['w'] = $width;
                }
                if ($height) {
                    $params['h'] = $height;
                }
                if ($density) {
                    $params['dpr'] = $density;
                }
                if ($format) {
                    $params['fm'] = $format;
                }
                if ($fit) {
                    $params['fit'] = $fit;
                }
                $params['file'] = $img;
                $cache = new ParsCache('image');
                $cacheID = md5(implode(';', $params));
                if ($cache->has($cacheID)) {
                    $ret = $cache->get($cacheID);
                } else {
                    if (file_exists('data/image_signature')) {
                        $key = file_get_contents('data/image_signature');
                    }
                    $url = UrlBuilderFactory::create($static, $key);
                    $ret = $url->getUrl('', $params);
                    unset($params['s'], $params['p']);
                    ksort($params);
                    $ext = (isset($params['fm']) ? $params['fm'] : pathinfo($img)['extension']);
                    $ext = ($ext === 'pjpg') ? 'jpg' : $ext;
                    $md5 = md5($img.'?'.http_build_query($params));
                    $cache->set($cacheID, '/c/' . $img . '/' . $md5 . '.' .$ext);
                }
            } else {
                $ret = $static . $img;
            }
            return $ret;
        });
    }

}

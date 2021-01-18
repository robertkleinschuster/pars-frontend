<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Glide\Urls\UrlBuilderFactory;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Niceshops\Bean\Type\Base\BeanInterface;
use Pars\Core\Cache\ParsCache;

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
                $params['q'] = 50;
                $params['file'] = $img;
                $p = $params;
                $cache = new ParsCache('image');
                unset($p['s'], $p['p']);
                ksort($p);
                $ext = (isset($p['fm']) ? $p['fm'] : pathinfo($img)['extension']);
                $ext = ($ext === 'pjpg') ? 'jpg' : $ext;
                $md5 = md5($img.'?'.http_build_query($p));
                if ($cache->has($md5)) {
                    $ret = $cache->get($md5);
                } else {
                    if (file_exists('data/image_signature')) {
                        $key = file_get_contents('data/image_signature');
                    }
                    $url = UrlBuilderFactory::create($static, $key);
                    $ret = $url->getUrl('', $params);
               #     $cache->set($md5, str_replace('/img', '', $static) . '/c/' . $img . '/' . $md5 . '.' .$ext, 3600);
                }
            } else {
                $ret = $static . $img;
            }
            return $ret;
        });
    }

}

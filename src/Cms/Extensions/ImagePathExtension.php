<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Glide\Signatures\SignatureFactory;
use League\Glide\Urls\UrlBuilderFactory;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Niceshops\Bean\Type\Base\BeanInterface;

class ImagePathExtension implements ExtensionInterface
{
    public function register(Engine $engine)
    {
        $engine->registerFunction('cmsimg', function ($img, $static = null, $width = null, $height = null, $density = null, $format = null) {
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
                $params['file'] = $img;
                $url = UrlBuilderFactory::create($static, 'pars-sign');
                return $url->getUrl('', $params);
            } else {
                return $static . '/upload/' . $img;
            }
        });
    }

}

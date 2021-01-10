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
                if (file_exists('data/image_signature')) {
                    $key = file_get_contents('data/image_signature');
                }
                $url = UrlBuilderFactory::create($static, $key);
                return $url->getUrl('', $params);
            } else {
                return $static . $img;
            }
        });
    }

}

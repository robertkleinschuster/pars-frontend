<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Niceshops\Bean\Type\Base\BeanInterface;
use Niceshops\Bean\Type\Base\BeanListInterface;

class CmsContentExtension implements ExtensionInterface
{
    public function register(Engine $engine)
    {
        $engine->registerFunction('page', function (BeanInterface $bean) use ($engine) {
            return $engine->render($bean->get('CmsPageType_Template'), ['page' => $bean]);
        });
        $engine->registerFunction('menu', function (BeanListInterface $menu, string $type, ?string $host = null) use ($engine) {
            $menu = $menu->filter(function ($bean) use ($type, $host) {
                return $bean->get('CmsMenuType_Code') == $type && ($bean->empty('ArticleTranslation_Host') || empty($host) || $bean->get('ArticleTranslation_Host') == $host);
            });
            if ($menu->count()) {
                return $engine->render($menu->first()->get('CmsMenuType_Template'), ['menu' => $menu]);
            }
            return '';
        });
        $engine->registerFunction('paragraph', function (BeanInterface $bean, $data = []) use ($engine) {
            return $engine->render($bean->get('CmsParagraphType_Template'), array_replace(['paragraph' => $bean], $data));
        });
        $engine->registerFunction('post', function (BeanInterface $bean) use ($engine) {
            return $engine->render($bean->get('CmsPostType_Template'), ['post' => $bean]);
        });
        $engine->registerFunction('file', function (BeanInterface $bean, $data = []) use ($engine) {
            return $engine->render('file::' . $bean->get('FileType_Code'), array_replace(['file' => $bean], $data));
        });
    }

}

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
            return $engine->render($bean->get('CmsPageType_Template'), ['bean' => $bean]);
        });
        $engine->registerFunction('menu', function (BeanListInterface $menu, string $type) use ($engine) {
            $menu = $menu->filter(function ($bean) use ($type) {
                return $bean->get('CmsMenuType_Code') == $type;
            });
            if ($menu->count()) {
                return $engine->render($menu->first()->get('CmsMenuType_Template'), ['menu' => $menu]);
            }
            return '';
        });
        $engine->registerFunction('paragraph', function (BeanInterface $bean) use ($engine) {
            return $engine->render($bean->get('CmsParagraphType_Template'), ['bean' => $bean]);
        });
        $engine->registerFunction('post', function (BeanInterface $bean) use ($engine) {
            return $engine->render($bean->get('CmsPostType_Template'), ['bean' => $bean]);
        });
        $engine->registerFunction('file', function (BeanInterface $bean) use ($engine) {
            return $engine->render('file::' . $bean->get('FileType_Code'), ['bean' => $bean]);
        });
    }

}

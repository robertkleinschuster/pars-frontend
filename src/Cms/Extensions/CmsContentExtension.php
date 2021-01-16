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
        $engine->registerFunction('page', function (BeanInterface $bean, $data = []) use ($engine) {
            return $engine->render($bean->get('CmsPageType_Template'), array_replace(['page' => $bean], $data));
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
        $engine->registerFunction('post', function (BeanInterface $bean, $data = []) use ($engine) {
            return $engine->render($bean->get('CmsPostType_Template'), array_replace(['post' => $bean], $data));
        });
        $engine->registerFunction('file', function ($bean, $data = []) use ($engine) {
            if ($bean instanceof BeanListInterface) {
                $ret = '';
                foreach ($bean as $item) {
                    $ret .= $engine->render('file::' . $item->get('FileType_Code'), array_replace(['file' => $item], $data));
                }
                return $ret;
            }
            if ($bean instanceof BeanInterface) {
                return $engine->render('file::' . $bean->get('FileType_Code'), array_replace(['file' => $bean], $data));
            }
            return '';
        });
        $engine->registerFunction('date', function ($date){
            if ($date instanceof \DateTime) {
                return $date->format('j.n.Y');
            }
            return '';
        });
        $engine->registerFunction('time', function ($date){
            if ($date instanceof \DateTime) {
                return $date->format('G:i');
            }
            return '';
        });
        $engine->registerFunction('datetime', function ($date){
            if ($date instanceof \DateTime) {
                return $date->format('j.n.Y G:i');
            }
            return '';
        });
    }

}

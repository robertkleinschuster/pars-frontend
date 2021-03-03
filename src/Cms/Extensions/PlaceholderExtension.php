<?php

namespace Pars\Frontend\Cms\Extensions;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Laminas\I18n\Translator\TranslatorInterface;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use League\Plates\Template\Template;
use Pars\Frontend\Cms\Form\FormFactory;
use Pars\Helper\String\StringHelper;
use Pars\Model\Cms\Block\CmsBlockBeanFinder;

class PlaceholderExtension implements ExtensionInterface, TranslatorAwareInterface
{
    use TranslatorAwareTrait;
    use AdapterAwareTrait;

    protected Engine $engine;
    protected array $blockBean_Map = [];
    public ?Template $template = null;
    /**
     * TranslatorExtension constructor.
     */
    public function __construct(TranslatorInterface $translator, Adapter $adapter)
    {
        $this->setTranslator($translator);
        $this->setDbAdapter($adapter);
    }

    public function register(Engine $engine)
    {
        $this->engine = $engine;
        $engine->registerFunction('p', function (?string $message, array $data = []) use ($engine) {
            $matches = [];
            preg_match_all('/\{.*?\}|%7B.*?%7D|%257B.*?%257D/', $message, $matches);
            $replace = [];
            $data = array_replace($engine->getData(), $data);
            $this->generateReplace($replace, $matches, $data);
            return str_replace(array_keys($replace), array_values($replace), $message);
        });
    }

    /**
     * @param array $replace
     * @param array $matches
     * @param array $data
     */
    protected function generateReplace(array &$replace, array $matches, array $data)
    {
        $blockCodes = [];
        foreach ($matches as $match) {
            if (is_array($match)) {
                $this->generateReplace($replace, $match, $data);
            } else {
                $key = trim(str_replace('}', '', str_replace('{', '', $match)));
                if (StringHelper::startsWith($key, 'block:')) {
                    $exp = explode(':', $key);
                    if (is_array($exp) && isset($exp[1])) {
                        $blockCodes[$exp[1]] = $match;
                    }
                } else {
                    if (isset($data[$key])) {
                        $replace[$match] = $data[$key];
                    } else {
                        $replace[$match] = $this->getTranslator()->translate($key, 'frontend');
                    }
                }
            }
        }
        $codeList = array_keys($blockCodes);
        $blockmap = $this->getBlock_Bean_Map($codeList, $data['locale']);
        foreach ($blockmap as $code => $bean) {
            $formFactory = new FormFactory();
            $form = $formFactory->createFormForBean($bean, $this->adapter, $data['session'], $data['guard'], $this->translator);
            $this->engine->addData(['form' => $form]);
            $replace[$blockCodes[$code]] = $this->engine->getFunction('block')->call($this->template, [$bean]);
        }
    }

    /**
     * @param array $codeList
     * @param string $locale
     * @return array|mixed
     * @throws \Niceshops\Bean\Type\Base\BeanException
     */
    protected function getBlock_Bean_Map(array $codeList, string $locale): array
    {
        $id = md5(implode($codeList) . $locale);
        if (isset($this->blockBean_Map[$id])) {
            return $this->blockBean_Map[$id];
        } else {
            $this->blockBean_Map[$id] = [];
        }
        $finder = new CmsBlockBeanFinder($this->adapter);
        $finder->setArticle_Code_List($codeList);
        $finder->setCmsBlockState_Code('active');
        $finder->setArticleTranslation_Active(true);
        $finder->findByLocaleWithFallback($locale, 'de_AT');
        foreach ($finder->getBeanListDecorator() as $bean) {
            $this->blockBean_Map[$id][$bean->get('Article_Code')] = $bean;
        }
        return $this->blockBean_Map[$id];
    }
}

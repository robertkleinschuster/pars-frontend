<?php


namespace Pars\Frontend\Cms\Extensions;


use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Laminas\I18n\Translator\TranslatorInterface;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class PlaceholderExtension implements ExtensionInterface, TranslatorAwareInterface
{
    use TranslatorAwareTrait;


    /**
     * TranslatorExtension constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('p', function ($message) {
            $matches = [];
            preg_match_all('/\{.*?\}|%7B.*?%7D|%257B.*?%257D/', $message, $matches);
            $replace = [];
            $this->generateReplace($replace, $matches);
            return str_replace(array_keys($replace), array_values($replace), $message);
        });
    }

    /**
     * @param array $replace
     * @param array $matches
     */
    protected function generateReplace(array &$replace, array $matches)
    {
        foreach ($matches as $match) {
            if (is_array($match)) {
                $this->generateReplace($replace, $match);
            } else {
                $key = str_replace('}', '', str_replace('{', '', $match));
                $replace[$match] = $this->getTranslator()->translate($key, 'frontend');
            }
        }
    }
}

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
        $engine->registerFunction('p', function (?string $message, array $data = []) use($engine) {
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
        foreach ($matches as $match) {
            if (is_array($match)) {
                $this->generateReplace($replace, $match, $data);
            } else {
                $key = str_replace('}', '', str_replace('{', '', $match));
                if (isset($data[$key])) {
                    $replace[$match] = $data[$key];
                } else {
                    $replace[$match] = $this->getTranslator()->translate($key, 'frontend');
                }
            }
        }
    }
}

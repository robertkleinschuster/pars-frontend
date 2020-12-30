<?php

namespace Pars\Frontend\Cms\Helper;

use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;

class CmsPlaceholder implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    private $locale;

    /**
     * CmsPlaceholder constructor.
     * @param $locale
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    public function replace(?string $content): ?string
    {
        $messages = $this->getTranslator()->getAllMessages('default', $this->locale);
        if ($messages instanceof \ArrayObject) {
            $messages = (array)$messages;
        }
        if (is_array($messages)) {
            $keys = array_map(function ($x) {
                return "{{$x}}";
            }, array_keys($messages));
            $content = str_replace($keys, array_values($messages), $content);
        }
        return $content;
    }
}

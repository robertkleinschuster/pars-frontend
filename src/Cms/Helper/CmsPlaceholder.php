<?php

namespace Pars\Frontend\Cms\Helper;

use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;

/**
 * Class CmsPlaceholder
 * @package Pars\Frontend\Cms\Helper
 */
class CmsPlaceholder implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    private string $locale;

    /**
     * CmsPlaceholder constructor.
     * @param string $locale
     */
    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * @param string|null $content
     * @return string|null
     */
    public function __invoke(?string $content): ?string
    {
        return $this->replace($content);
    }

    /**
     * @param string|null $content
     * @return string|null
     */
    public function replace(?string $content): ?string
    {
        $messages = $this->getTranslator()->getAllMessages('frontend', $this->locale);
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

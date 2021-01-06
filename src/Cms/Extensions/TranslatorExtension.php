<?php


namespace Pars\Frontend\Cms\Extensions;


use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Laminas\I18n\Translator\TranslatorInterface;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class TranslatorExtension implements ExtensionInterface, TranslatorAwareInterface
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
        $engine->registerFunction('t', function ($message) {

            return $this->getTranslator()->translate($message, 'frontend');
        });
    }

}

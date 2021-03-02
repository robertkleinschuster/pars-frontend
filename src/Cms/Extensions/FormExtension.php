<?php


namespace Pars\Frontend\Cms\Extensions;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Pars\Frontend\Cms\Form\Base\AbstractForm;

class FormExtension implements ExtensionInterface
{
    protected $opened = false;
    protected $class = null;
    public function register(Engine $engine)
    {
        $engine->registerFunction('form', function (AbstractForm $form = null, $class = null) {
            if ($this->opened) {
                $this->opened = false;
                $content = ob_get_clean();
                $html = "<form";
                if ($form) {
                    $method = $form->getMethod();
                    $html .= " method=\"$method\"";
                    if ($form->hasAction()) {
                        $action = $form->getAction();
                        $html .= " action=\"$action\"";
                    }

                }
                if ($this->class) {
                    $html .= " class=\"{$this->class}\"";
                }
                $html .= '>';
                if ($form) {
                    $name = $form::PARAMETER_TOKEN;
                    $value = $form->generateToken();
                    $html .= "<input type=\"hidden\" name=\"$name\" value=\"$value\">";
                    $name = $form::PARAMETER_ID;
                    $value = $form::id();
                    $html .= "<input type=\"hidden\" name=\"$name\" value=\"$value\">";
                    if ($form->hasBean() && $form->getBean()->exists('Article_ID')) {
                        $name = 'Article_ID';
                        $value = $form->getBean()->get('Article_ID');
                        $html .= "<input type=\"hidden\" name=\"$name\" value=\"$value\">";

                    }
                }
                $html .= "{$content}";
                $html .= "</form>";
                return $html;
            } else {
                $this->opened = true;
                $this->class = $class;
                ob_start();
            }
            return '';
        });
    }

}

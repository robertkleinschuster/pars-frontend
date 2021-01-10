<?php


namespace Pars\Frontend\Cms\Form\Base;


class Sanitize
{
    protected function fromArray($data, $key)
    {
        if ($key && is_array($data)) {
            if (!empty($data[$key])) {
                $data = $data[$key];
            } else {
                $data = null;
            }
        }
        return $data;
    }

    public function email($email, $key = null)
    {
        $email = $this->fromArray($email, $key);
        if (!empty($email)) {
            return (string)filter_var($email, FILTER_SANITIZE_EMAIL);
        }
        return null;
    }

    public function string($text, $key = null)
    {
        $text = $this->fromArray($text, $key);
        if (!empty($text)) {
            return (string)filter_var($text, FILTER_SANITIZE_STRING);
        }
        return null;
    }

    public function text($text, $key = null)
    {
        return $this->string($text, $key);
    }

    public function url($url, $key = null)
    {
        $url = $this->fromArray($url, $key);
        if (!empty($url)) {
            return (string)filter_var($url, FILTER_SANITIZE_URL);
        }
        return null;
    }

    public function int($int, $key = null)
    {
        $int = $this->fromArray($int, $key);
        if (!empty($int)) {
            return (int)filter_var($int, FILTER_SANITIZE_NUMBER_INT);
        }
        return null;
    }

    public function float($float, $key = null)
    {
        $float = $this->fromArray($float, $key);
        if (!empty($float)) {
            return (float)filter_var($float, FILTER_SANITIZE_NUMBER_FLOAT);
        }
        return null;
    }
}

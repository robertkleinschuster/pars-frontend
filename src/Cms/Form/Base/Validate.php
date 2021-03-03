<?php

namespace Pars\Frontend\Cms\Form\Base;

class Validate
{
    public function boolean($boolean): bool
    {
        return filter_var($boolean, FILTER_VALIDATE_BOOLEAN) !== null;
    }

    public function domain($domain): bool
    {
        return filter_var($domain, FILTER_VALIDATE_DOMAIN);
    }

    public function email($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function float($float, int $min, int $max, int $decimal): bool
    {
        return filter_var($float, FILTER_VALIDATE_FLOAT, [
            'min_range' => $min,
            'max_range' => $max,
            'decimal' => $decimal
        ]);
    }

    public function int($int, int $min, int $max): bool
    {
        return filter_var($int, FILTER_VALIDATE_INT, [
            'min_range' => $min,
            'max_range' => $max,
        ]);
    }

    public function ip($ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    public function mac($mac): bool
    {
        return filter_var($mac, FILTER_VALIDATE_MAC);
    }

    public function url($url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}

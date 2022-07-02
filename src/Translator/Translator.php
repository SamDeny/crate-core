<?php declare(strict_types=1);

namespace Crate\Core\Translator;

use Citrus\Contracts\SingletonContract;

class Translator implements SingletonContract
{

    public function translate(string $key, ?string $default = null)
    {
        return $default;
    }

    public function trans(string $key, ?string $default = null)
    {
        return $this->translate($key, $default);
    }

}

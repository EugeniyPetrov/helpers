<?php

namespace Eugeniypetrov\Lib\Service\TrustedProxiesLoader;

interface TrustedProxiesLoader
{
    /**
     * @return string[]
     */
    public function load(): array;
}

<?php

namespace Eugeniypetrov\Lib\Service\TrustedProxiesLoader;

class ArrayTrusedProxiesLoader implements TrustedProxiesLoader
{
    /**
     * @var array
     */
    private $proxies;

    /**
     * ArrayTrusedProxiesLoader constructor.
     * @param array $proxies
     */
    public function __construct(array $proxies)
    {
        $this->proxies = $proxies;
    }

    public function load(): array
    {
        return $this->proxies;
    }
}

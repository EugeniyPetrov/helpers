<?php

namespace Eugeniypetrov\Lib\Service\TrustedProxiesLoader;

class MultipleTrustedProxiesLoader implements TrustedProxiesLoader
{
    /**
     * @var array|TrustedProxiesLoader[]
     */
    private $proxyLoader;

    /**
     * MultipleTrustedProxiesLoader constructor.
     * @param TrustedProxiesLoader[] $proxyLoader
     */
    public function __construct(array $proxyLoader)
    {
        $this->proxyLoader = $proxyLoader;
    }

    public function load(): array
    {
        $proxies = [];

        foreach ($this->proxyLoader as $loader) {
            $proxies = array_merge($proxies, $loader->load());
        }

        return $proxies;
    }
}

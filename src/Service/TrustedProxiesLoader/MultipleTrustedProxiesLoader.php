<?php

namespace Eugeniypetrov\Lib\Service\TrustedProxiesLoader;

class MultipleTrustedProxiesLoader implements TrustedProxiesLoader
{
    /**
     * @var array|TrustedProxiesLoader[]
     */
    private $proxyLoaders;

    /**
     * MultipleTrustedProxiesLoader constructor.
     * @param TrustedProxiesLoader[] $proxyLoaders
     */
    public function __construct(array $proxyLoaders)
    {
        $this->proxyLoaders = $proxyLoaders;
    }

    public function load(): array
    {
        $proxies = [];

        foreach ($this->proxyLoaders as $loader) {
            $proxies = array_merge($proxies, $loader->load());
        }

        return $proxies;
    }
}

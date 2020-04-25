<?php

namespace Eugeniypetrov\Lib\Service\TrustedProxiesLoader;

class RemotePlaintextProxiesLoader implements TrustedProxiesLoader
{
    /**
     * @var RemoteFileLoader
     */
    private $fileLoader;

    /**
     * RemotePlaintextProxiesLoader constructor.
     * @param RemoteFileLoader $fileLoader
     */
    public function __construct(RemoteFileLoader $fileLoader)
    {
        $this->fileLoader = $fileLoader;
    }

    private function parseProxies(string $content): array
    {
        return array_values(array_filter(array_map("trim", preg_split("~\n~", $content))));
    }

    public function load(): array
    {
        return $this->parseProxies(
            $this->fileLoader->load()
        );
    }
}

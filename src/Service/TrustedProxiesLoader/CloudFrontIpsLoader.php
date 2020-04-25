<?php

namespace Eugeniypetrov\Lib\Service\TrustedProxiesLoader;

class CloudFrontIpsLoader implements TrustedProxiesLoader
{
    /**
     * @var RemoteFileLoader
     */
    private $fileLoader;

    /**
     * CloudFrontIpsLoader constructor.
     * @param RemoteFileLoader $fileLoader
     */
    public function __construct(RemoteFileLoader $fileLoader)
    {
        $this->fileLoader = $fileLoader;
    }

    private function parseProxies(string $content): array
    {
        $proxies = [];
        $decoded = json_decode($content, true);
        foreach ($decoded["prefixes"] as $prefix) {
            if ($prefix["service"] === "CLOUDFRONT") {
                $proxies[] = $prefix["ip_prefix"];
            }
        }

        return $proxies;
    }

    public function load(): array
    {
        return $this->parseProxies(
            $this->fileLoader->load()
        );
    }
}

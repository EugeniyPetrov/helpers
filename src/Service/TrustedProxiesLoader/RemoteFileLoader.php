<?php

namespace Eugeniypetrov\Lib\Service\TrustedProxiesLoader;

class RemoteFileLoader
{
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $cacheDir;
    /**
     * @var int
     */
    private $ttl;

    /**
     * RemotePlaintextProxiesLoader constructor.
     * @param string $url
     * @param string $cacheDir
     * @param int $ttl
     */
    public function __construct(string $url, string $cacheDir, int $ttl)
    {
        $this->url = $url;
        $this->cacheDir = $cacheDir;
        $this->ttl = $ttl;
    }

    private function getCacheFilename(): string
    {
        $n = gmp_init(md5($this->url), 16);

        return sprintf(
            "%s/%s_%s",
            $this->cacheDir,
            gmp_strval($n, 36),
            preg_replace("~[^a-z0-9_\-\.]+~", "_", $this->url)
        );
    }

    private function loadFromCache(bool $force = false)
    {
        if (file_exists($this->getCacheFilename())) {
            $mtime = filemtime($this->getCacheFilename());
            if (time() - $mtime <= $this->ttl || $force === true) {
                return file_get_contents($this->getCacheFilename());
            }
        }

        return false;
    }

    private function saveToCache(string $content)
    {
        file_put_contents($this->getCacheFilename(), $content);
    }

    public function load(): string
    {
        $content = $this->loadFromCache();
        if ($content !== false) {
            return $content;
        }

        $content = file_get_contents($this->url);
        if ($content !== false) {
            $this->saveToCache($content);
        } else {
            $content = $this->loadFromCache(true);
        }

        if ($content === false) {
            throw new \RuntimeException(sprintf("Unable to get remote file from %s", $this->url));
        }

        return $content;
    }
}

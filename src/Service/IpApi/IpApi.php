<?php

namespace Eugeniypetrov\Lib\Service\IpApi;

use Psr\Log\LoggerInterface;

class IpApi
{
    const ENDPOINT = "http://ip-api.com/json/%s";
    const PRO_ENDPOINT = "http://pro.ip-api.com/json/%s?key=%s";

    /**
     * @var string
     */
    private $key;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * IpApi constructor.
     * @param string $key
     * @param LoggerInterface $logger
     */
    public function __construct(string $key, LoggerInterface $logger)
    {
        $this->key = $key;
        $this->logger = $logger;
    }

    private function getEndpoint()
    {
        if ($this->key === "") {
            return self::ENDPOINT;
        } else {
            return self::PRO_ENDPOINT;
        }
    }

    public function getLocation(string $ip)
    {
        $endpoint = sprintf($this->getEndpoint(), $ip, urlencode($this->key));

        $this->logger->debug("Init geo request", [
            "endpoint" => $endpoint,
        ]);

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_errno($ch) !== CURLE_OK) {
            throw new \RuntimeException(sprintf("Unable to obtain location. Error: %s", curl_error($ch)));
        }

        $geo = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(sprintf("Invalid response from geo service"));
        }

        if (key_exists("status", $geo) && $geo["status"] === "fail") {
            throw new \RuntimeException(
                sprintf("Unable to obtain location. Error: %s", $geo["message"])
            );
        }

        curl_close($ch);

        return (new Geo())
            ->setCountry((string)$geo["country"])
            ->setCountryCode((string)$geo["countryCode"])
            ->setRegion((string)$geo["region"])
            ->setRegionName((string)$geo["regionName"])
            ->setCity((string)$geo["city"])
            ->setZip((string)$geo["zip"])
            ->setLat((float)$geo["lat"])
            ->setLon((float)$geo["lon"])
            ->setTimezone(new \DateTimeZone($geo["timezone"]))
            ->setIsp((string)$geo["isp"])
            ->setOrg((string)$geo["org"])
            ->setAs((string)$geo["as"]);
    }
}

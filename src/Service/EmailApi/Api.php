<?php

namespace Eugeniypetrov\Lib\Service\EmailApi;

class Api
{
    /**
     * @var string
     */
    private $endpoint;

    /**
     * RegApi constructor.
     * @param string $endpoint
     */
    public function __construct(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @param Request $request
     * @param string $secret
     * @return Response
     */
    public function register(Request $request, string $secret): Response
    {
        $ch = curl_init($this->endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $request = http_build_query([
            "public_key" => $request->getPublicKey(),
            "email" => $request->getEmail(),
            "ip" => $request->getIp(),
            "accept_language" => $request->getAcceptLanguage(),
            "user_agent" => $request->getUserAgent(),
            "subscribed_at" => date("Y-m-d H:i:s"),
            "var1" => $request->getVar1(),
            "var2" => $request->getVar2(),
            "var3" => $request->getVar3(),
            "var4" => $request->getVar4(),
            "date_of_birth" => $request->getDateOfBirth() === null ? null : $request->getDateOfBirth()->format("Y-m-d"),
            "gender" => $request->getGender(),
            "nickname" => $request->getNickname(),
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

        $signature = hash_hmac('sha256', $request, $secret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/x-www-form-urlencoded",
            "X-Signature: $signature",
        ]);
        $response = curl_exec($ch);

        if (curl_errno($ch) !== CURLE_OK) {
            throw new \RuntimeException(sprintf("Request failed. %s", curl_error($ch)));
        }

        if ($response === "") {
            throw new \RuntimeException("Empty response.");
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(sprintf("Unable to decode response. %s", json_last_error_msg()));
        }

        return new Response(
            $decoded["status"] ?? "",
            $decoded["message"] ?? ""
        );
    }
}

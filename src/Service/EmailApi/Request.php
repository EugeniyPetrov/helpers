<?php

namespace Eugeniypetrov\Lib\Service\EmailApi;

class Request
{
    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $acceptLanguage;

    /**
     * @var string
     */
    private $userAgent;

    /**
     * @var string
     */
    private $var1 = "";

    /**
     * @var string
     */
    private $var2 = "";

    /**
     * @var string
     */
    private $var3 = "";

    /**
     * @var string
     */
    private $var4 = "";

    /**
     * @var null|\DateTimeInterface
     */
    private $dateOfBirth;

    /**
     * @var null|string
     */
    private $gender;

    /**
     * @var null|string
     */
    private $nickname;

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     * @return Request
     */
    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Request
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return Request
     */
    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return string
     */
    public function getAcceptLanguage(): string
    {
        return $this->acceptLanguage;
    }

    /**
     * @param string $acceptLanguage
     * @return Request
     */
    public function setAcceptLanguage(string $acceptLanguage): self
    {
        $this->acceptLanguage = $acceptLanguage;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     * @return Request
     */
    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @return string
     */
    public function getVar1(): string
    {
        return $this->var1;
    }

    /**
     * @param string $var1
     * @return Request
     */
    public function setVar1(string $var1): self
    {
        $this->var1 = $var1;

        return $this;
    }

    /**
     * @return string
     */
    public function getVar2(): string
    {
        return $this->var2;
    }

    /**
     * @param string $var2
     * @return Request
     */
    public function setVar2(string $var2): self
    {
        $this->var2 = $var2;

        return $this;
    }

    /**
     * @return string
     */
    public function getVar3(): string
    {
        return $this->var3;
    }

    /**
     * @param string $var3
     * @return Request
     */
    public function setVar3(string $var3): self
    {
        $this->var3 = $var3;

        return $this;
    }

    /**
     * @return string
     */
    public function getVar4(): string
    {
        return $this->var4;
    }

    /**
     * @param string $var4
     * @return Request
     */
    public function setVar4(string $var4): self
    {
        $this->var4 = $var4;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    /**
     * @param \DateTimeInterface|null $dateOfBirth
     * @return Request
     */
    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string|null $gender
     * @return Request
     */
    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @param string|null $nickname
     * @return Request
     */
    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }
}

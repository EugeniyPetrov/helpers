<?php

namespace Eugeniypetrov\Lib\Service\EmailApi;

class Response
{
    /**
     * @var string
     */
    private $status;
    /**
     * @var string
     */
    private $message;

    /**
     * EmailResponse constructor.
     * @param string $status
     * @param string $message
     */
    public function __construct(string $status, string $message)
    {
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}

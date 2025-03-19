<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

class Response implements iResponse
{
    public function __construct
    (
        private int $code_status,
        private string $response
    ){}

    public function getCodeStatus(): int
    {
        return $this->code_status;
    }

    public function getResponse(): string
    {
        return $this->response;
    }
}
<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

class RequestSchemaHeader implements iRequestSchemaHeader
{
    public function __construct
    ( 
        private string $api_name,
        private int $number_request
    ){}    

    public function getNameAPI(): string
    {
        return $this->api_name;
    }   

    public function getNumberRequest(): int
    {
        return $this->number_request;
    }
}
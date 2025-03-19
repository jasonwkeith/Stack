<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

use JasonWKeith\Application\Stack\Boundaries\iRequest;

class Request implements iRequest
{
    private string $method;
    private string $api_name;
    private ?array $api_data;
    private ?string $api_version;

    public function __construct( string $method, string $api_name, ?array $api_data = null, ?string $api_version = null )
    {
        $this->method = $method;
        $this->api_name = $api_name;
        $this->api_data = $api_data;
        $this->api_version = $api_version;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getAPIName(): string
    {
        return $this->api_name;
    }

    public function getAPIData(): ?array
    {
        return $this->api_data;
    }

    public function getAPIVersion(): ?string
    {
        return $this->api_version;
    }
}


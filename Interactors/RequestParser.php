<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Interactors;

use JasonWKeith\Application\Stack\Boundaries\iRequest;
use JasonWKeith\Application\Stack\Boundaries\iRequestParser;
use JasonWKeith\Application\Stack\Entities\Request;

class RequestParser implements iRequestParser
{
    public function execute( array $http_request, string $url ): iRequest
    {
        $method = $http_request['method'] ?? 'GET';
        [$api_version, $api_name] = $this->extractResourceAndVersionFromURL( $url );
        $api_data = $this->getRequestBody();

        return new Request( $method, $api_name, $api_data, $api_version );
    }

    private function extractResourceAndVersionFromURL( string $url ): array
    {
        $parsed_url = parse_url( $url );
        $path = $parsed_url['path'] ?? '';
        
        $parts = explode( '/', trim( $path, '/' ) );
        $api_version = null;
        
        // Check if the first part is an API version (e.g., v1, v2)
        if( !empty( $parts ) && preg_match( '/^v\d+$/i', $parts[0] ) )
        {
            $api_version = array_shift( $parts );
        }
        
        // Join remaining parts to preserve full resource path (e.g., auth/login)
        $api_name = implode( '/', $parts );
        
        return [$api_version, $api_name];
    }

    private function getRequestBody(): ?array
    {
        $body = file_get_contents( 'php://input' );
        return !empty( $body ) ? json_decode( $body, true ) : null;
    }
}

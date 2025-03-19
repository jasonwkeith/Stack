<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Infrastructure;

use JasonWKeith\Application\Stack\Entities\iRequest;
use JasonWKeith\Application\Stack\Entities\Request;

class RequestFactory
{
    private $helix;
    
    public function __construct( $helix )
    {
        $this->helix = $helix;
    }
    
    public function create( string $body, array $headers, string $method, string $url ): iRequest 
    {
        // Parse the URL into components
        $url_parsed = parse_url( $url );
        
        // Initialize the data array with default values
        $data = $this->initializeRequestData( $url, $method );
        
        // Extract basic URL components
        $data = $this->extractBasicUrlComponents( $data, $url_parsed );
        
        // Extract and process the path
        if( isset( $url_parsed[ 'path' ] ) ) 
        {
            $data = $this->processUrlPath( $data, $url_parsed[ 'path' ] );
        }
        
        // Parse query parameters
        if( isset( $url_parsed[ 'query' ] ) )
        {
            $data = $this->processQueryParameters( $data, $url_parsed[ 'query' ] );
        }
        else
        {
            $data = $this->setDefaultPaginationAndSorting( $data );
        }

        // Build the Request object
        return $this->buildRequestObject( $data );
    }
    
    private function buildRequestObject( array $data ): Request
    {
        $parameters_pagination = NULL;
        if( isset( $data[ 'pagination' ] ) ) 
        {
            $parameters_pagination = new ParameterPagination(
                $data[ 'pagination' ][ 'limit' ],
                $data[ 'pagination' ][ 'offset' ],
                $data[ 'pagination' ][ 'page' ]
            );
        }
        
        return new Request
        (
            $body,
            $data[ 'filters' ], 
            $data[ 'format' ],
            $headers,
            $data[ 'host' ],
            $data[ 'method' ],
            $parameters_pagination,
            $data[ 'path_base' ],
            $data[ 'path_segments' ],
            $data[ 'port' ],
            $data[ 'resource' ],
            $data[ 'resource_sub' ],
            $data[ 'scheme' ],
            $data[ 'sorting' ],            
            $data[ 'url_original' ],
            $data[ 'uuids_resource' ],
            $data[ 'uuids_subresource' ],            
            $data[ 'version_api' ]
        );
    }
    
    private function extractBasicUrlComponents( array $data, array $url_parsed ): array
    {
        if( isset( $url_parsed[ 'scheme' ] ) ) 
        {
            $data[ 'scheme' ] = $url_parsed[ 'scheme' ];
        }
        
        if( isset( $url_parsed[ 'host' ] ) ) 
        {
            $data[ 'host' ] = $url_parsed[ 'host' ];
        }
        
        if( isset( $url_parsed[ 'port' ] ) ) 
        {
            $data[ 'port' ] = $url_parsed[ 'port' ];
        }
        
        return $data;
    }
    
    private function initializeRequestData( string $url, string $method ): array
    {
        return [
            'url_original' => $url,
            'scheme' => '',
            'host' => '',
            'method'=> strtoupper( $method ),
            'port' => NULL,
            'path_base' => '',
            'path_segments' => [],
            'parameters_query' => [],
            'version_api' => NULL,
            'resource' => NULL,
            'uuids_resource' => [],
            'resource_sub' => NULL,
            'uuids_subresource' => [],
            'format' => NULL
        ];
    }
    
    private function isFormatSegment( string $segment ): bool 
    {
        return strpos( $segment, '.' ) !== false;
    }
    
    private function isApiVersionSegment( string $segment ): bool
    {
        return preg_match( '/^v\d+$/', $segment ) === 1;
    }
    
    private function processUrlPath( array $data, string $path ): array
    {
        $data[ 'path_base' ] = $path;
        
        // Remove leading and trailing slashes and split into segments
        $path_clean = trim( $path, '/' );
        if( empty( $path_clean ) ) 
        {
            return $data;
        }
        
        $data[ 'path_segments' ] = explode( '/', $path_clean );
        $segment_count = count( $data[ 'path_segments' ] );
        
        // Process API version and resources
        if( $segment_count > 0 && $this->isApiVersionSegment( $data[ 'path_segments' ][ 0 ] ) ) 
        {
            $data = $this->processApiVersionAndResources( $data );
        }
        
        // Process format extension (e.g., .json, .xml)
        if( $segment_count > 0 ) 
        {
            $data = $this->processFormatExtension( $data, $segment_count );
        }
        
        return $data;
    }
    
    private function setDefaultPaginationAndSorting( array $data ): array
    {
        $data[ 'pagination' ] = [
            'page' => NULL,
            'limit' => NULL,
            'offset' => NULL,
        ];
        $data[ 'sorting' ] = NULL;
        $data[ 'filters' ] = NULL;
        
        return $data;
    }
}

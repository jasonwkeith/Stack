<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Boundaries;

use JasonWKeith\Application\Stack\Boundaries\iRequest;

interface iRequestParser
{
    public function execute( array $http_request, string $url ): iRequest;
}

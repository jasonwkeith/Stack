<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Infrastructure;

use JasonWKeith\Application\Stack\Boundaries\iRequest;

interface iRequestHandler
{
   public function handleRequest( iRequest $request ): string;
}
    
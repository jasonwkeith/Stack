<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Interactors;

use JasonWKeith\Domain\Stack\Boundaries\iStack;
use JasonWKeith\Application\Stack\Infrastructure\RequestFactory;

class Stack implements iStack
{
    public function __construct()
    {
        $this->request_handler = new RequestHandler();
    }

    function execute( iRequest $request ): string 
    {
        $response = $this->request_handler->handleRequest( $request );
        return $response;
    }  
}
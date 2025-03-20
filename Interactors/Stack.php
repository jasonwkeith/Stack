<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Interactors;

use JasonWKeith\Application\Stack\Boundaries\iRequest;
use JasonWKeith\Application\Stack\Infrastructure\iAPIDataModelFactory;
use JasonWKeith\Application\Stack\Infrastructure\RequestHandler;
use JasonWKeith\Core\Mercury\Boundaries\iMuse;
use JasonWkeith\Domain\Parametrix\Boundaries\iParametrix;
use JasonWKeith\Domain\Stack\Boundaries\iStack;
use JasonWKeith\Domain\Stratum\Boundaries\iStratum;

class Stack implements iStack
{
    public function __construct
    ( 
        private iAPIDataModelFactory $api_data_model_factory, 
        private iMuse $muse, 
        private iParametrix $parametrix, 
        private iStratum $stratum 
    )
    {
        $this->request_handler = new RequestHandler
        (
            $api_data_model_factory, 
            $muse, 
            $parametrix, 
            $stratum 
        );
    }

    function execute( iRequest $request ): string 
    {
        $response = $this->request_handler->handleRequest( $request );
        return $response;
    }  
}

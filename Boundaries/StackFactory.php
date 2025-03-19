<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Boundaries;

use JasonWKeith\Application\Stack\Boundaries\iRequestParser;
use JasonWKeith\Application\Stack\Boundaries\iStack;
use JasonWKeith\Application\Stack\Infrastructure\RequestHandler;
use JasonWKeith\Application\Stack\Interactors\RequestParser;
use JasonWKeith\Application\Stack\Interactors\Stack;
use JasonWKeith\Core\Helix\Boundaries\iUUID;
use JasonWKeith\Data\PermaDust\Boundaries\PermaDustFactory;
use JasonWKeith\Domain\Parametrix\Boundaries\ParametrixFactory;
use JasonWKeith\Domain\Stratum\Boundaries\StratumFactory;

class StackFactory
{
    static public function create( array $data_configuration, iUUID $user ): iStack
    {
        $parametrix = ParametrixFactory::create();

        $permadust = PermaDustFactory::create( $data_configuration, $user );
        $stratum = StratumFactory::create( $permadust );
        $request_handler = new RequestHandler( $parametrix, $stratum );
        return new Stack( $request_handler );
    }

    static public function createRequestParser( ): iRequestParser
    {
        return new RequestParser;
    }
}
<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Boundaries;

use JasonWKeith\Application\Stack\Boundaries\iRequestParser;
use JasonWKeith\Application\Stack\Boundaries\iStack;
use JasonWKeith\Application\Stack\Infrastructure\APIDataModelFactory;
use JasonWKeith\Application\Stack\Infrastructure\RequestHandler;
use JasonWKeith\Application\Stack\Interactors\RequestParser;
use JasonWKeith\Application\Stack\Interactors\Stack;
use JasonWKeith\Data\Helix\Boundaries\HelixFactory;
use JasonWKeith\Core\Helix\Boundaries\iUUID;
use JasonWKeith\Core\Mercury\Boundaries\MercuryFactory;
use JasonWKeith\Data\PermaDust\Boundaries\PermaDustFactory;
use JasonWKeith\Domain\Parametrix\Boundaries\ParametrixFactory;
use JasonWKeith\Domain\Stratum\Boundaries\StratumFactory;

class StackFactory
{
    static public function create( array $data_configuration, iUUID $tenant, iUUID $user ): iStack
    {
        $helix = HelixFactory::create();

        $nymph = NymphFactory::create();
        $permadust = PermaDustFactory::create( $data_configuration, $user );
        $stratum = StratumFactory::create( $permadust );
        $keep = KeepFactory::create
        (
            $user, 
            $tenant,
            $stratum 
        );

        $api_data_model_factory = new APIDataModelFactory
        (
            $helix,
            $keep,
            $nymph,
            $stratum
        );
        $muse = MercuryFactory::createMuse();
        $parametrix = ParametrixFactory::create();

        return new Stack
        ( 
            $api_data_model_factory, 
            $muse, 
            $parametrix, 
            $stratum 
        );
    }

    static public function createRequestParser( ): iRequestParser
    {
        return new RequestParser;
    }
}
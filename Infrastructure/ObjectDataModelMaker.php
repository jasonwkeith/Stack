<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Interactors;

use JasonWKeith\Domain\Stack\Boundaries\iStack;
use JasonWKeith\Application\Stack\Infrastructure\RequestFactory;

class ObjectDataModelMaker 
{
    public function __construct
    ( iHelix $helix, iIsenguard $isenguard, iStratum $stratum, iVulcan $vulcan )
    {

    }

    public function makeObjectDataModelAPI( string $api_data ): ?iObjectDataModel
    {
        $data = json_decode( $api_data, true );

        $keys_required = array();
        array_push( $keys_required, $this->codex->getAPINameUUID() );
        array_push( $keys_required, $this->codex->getAPINameSchema() );
        array_push( $keys_required, $this->codex->getAPINameOwner() );
        array_push( $keys_required, $this->codex->getAPINameOrganization() );     
        array_push( $keys_required, $this->codex->getAPINameTenant() );     
        array_push( $keys_required, $this->codex->getAPINameIsDeleted() );

        foreach( $keys_required as $key )
        {
            if( ! isset( $data[ $key ] ) )
            {
                $message = "$key was missing from metadata!" .print_r( $data, true );
                $this->throwException( $message );
            }
        }

        $schema_value = $data[ $this->codex->getAPINameSchema() ];
        $schema_uuid = $this->helix->createUUID( $schema_value );
        $schema_header = $this->stratum->load( $schema_uuid );
        $schema_header = $this->vulcan->makeSchemaHeader( $schema_header->getSerializedData() );
        
        $pairs = array();
        $keys = array_keys( $data );
        foreach( $keys as $key )
        {
            $schema_field = $schema_header->getSchemaFieldAPIName( $key );
            $pairs[] = $this->createPair( $schema_field->getUUID(), $data[ $key ] );
        }

        $uuid = $this->helix->createUUID( $data[ $this->codex->getUUID() ] );
        $schema = $this->helix->createUUID( $data[ $this->codex->getSchema() ]);
        $owner = $this->helix->createUUID( $data[ $this->codex->getOwner() ]);
        $organization = $this->helix->createUUID( $data[ $this->codex->getOrganization() ]);
        $tenant = $this->helix->createUUID( $data[ $this->codex->getTenant() ]);
        $is_deleted = $data[ $this->codex->getIsDeleted() ];
        
        return $this->isenguard->createObjectDataModel
        (
            $uuid,
            $schema,
            $owner,
            $organization,
            $tenant,
            $is_deleted,
            ...$pairs
        );
    }  
}     
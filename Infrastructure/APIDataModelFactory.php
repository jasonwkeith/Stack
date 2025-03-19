<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Infrastructure;

use JasonWKeith\Application\Stack\Entities\APIDataModel;
use JasonWKeith\Application\Stack\Entities\iAPIDataModel;
use JasonWKeith\Application\Stack\Entities\iRequest;
use JasonWKeith\Application\Stack\Entities\Request;
use JasonWKeith\Core\Isenguard\Boundaries\iObjectDataModel;
use JasonWKeith\Core\Isenguard\Boundaries\iUniquelyIdentifiable;
use JasonWKeith\Core\Isenguard\Boundaries\iUUID;
use JasonWKeith\Core\Mercury\Boundaries\iJSON;
use JasonWKeith\Core\Mercury\Boundaries\iNymph;
use JasonWKeith\Domain\Keep\Boundaries\iKeep;
use JasonWKeith\Domain\Stratum\Boundaries\iStratum;
use JasonWKeith\Application\Stack\Infrastructure\iResourceFactory;

class APIDataModelFactory implements iAPIDataModelFactory
{
    private array $entities_expanded = [];
    private array $entities_identity = [];    
    private array $schema_headers = [];

    public function __construct
    ( 
        private iHelix $helix,
        private iKeep $keep,
        private Nymph $nymph,
        private iStratum $stratum
    )
    {
    }
    
    public function create( iObjectDataModel ...$entities ): array
    {
        $this->schema_headers = $this->requireSchemaHeaders( ...$entities );

        return $this->createAPIDataModels( $entities );
    }

    public function createExpanded( iObjectDataModel ...$entities ): array
    {
        $this->schema_headers = $this->requireSchemaHeaders( ...$entities );
        $this->entities_expanded = $this->getEntitiesRelated( ...$entities );
        $schema_headers = $this->requireSchemaHeaders( ...$this->entities_expanded );
        $this->schema_headers = $this->merge( $this->schema_headers, $schema_headers );

        $this->entities_identity = $this->getEntitiesRelated( ...$this->entities_expanded );
        $schema_headers = $this->requireSchemaHeaders( ...$this->entities_identity );
        $this->schema_headers = $this->merge( $this->schema_headers, $schema_headers );

       return $this->createAPIDataModelExpanded( $entities );        
    }   

    private function createAPIDataModel( iObjectDataModel $entity ): iJSON
    {
        $schema_header = $this->requireSchemaHeader( $entity->getSchema() );

        $fields_schema = $entity->getFieldsSchema();
        $api_data_model = new APIDataModel();
        $key_values = [];
        foreach( $fields_schema as $field_schema )
        {
            $api_key = $field_schema->getAPIName();
            $pair = $entity->getPair( $field_schema->getUUID() );
            if( $pair === NULL )
            {
                continue;
            }
            $value = $pair->getValue();
            $key_values[] = $this->nymph->createKeyValue( $api_key, $value );
        }

        return $this->muse->createJSON( ...$key_values );
    }

    private function createAPIDataModelExpanded( iObjectDataModel $entity ): iJSON
    {
        $schema_header = $this->requireSchemaHeader( $entity->getSchema() );

        $fields_schema = $entity->getFieldsSchema();
        $api_data_model = new APIDataModel();
        $key_values = [];
        foreach( $fields_schema as $field_schema )
        {
            $api_key = $field_schema->getAPIName();
            $pair = $entity->getPair( $field_schema->getUUID() );
            if( $pair === NULL )
            {
                continue;
            }
            $value = $pair->getValue();

            if( $field_schema->isRelatedEntity() === true )
            {
                $uuid_value = $pair->getUUID()->getValue();
                if( isset( $this->entities_expanded[ $uuid_value ] ) === true )
                {
                    $value = $this->createAPIDataModelIdentified( $this->entities_expanded[ $uuid_value ] );
                    $key_values[] = $this->nymph->createKeyJSON( $api_key, $value );
                }
                else
                {
                    $key_values[] = $this->nymph->createKeyValue( $api_key, $value );
                }
            }
            else
            {
                $key_values[] = $this->nymph->createKeyValue( $api_key, $value );
            }
        }

        return $this->muse->createJSON( ...$key_values );
    }    

    private function createAPIDataModelIdentified( iObjectDataModel $entity ): iJSON
    {
        $schema_header = $this->getSchemaHeader( $entity->getSchema() );

        $fields_schema = $entity->getFieldsSchema();
        $api_data_model = new APIDataModel();
        $key_values = [];
        foreach( $fields_schema as $field_schema )
        {
            $value_schema_field = $field_schema->getUUID()->getValue();
            if( $value_schema_field === $this->codex->getIdentifier() )
            {
                $pair = $entity->getPair( $field_schema->getUUID() );
                if( $pair === NULL )
                {
                    continue;
                }
                $value = $pair->getValue();
                if( isset( $entities_identity[ $value ] ) === true )
                {
                    $value = $this->createAPIDataModel( $entities_identity[ $value ] );
                    $key_values[] = $this->nymph->createKeyJSON( $api_key, $value );
                }
                else
                {
                    $key_values[] = $this->nymph->createKeyValue( $api_key, $value );
                }
            }
        }
    
        return $this->muse->createJSON( ...$key_values );
    }      

    private function createAPIDataModels( iObjectDataModel ...$entities_resource ): array
    {
        $api_data_models = [];
        foreach( $entities_resource as $entity )
        {
            $api_data_models[] = $this->createAPIDataModel( $entity );
        }
        return $api_data_models;
    }

    private function createAPIDataModelsExpanded( iObjectDataModel ...$entities ): array
    {
        $this->entities_identity = $this->getEntitiesIdentified( ...$entities );

        $api_data_models = [];
        foreach( $entities_resource as $entity )
        {
            $api_data_models[] = $this->createAPIDataModelExpanded( $entity );
        }
        return $api_data_models;
    }  

    private function getEntitiesRelated( iObjectDataModel ...$entities ): array
    {
        $uuids_expanded = [];
        foreach( $entities as $entity )
        {
            $uuids = $this->getUUIDsRelated( $entity );
            foreach( $uuids as $uuid )
            {
                $value = $uuid->getValue();
                $uuids_expanded[ $value ] = $uuid;
            }
        }

        $entities_expanded = $this->keep->loadDataBatch( ...$uuids_expanded );
        return $this->normalize( ...$entities_expanded );
    }

    private function getUUIDsRelated( iObjectDataModel $entity ): array
    {
        $uuids = [];
        $schema_header = $this->requireSchemaHeader( $entity->getSchema() );

        $fields_schema = $schema_header->getFieldsSchema();
        foreach( $fields_schema as $field_schema )
        {
            if( $field_schema->isRelatedEntity() === true )
            {
                $pair = $entity->getPair( $field_schema->getUUID() );
                if( $pair === NULL )
                {
                    continue;
                }
                $value = $pair->getValue();
                $uuids[] = $this->helix->createUUID( $value );
            }
        }
        return $uuids;
    }

    private function merge( array $schema_headers, array $schema_headers_new ): array
    {
        $schema_headers_merged = $this->normalize( $schema_headers );
        foreach( $schema_headers_new as $schema_header_new )
        {
            $value_schema_header_new = $schema_header_new->getUUID()->getValue();
            $schema_headers_merged[ $value_schema_header_new ] = $schema_header_new;
        }
        return $schema_headers_merged;
    }

    private function normalize( iUniquelyIdentifiable ...$identifiables ): array
    {
        $normalized = array();
        foreach( $identifiables as $identifiable )
        {
            $uuid_value = $identifiable->getUUID()->getValue();
            $normalized[ $uuid_value ] = $identifiable;
        }
        return $normalized;
    }

    private function requireSchemaHeader( iUUID $schema ): ?iObjectDataModel
    {
        $schema_header = $this->stratum->load( $schema );
        if( $schema_header === NULL )
        {
            return NULL;
        }

        return $schema_header;
    }

    private function requireSchemaHeaders( iObjectDataModel ...$entities ): array
    {
        $uuids_schema = [];
        foreach( $entities as $entity )
        {
            $value_schema = $entity->getSchema()->getValue();
            $uuids_schema[ $value_schema ] = $entity->getSchema();
        }

        return $this->stratum->loadBatch( ...$uuids_schema );

        foreach( $entities as $entity )
        {
            $value_schema = $entity->getSchema()->getValue();
            if( isset( $schema_headers[ $value_schema ] ) === false )
            {
                return NULL;
            }
        }

        $this->schema_headers = $schema_headers;
    }    
}


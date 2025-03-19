<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Infrastructure;

use JasonWKeith\Application\Stack\Boundaries\iRequest;
use JasonWKeith\Application\Stack\Infrastructure\iAPIDataModelFactory;
use JasonWKeith\Core\Isenguard\Boundaries\iUUID;
use JasonWKeith\Core\Mercury\Boundaries\iMuse;
use JasonWkeith\Domain\Parametrix\Boundaries\iParametrix;
use JasonWKeith\Domain\Stratum\Boundaries\iStratum;
use JasonWKeith\Application\Stack\Infrastructure\iRequestHandler;

class RequestHandler implements iRequestHandler
{
    public function __construct
    ( 
        private iAPIDataModelFactory $api_data_model_factory, 
        private iMuse $muse, 
        private iParametrix $parametrix, 
        private iStratum $stratum 
    )
    {}

    public function handleRequest( iRequest $request ): string 
    {
        $method = $request->getMethod();
        if( $request->getAPIName() === NULL  || $request->getAPIName() === "" )
        {
            $response = $this->muse
                ->withStatusErrorDetail( "Request must include a valid endpoint" )
                ->buildBadRequest();

            return $response->execute();
        }

        $schema = $this->getSchemaHeader( $resource->getAPIName() );

        if( $schema === NULL )
        {
            $response = $this->muse
            ->withStatusErrorDetail( "Could not find matching SchemaHeader for requested resource: " . $request->getAPIName() )
            ->buildNotFound();

            return $response->execute();
        }

        $api_data = $request->getAPIData();

        $response = "";
        switch( $method )
        {
            case "DELETE":
                $response = $this->processDelete( $schema, ...$uuids );
                break;
            case "GET":
                $response = $this->processGet( $schema, ...$uuids );
                break;
            case "POST":
                $response = $this->processPost( $schema, $api_data );
                break;
            case "PUT":
                $response = $this->processPut( $schema, $api_data );
                break;
            default:
                $response = $this->muse
                ->withStatusErrorDetail( "Method not allowed: " . $request->getAPIName() )
                ->buildMethodNotAllowed();
    
                return $response->execute();
        }

        return $response;
    }

    private function getSchema( string $api_name ): ?iUUID
    {        
        $search_parameter = $this->parametrix->getSchemaHeaderByAPIName( $api_name );
        $object_data_models = $this->loadBySearchParameter( $search_parameter );

        if( empty( $object_data_models ) )
        {
            return NULL;
        }
        
        $object_data_model = reset( $object_data_models );

        return $object_data_model->getUUID();
    }       

    public function processDelete( iUUID $schema, iUUID $uuids ): string 
    {
        $object_data_models = $this->stratum->loadBatch( ...$uuids );
        $object_data_models = $this->helix->normalize( ...$object_data_models );

        $responses = array();
        foreach( $uuids as $uuid )
        {
            if( !array_key_exists( $uuid->getValue(), $object_data_models ) )
            {
                $response = $this->muse
                    ->withStatusErrorDetail( "Entity: $value_uuid was not found" )
                    ->buildNotFound();

                return $response->execute();
            }
        }

        foreach( $object_data_models as $object_data_model )
        {
            $value_uuid = $object_data_model->getUUID()->getValue();
            if( $object_data_model->getSchema()->getValue() !== $schema->getValue() )
            {
                $response = $this->muse
                    ->withStatusErrorDetail( "Entity: $value_uuid's schema does not match url resource" )
                    ->buildBadRequest();

                return $response->execute();
            }
        }

        $this->stratum->deleteBatch( ...$uuids );

        $response = $this->muse
            ->buildNoContent();   

        return $response->execute();
    }

    public function processGet( iUUID $schema, iUUID $uuids ): string 
    {
        $object_data_models = $this->stratum->loadBatch( ...$uuids );
        $object_data_models = $this->helix->normalize( ...$object_data_models );

        $responses = array();
        foreach( $uuids as $uuid )
        {
            if( !array_key_exists( $uuid->getValue(), $object_data_models ) )
            {
                $response = $this->muse
                    ->withStatusErrorDetail( "Entity was not found" )
                    ->buildMethodNotAllowed();
    
                return $response->execute();
            }
        }

        $api_data_models = $this->api_data_model_factory->create( ...$object_data_models );

        return $this->muse
            ->withData( ...$api_data_models )
            ->buildSuccess();
    }

    public function processPost( iUUID $schema, string $api_data ): string 
    {
        $object_data_model = $this->helix->makeObjectDataModelAPIData( $api_data );
        if( $object_data_model === NULL )
        {
            $response = $this->muse
                ->withStatusErrorDetail( "Request is missing required fields" )
                ->buildBadRequest();

            return $response->execute();              
        }

        $uuid = $object_data_model->getUUID();
        $value_uuid = $object_data_model->getUUID()->getValue();
        if( $object_data_model->getSchema()->getValue() !== $schema->getValue() )
        {
            $response = $this->muse
                ->withStatusErrorDetail( "Entity's schema does not match url resource" )
                ->buildBadRequest();

            return $response->execute();    
        }

        $save_response = $this->keep->save( $object_data_model );
        if( $save_response->hasError() )
        {
            $response = $this->muse
                ->withStatusErrorDetail( "Unknown issue during save" )
                ->buildBadRequest();

            return $response->execute();   
        }

        $response = $this->muse
            ->buildNoContent();   

        return $response->execute();        
    }

    public function processPut( iUUID $schema, string $api_data ): string 
    {
        $object_data_model_new = $this->helix->makeObjectDataModelAPIData( $api_data );
        if( $object_data_model_new === NULL )
        {
            $response = $this->muse
                ->withStatusErrorDetail( "Request is missing required fields" )
                ->buildBadRequest();

            return $response->execute();   
        }

        $uuid = $object_data_model_new->getUUID();
        $object_data_model_old = $this->stratum->load( $uuid );
        if( $object_data_model_old === NULL )
        {
            $response = $this->muse
                ->withStatusErrorDetail( "Cannot find enity to update with this uuid" )
                ->buildBadRequest();

            return $response->execute(); 
        }

        if( $object_data_model_new->getSchema()->getValue() !== $schema->getValue() )
        {
            $response = $this->muse
                ->withStatusErrorDetail( "Entity's schema does not match url resource" )
                ->buildBadRequest();

            return $response->execute(); 
        }

        $save_response = $this->keep->save( $object_data_model_new );
        if( $save_response->hasError() )
        {
            $response = $this->muse
                ->withStatusErrorDetail( "Unknown issue during save" )
                ->buildBadRequest();

            return $response->execute();   
        }

        $response = $this->muse
            ->buildNoContent();   

        return $response->execute();
    }
}

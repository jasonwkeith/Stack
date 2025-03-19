<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

use JasonWKeith\Application\Stack\Entities\iAPIDataModel;
use JasonWKeith\Core\Mercury\Boundaries\iKeyJSON;

class APIDataModel implements iAPIDataModel
{
    public function __construct
    (
    )
    {
        $this->key_values = [];
    }

    public function getKeyValues(): array
    {
        return $this->key_values;
    }

    public function setKeyValue( iKeyJSON $key_value ): void
    {
        $this->key_values[] = $key_value;
    }
}
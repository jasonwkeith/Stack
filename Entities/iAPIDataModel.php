<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

use JasonWKeith\Core\Mercury\Boundaries\iKeyJSON;

interface iAPIDataModel
{
    public function getKeyValues(): array;
    public function setKeyValue( iKeyJSON $key_value ): void;
}
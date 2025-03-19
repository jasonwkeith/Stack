<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Infrastructure;

use JasonWKeith\Core\Isenguard\Boundaries\iObjectDataModel;

interface iAPIDataModelFactory
{
    public function create( iObjectDataModel ...$entities ): array;
    public function createExpanded( iObjectDataModel ...$entities ): array;
}


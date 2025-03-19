<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Boundaries;

use JasonWKeith\Application\Stack\Boundaries\iRequest;

interface iStack
{
    function execute( iRequest $request ): string;
}
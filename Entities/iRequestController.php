<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

use JasonWKeith\Application\Stack\Entities\iRequestController;
use JasonWKeith\Core\Helix\Boundaries\iUUID;
use JasonWKeith\Core\Isenguard\Boundaries\iSchemaHeader;

interface iRequestContoller
{

    public function getBody(): string;
    public function getMethod(): string;
    public function getSchemaHeader(): iSchemaHeader;
    public function getUUIDsResource(): array;
}
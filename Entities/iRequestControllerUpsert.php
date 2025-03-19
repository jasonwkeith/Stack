<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

use JasonWKeith\Core\Isenguard\Boundaries\iSchemaHeader;

interface iRequestControllerUpsert
{
    public function getBody(): string;
    public function getMethod(): string;
    public function getSchemaHeader(): iSchemaHeader;
}
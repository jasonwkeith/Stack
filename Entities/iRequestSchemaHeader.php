<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

interface iRequestSchemaHeader
{
    public function getNameAPI(): string;
    public function getNumberRequest(): int;
}
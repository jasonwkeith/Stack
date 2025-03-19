<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

interface iResponse
{
    public function getCodeStatus(): int;
    public function getResponse(): string;
}
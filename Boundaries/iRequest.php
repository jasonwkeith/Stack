<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Boundaries;

interface iRequest
{
    public function getMethod(): string;
    public function getAPIName(): string;
    public function getAPIData(): ?array;
    public function getAPIVersion(): ?string;
}


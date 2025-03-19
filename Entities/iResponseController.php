<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

interface iResponseController
{
    public function getMethod(): string;
    public function getResourceAPI(): string;
    public function getSerializedData(): string;
}
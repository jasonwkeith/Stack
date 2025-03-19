<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

interface iRequestController
{
    public function getMethod(): string;
    public function getBody(): string;
}
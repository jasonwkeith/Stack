<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

interface iParameterPagination
{
    public function getLimit():?int;
    public function getOffset():?int;
    public function getPage():?int;
}
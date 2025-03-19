<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

use JasonWKeith\Domain\Stack\Entities\iParameterPagination;

class ParameterPagination implements iParameterPagination
{
    public function __construct
    ( 
        private ?int $limit,
        private ?int $offset,
        private ?int $page
    ){}    

    public function getLimit():?int
    {
        return $this->limit;
    }

    public function getOffset():?int
    {
        return $this->offset;
    }

    public function getPage():?int
    {
        return $this->page;
    }
}
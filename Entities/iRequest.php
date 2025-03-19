<?php
declare( strict_types = 1 );
namespace JasonWKeith\Application\Stack\Entities;

use JasonWKeith\Application\Stack\Entities\iParameterPagination;

interface iRequest
{
    public function getBody(): string;
    public function getHeaders(): array;
    public function getFilters(): array;
    public function getFormat(): ?string;
    public function getHost(): string;
    public function getMethod(): string;
    public function getParametersQuery(): iParameterPagination;
    public function getPathBase(): string;
    public function getPathSegments(): array;
    public function getPort(): ?int;
    public function getResource(): string;
    public function getResourceSub(): ?string;
    public function getScheme(): string;
    public function getSorting(): array;
    public function getURLOriginal(): string;
    public function getUUIDsResource(): array;
    public function getUUIDsResourceSub(): array;
    public function getVersionAPI(): ?int;
}
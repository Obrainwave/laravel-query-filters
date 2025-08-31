<?php

namespace Obrainwave\LaravelQueryFilters\Concerns;

trait HandlesPagination
{
    protected function applyPaginationParams(?int $perPage = null, ?int $page = null): array
    {
        // URL query param overrides everything
        $requestPerPage = $this->filters['per_page'] ?? null;
        $perPage = $requestPerPage ?? $perPage ?? config('query-filters.pagination.per_page', 15);

        $maxPerPage = (int) config('query-filters.pagination.max_per_page', 100);
        $perPage = min((int) $perPage, $maxPerPage);

        // Page parameter precedence: URL → method → default 1
        $requestPage = $this->filters['page'] ?? null;
        $page = $requestPage ?? $page ?? 1;

        return [(int) $perPage, (int) $page];
    }

    public function paginate(
        ?int $perPage = null,
        array $columns = ['*'],
        string $pageName = 'page',
        ?int $page = null
    ) {
        [$perPage, $page] = $this->applyPaginationParams($perPage, $page);

        return $this->builder->paginate($perPage, $columns, $pageName, $page);
    }

    public function simplePaginate(
        ?int $perPage = null,
        array $columns = ['*'],
        string $pageName = 'page',
        ?int $page = null
    ) {
        [$perPage, $page] = $this->applyPaginationParams($perPage, $page);

        return $this->builder->simplePaginate($perPage, $columns, $pageName, $page);
    }
}

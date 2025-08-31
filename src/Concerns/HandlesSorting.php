<?php

namespace Obrainwave\LaravelQueryFilters\Concerns;

trait HandlesSorting
{
    protected function applySorting(): void
    {
        $sortParam = $this->filters['sort'] ?? config('query-filters.sorting.default');

        if (! $sortParam) {
            return;
        }

        $sorts = explode(',', $sortParam);
        $allowed = config('query-filters.sorting.allowed_columns', []);

        foreach ($sorts as $sort) {
            $direction = \Illuminate\Support\Str::startsWith($sort, '-') ? 'desc' : 'asc';
            $column = ltrim($sort, '-');

            // If allowed columns are defined, enforce them
            if (! empty($allowed) && ! in_array($column, $allowed, true)) {
                continue;
            }

            $this->builder->orderBy($column, $direction);
        }
    }
}

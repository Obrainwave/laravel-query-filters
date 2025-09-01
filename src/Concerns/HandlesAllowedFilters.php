<?php

namespace Obrainwave\LaravelQueryFilters\Concerns;

use Illuminate\Support\Facades\Schema;

trait HandlesAllowedFilters
{
    protected function getAllowedFilters(): array
    {
        $model = $this->builder?->getModel();

        if ($model && property_exists($model, 'allowedFilters') && is_array($model->allowedFilters)) {
            return $model->allowedFilters;
        }

        $allowed = config('query-filters.allowed_filters', ['*']);

        // If wildcard used, resolve to actual columns
        if (in_array('*', $allowed, true) && $model) {
            try {
                return Schema::getColumnListing($model->getTable());
            } catch (\Throwable $e) {
                // Fallback to empty array if schema lookup fails
                return [];
            }
        }

        return $allowed;
    }

    protected function isAllowedFilter(string $key, array $allowed): bool
    {
        return in_array(strtolower($key), array_map('strtolower', $allowed), true);
    }
}

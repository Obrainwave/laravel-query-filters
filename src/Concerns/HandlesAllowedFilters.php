<?php
namespace Obrainwave\LaravelQueryFilters\Concerns;

trait HandlesAllowedFilters
{
    protected function getAllowedFilters(): array
    {
        $model = $this->builder?->getModel();

        if ($model && property_exists($model, 'allowedFilters') && is_array($model->allowedFilters)) {
            return $model->allowedFilters;
        }

        return config('query-filters.allowed_filters', []);
    }

    protected function isAllowedFilter(string $key, array $allowed): bool
    {
        return in_array(strtolower($key), array_map('strtolower', $allowed), true);
    }
}

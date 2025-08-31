<?php

namespace Obrainwave\LaravelQueryFilters\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Obrainwave\LaravelQueryFilters\Traits\Helpers;

trait HandlesRelationships
{
    use Helpers;

    /**
     * Apply nested relationship filters using dot notation.
     * Supports:
     *   - "or:" prefix for OR logic
     *   - Arrays of values (treated as multiple OR where clauses)
     *   - Operators (gt, lt, gte, lte, between, etc.)
     */
    protected function applyRelationFilters(array $filters): void
    {
        foreach ($filters as $key => $value) {
            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            $isOr = Str::startsWith($key, 'or:');
            $cleanKey = $isOr ? Str::after($key, 'or:') : $key;

            $segments = explode('.', $cleanKey);
            $column = array_pop($segments);
            $relations = implode('.', $segments);

            if (empty($relations)) {
                continue;
            }

            $method = $isOr ? 'orWhereHas' : 'whereHas';

            $this->builder->{$method}($relations, function (Builder $query) use ($column, $value) {
                if (is_array($value) && ! $this->isOperatorArray($value)) {
                    // Multiple values = OR conditions
                    $query->where(function (Builder $q) use ($column, $value) {
                        foreach ($value as $val) {
                            $this->applyCaseInsensitiveToBuilder($column, $val, 'or', $q);
                        }
                    });
                } else {
                    // Single value OR operator-based array
                    $this->applyCaseInsensitiveToBuilder($column, $value, 'and', $query);
                }
            });
        }
    }

    /**
     * Check if an array is actually an operator array (e.g. [between => ...])
     */
    protected function isOperatorArray(array $value): bool
    {
        $operators = ['gt', 'lt', 'gte', 'lte', 'between', 'neq', 'like', 'in', 'not_in'];

        return count($value) === 1 && in_array(strtolower(array_key_first($value)), $operators, true);
    }
}

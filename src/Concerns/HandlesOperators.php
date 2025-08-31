<?php

namespace Obrainwave\LaravelQueryFilters\Concerns;

trait HandlesOperators
{
    protected function applyOperatorFilters(string $column, array $operators): void
    {
        if (! config('query-filters.operators.enabled', true)) {
            return;
        }

        foreach ($operators as $op => $val) {
            if ($val === null || $val === '') {
                continue;
            }

            switch (strtolower($op)) {
                case 'gt':
                    $this->builder->where($column, '>', $val);
                    break;
                case 'gte':
                    $this->builder->where($column, '>=', $val);
                    break;
                case 'lt':
                    $this->builder->where($column, '<', $val);
                    break;
                case 'lte':
                    $this->builder->where($column, '<=', $val);
                    break;
                case 'between':
                    $range = is_array($val) ? $val : explode(',', $val);
                    if (count($range) === 2) {
                        $this->builder->whereBetween($column, $range);
                    }
                    break;
                case 'like':
                    $this->builder->where($column, 'LIKE', "%{$val}%");
                    break;
                default:
                    $this->applyCaseInsensitive($column, $val);
            }
        }
    }
}

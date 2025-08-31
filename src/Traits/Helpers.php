<?php

namespace Obrainwave\LaravelQueryFilters\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Helpers
{
    protected function applyCaseInsensitiveToBuilder(string $column, $value, string $boolean = 'and', ?Builder $builder = null): void
    {
        $targetBuilder = $builder ?? $this->builder;

        $driver = $targetBuilder->getConnection()->getDriverName();
        $mode = $this->resolveFilterMode($column);

        $apply = function ($q, $col, $val, $bool) use ($driver, $mode) {
            switch ($mode) {
                case 'like':
                    $operator = ($driver === 'pgsql') ? 'ILIKE' : 'LIKE';
                    $val = ($driver === 'pgsql') ? "%{$val}%" : '%'.strtolower($val).'%';

                    if ($driver !== 'pgsql') {
                        $q->whereRaw("LOWER($col) $operator ?", [$val], $bool);
                    } else {
                        $q->where($col, $operator, $val, $bool);
                    }
                    break;
                case 'strict':
                    $q->where($col, $val, null, $bool);
                    break;
                case 'exact':
                default:
                    $operator = ($driver === 'pgsql') ? 'ILIKE' : '=';
                    $val = ($driver === 'pgsql') ? $val : strtolower($val);

                    if ($driver !== 'pgsql') {
                        $q->whereRaw("LOWER($col) $operator ?", [$val], $bool);
                    } else {
                        $q->where($col, $operator, $val, $bool);
                    }
                    break;
            }
        };

        if (is_array($value)) {
            $targetBuilder->where(function ($q) use ($column, $value, $apply) {
                foreach ($value as $val) {
                    $apply($q, $column, $val, 'or');
                }
            }, null, null, $boolean);
        } else {
            $targetBuilder->where(function ($q) use ($column, $value, $apply) {
                $apply($q, $column, $value, 'and');
            }, null, null, $boolean);
        }
    }

    protected function resolveFilterMode(string $column): string
    {
        $modes = config('query-filters.filter_modes', []);
        $default = config('query-filters.default_match', 'exact');

        // 1. Direct exact match
        if (isset($modes[$column])) {
            return $modes[$column];
        }

        // 2. Wildcard checks
        foreach ($modes as $pattern => $mode) {
            if ($pattern === '*') {
                return $mode; // global override for all
            }

            // Convert dot-wildcards to regex
            $regex = '/^'.str_replace(['\*', '\.'], ['.*', '\.'], preg_quote($pattern, '/')).'$/i';

            if (preg_match($regex, $column)) {
                return $mode;
            }
        }

        // 3. Fallback
        return $default;
    }
}

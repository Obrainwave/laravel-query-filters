<?php

namespace Obrainwave\LaravelQueryFilters;

use Illuminate\Database\Eloquent\Builder;

class QueryFiltersManager
{
    public function apply(Builder $query, array $filters): Builder
    {
        foreach ($filters as $name => $value) {
            if (method_exists($this, $name)) {
                $this->$name($query, $value);
            }
        }

        return $query;
    }
}

<?php

namespace Obrainwave\LaravelQueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
<<<<<<< HEAD
    public function scopeFilter(Builder $query, Request | array | null $filters = null): QueryFilter
=======
    public function scopeFilter(Builder $query, Request|array|null $filters = null): QueryFilter
>>>>>>> 77135ce66352db8a8055e8d1eb34d75fa04a8ed4
    {
        $filterClass = $this->getFilterClass();

        if (! class_exists($filterClass)) {
            return $query;
        }

        $filter = (new $filterClass($filters))->setBuilder($query);

        return $filter; // Now you can chain ->status(...)->role(...)->get()
    }

    protected function getFilterClass(): string
    {
        return str_replace('Models', 'Filters', static::class).'Filter';
    }
}

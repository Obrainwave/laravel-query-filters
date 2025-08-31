<?php
namespace Obrainwave\LaravelQueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    public function scopeFilter(Builder $query, Request | array | null $filters = null) : QueryFilter
    {
        $filterClass = $this->getFilterClass();

        if (! class_exists($filterClass)) {
            return $query;
        }

        $filter = (new $filterClass($filters)) ->setBuilder($query);

        return $filter; // Now you can chain ->status(...)->role(...)->get()
    }

    protected function getFilterClass(): string
    {
        return str_replace('Models', 'Filters', static::class) . 'Filter';
    }
}

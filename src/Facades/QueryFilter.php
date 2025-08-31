<?php

namespace Obrainwave\LaravelQueryFilters\Facades;

use Illuminate\Support\Facades\Facade;

class QueryFilter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Obrainwave\LaravelQueryFilters\QueryFilters::class;
    }
}

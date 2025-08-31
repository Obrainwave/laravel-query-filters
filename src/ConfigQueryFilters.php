<?php

namespace Obrainwave\LaravelQueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConfigQueryFilters
{
    protected Builder $builder;
    protected Request $request;
    protected array $filters;

    public function __construct(Request $request, string $model)
    {
        $this->request = $request;
        $this->filters = config("query-filters." . Str::lower(class_basename($model)), []);
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $name => $value) {
            if (isset($this->filters[$name]) && $value !== null && $value !== '') {
                call_user_func($this->filters[$name], $this->builder, $value);
            }
        }

        return $this->builder;
    }
}

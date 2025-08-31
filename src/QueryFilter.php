<?php
namespace Obrainwave\LaravelQueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Obrainwave\LaravelQueryFilters\Concerns\HandlesAllowedFilters;
use Obrainwave\LaravelQueryFilters\Concerns\HandlesGlobalSearch;
use Obrainwave\LaravelQueryFilters\Concerns\HandlesOperators;
use Obrainwave\LaravelQueryFilters\Concerns\HandlesPagination;
use Obrainwave\LaravelQueryFilters\Concerns\HandlesRelationships;
use Obrainwave\LaravelQueryFilters\Concerns\HandlesSorting;
use Obrainwave\LaravelQueryFilters\Traits\Helpers;

abstract class QueryFilter
{
    use HandlesAllowedFilters,
    HandlesGlobalSearch,
    HandlesRelationships,
    HandlesSorting,
    HandlesPagination,
    HandlesOperators,
        Helpers;

    protected ?Builder $builder = null;
    protected array $filters    = [];
    protected ?Request $request = null;

    /**
     * Accept request or array of filters
     */
    public function __construct(Request | array | null $filters = null)
    {
        if ($filters instanceof Request) {
            $this->request = $filters;
            $this->filters = $filters->all();
        } elseif (is_array($filters)) {
            $this->filters = $filters;
        } else {
            $this->filters = [];
        }
    }

    /**
     * Set the query builder instance
     */
    public function setBuilder(Builder $builder): static
    {
        $this->builder = $builder;

        // Apply filters immediately
        $this->applyFilters();

        return $this;
    }

    /**
     * Apply the filters to the builder
     */
    protected function applyFilters(): void
    {
        $allowed   = $this->getAllowedFilters();
        $globalKey = $this->getGlobalSearchKey();

        // Apply global search
        if (! empty($this->filters[$globalKey])) {
            $this->applyGlobalSearch($this->filters[$globalKey]);
        }

        // Apply other filters
        foreach ($this->filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            // skip reserved keys
            if (in_array($key, ['sort', 'page', 'per_page'])) {
                continue;
            }

            if (str_contains($key, '.')) {
                // Handle relationships here
                $this->applyRelationFilters([$key => $value]);
                continue;
            }

            $method            = Str::camel($key);
            $normalizedAllowed = array_map('strtolower', $allowed);

            // If there’s a matching method and it’s allowed, call it
            if (
                method_exists($this, $method) &&
                (empty($allowed) || in_array(strtolower($method), $normalizedAllowed))
            ) {
                $this->$method($value);
                continue;
            }

            if (is_array($value)) {
                $this->applyOperatorFilters($key, $value);
                continue;
            }

            // Fallback: apply generic filter using helper
            if ($this->isAllowedFilter($key, $allowed)) {
                $this->applyCaseInsensitiveToBuilder($key, $value);
            }
        }

        $this->applySorting();
    }

    /**
     * Proxy unknown methods to builder
     */
    public function __call($method, $args)
    {
        // If the underlying query builder has the method, forward the call
        if (method_exists($this->builder, $method)) {
            $result = $this->builder->$method(...$args);
            // If the builder returned itself, allow fluent chaining
            return $result === $this->builder ? $this : $result;
        }

        // Otherwise treat it as a column filter
        $column = $method;
        $value  = $args[0] ?? null;

        if ($value !== null) {
            $this->applyCaseInsensitive($column, $value);
        }

        return $this;
    }

}

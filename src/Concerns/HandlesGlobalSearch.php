<?php
namespace Obrainwave\LaravelQueryFilters\Concerns;

trait HandlesGlobalSearch
{
    protected string $searchKey = 'q';

    protected function getGlobalSearchKey(): string
    {
        $model = $this->builder?->getModel();

        if ($model && method_exists($model, 'getGlobalSearchKey')) {
            return $model->getGlobalSearchKey();
        } elseif ($model && property_exists($model, 'globalSearchKey')) {
            return $model->globalSearchKey;
        }

        return config('query-filters.global_key', $this->searchKey);
    }

    protected function applyGlobalSearch(string $term): void
    {
        $term  = strtolower($term);
        $model = $this->builder->getModel();

        if ($model && method_exists($model, 'getGlobalSearchColumns')) {
            $columns = $model->getGlobalSearchColumns();
        } elseif ($model && property_exists($model, 'globalSearchColumns') &&
            is_array($model->globalSearchColumns) &&
            ! empty($model->globalSearchColumns)) {
            $columns = $model->globalSearchColumns;
        } else {
            $columns = ['name', 'title', 'content', 'body', 'message', 'subject'];
        }

        $this->builder->where(function ($query) use ($term, $columns) {
            foreach ($columns as $column) {
                $query->orWhereRaw("LOWER({$column}) LIKE ?", ["%{$term}%"]);
            }
        });
    }
}

<?php

namespace Obrainwave\LaravelQueryFilters\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MakeFilterCommand extends GeneratorCommand
{
    protected $name = 'make:filter';

    protected $description = 'Create a new query filter class';

    protected $type = 'Filter';

    protected function getStub()
    {
        return __DIR__.'/stubs/filter.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\Filters';
    }

    public function handle()
    {
        parent::handle();

        $model = $this->option('model') ?? $this->guessModel();
        if ($model) {
            $this->addFilterMethods($model);
        }
    }

    protected function guessModel(): ?string
    {
        $filterClass = $this->getNameInput();
        $modelName = Str::before($filterClass, 'Filter');
        $modelClass = "App\\Models\\{$modelName}";

        return class_exists($modelClass) ? $modelClass : null;
    }

    protected function addFilterMethods(string $model)
    {
        $instance = new $model;
        $table = $instance->getTable();

        // Allowed filters: model property or config
        $allowed = property_exists($instance, 'allowedFilters')
        ? $instance->allowedFilters
        : config('query-filters.allowed_filters', []);

        // Get fields: fillable or all minus guarded
        $fields = $instance->getFillable();
        if (empty($fields)) {
            $allColumns = Schema::getColumnListing($table);
            $fields = array_diff($allColumns, $instance->getGuarded());
        }

        $path = $this->getPath($this->qualifyClass($this->getNameInput()));
        $contents = file_get_contents($path);

        // Extract existing method names to avoid duplicates
        preg_match_all('/public function (\w+)\(/', $contents, $matches);
        $existingMethods = $matches[1] ?? [];

        $methods = '';
        foreach ($fields as $field) {
            if (! empty($allowed) && ! in_array($field, $allowed)) {
                continue; // skip if not allowed
            }

            $methodName = Str::camel($field);
            if (in_array($methodName, $existingMethods)) {
                continue; // skip if method already exists
            }

            $methods .= <<<PHP

                public function {$methodName}(\$value)
                {
                    \$this->builder->where('{$field}', \$value);
                    return \$this;
                }

            PHP;
        }

        // Insert methods before the last closing brace of the class
        if (! empty($methods)) {
            $contents = preg_replace('/}\s*$/', $methods."\n}", $contents);
            file_put_contents($path, $contents);
            $this->info("Added filter methods for [$model].");
        } else {
            $this->info("No new methods to add for [$model].");
        }
    }

    protected function getOptions()
    {
        return [
            [
                'model', null,
                \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
                'The model to generate filters for',
            ],
        ];
    }
}

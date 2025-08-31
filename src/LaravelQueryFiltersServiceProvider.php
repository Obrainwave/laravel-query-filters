<?php

namespace Obrainwave\LaravelQueryFilters;

use Obrainwave\LaravelQueryFilters\Commands\MakeFilterCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelQueryFiltersServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-query-filters')
            ->hasConfigFile('query-filters')
            // If you don’t need views yet, you can remove this:
            // ->hasViews()
            // If you don’t need a migration yet, you can remove this:
            // ->hasMigration('create_query_filters_table')
            ->hasCommand(MakeFilterCommand::class);

    }

    public function registeringPackage()
    {
        // Bind your core service into the container
        $this->app->singleton('query-filters', function () {
            return new QueryFiltersManager;
        });
    }
}

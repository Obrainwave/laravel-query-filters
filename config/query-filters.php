<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Global Search Key
    |--------------------------------------------------------------------------
    |
    | Query parameter used for global searches across multiple columns.
    | Default is "q":
    |
    |   /users?q=john
    |
    | Custom key example:
    |
    |   /users?search=john
    |   'global_key' => 'search'
    |
    | You can also override per model via $globalSearchKey property.
    |
    */
    'global_key'      => 'q',

    /*
    |--------------------------------------------------------------------------
    | Default Allowed Filters
    |--------------------------------------------------------------------------
    |
    | Columns that can be filtered globally. Only listed keys are allowed.
    | Example usage in URL:
    |
    |   /users?status=active&role=admin
    |
    */
    'allowed_filters' => [
        'status',
        'role',
        'email',
        'created_at',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Filter Matching Mode
    |--------------------------------------------------------------------------
    |
    | Controls how filter values are matched in the DB:
    | - 'exact'  => Case-insensitive exact match
    | - 'like'   => Partial match (case-insensitive)
    | - 'strict' => Case-sensitive exact match
    |
    | Example:
    |   /users?name=John         => exact match by default
    |   /users?name=John&mode=like => partial match if mode is changed
    |
    */
    'default_match'   => 'exact',

    /*
    |--------------------------------------------------------------------------
    | Per-Filter Matching Modes
    |--------------------------------------------------------------------------
    |
    | Override default matching for specific filters.
    | Supports wildcards:
    |
    |   /users?email=john@example.com
    |   'email' => 'strict'        // case-sensitive
    |
    |   /users?name=jo
    |   'name' => 'like'           // partial match
    |
    |   /users?user.status=active
    |   'user.*' => 'like'         // wildcard on all user columns
    |
    */
    'filter_modes'    => [
        // 'email'    => 'strict',
        // 'name'     => 'like',
        // 'user.*'   => 'like',
        // '*.status' => 'strict',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Defaults
    |--------------------------------------------------------------------------
    |
    | Default pagination settings:
    |
    |   /users?per_page=10          // use URL param
    |   User::filter($request)->paginate(5); // method-level
    |   Config fallback if none specified
    |
    */
<<<<<<< HEAD
    'pagination'      => [
        'per_page'     => 1,   // default items per page
=======
    'pagination' => [
        'per_page' => 1,   // default items per page
>>>>>>> 77135ce66352db8a8055e8d1eb34d75fa04a8ed4
        'max_per_page' => 100, // maximum items allowed per page
    ],

    /*
    |--------------------------------------------------------------------------
    | Sorting Defaults
    |--------------------------------------------------------------------------
    |
    | Default and allowed sorting columns:
    |
    |   /users?sort=created_at      // ascending
    |   /users?sort=-created_at     // descending
    |
    | Example allowed_columns:
    |   'allowed_columns' => ['name', 'created_at']
    |
    */
<<<<<<< HEAD
    'sorting'         => [
        'allowed_columns' => [],   // empty = all allowed
        'default'         => null, // e.g., 'created_at' or '-created_at'
=======
    'sorting' => [
        'allowed_columns' => [], // empty = all allowed
        'default' => null, // e.g., 'created_at' or '-created_at'
>>>>>>> 77135ce66352db8a8055e8d1eb34d75fa04a8ed4
    ],

    /*
    |--------------------------------------------------------------------------
    | Operator Filters
    |--------------------------------------------------------------------------
    |
    | Enable advanced operators:
    |   gt, gte, lt, lte, between, like
    |
    | URL examples:
    |
    |   /users?age[gt]=18            // greater than 18
    |   /users?age[between]=18,30    // age between 18 and 30
    |   /users?name[like]=jo         // partial match "jo"
    |
    | Disable by setting 'enabled' => false
    |
    */
    'operators'       => [
        'enabled' => true,
    ],

];

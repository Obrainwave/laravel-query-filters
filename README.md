# Laravel Query Filters

A lightweight Laravel package to apply filters, global search, and relationship filters to your Eloquent queries. Supports Laravel 9–12.

---

## Installation

```bash
composer require obrainwave/laravel-query-filters
```

Publish the configuration:

```bash
php artisan vendor:publish --provider="Obrainwave\LaravelQueryFilters\LaravelQueryFiltersServiceProvider" --tag="config"
```

---

## Usage

### 1. Simple Filtering

```php
use Obrainwave\LaravelQueryFilters\QueryFilters;
use App\Models\User;

// Apply filters from request or array
$users = QueryFilters::applyFilter(
    UserFilter::class,   // Your custom filter class
    request(),           // Request or array of filters
    User::query()        // Eloquent builder
)->paginate();
```

---

### 2. Relationship Filtering

Filter on nested relationships using dot notation:

```php
$users = User::filter([
    'posts.comments.title' => request('title'),
    'or:posts.comments.body' => request('body'),
])->with(['posts.comments'])->paginate();
```

**Notes:**

- Supports `or:` prefix for OR logic.
- Supports arrays of values.
- Supports operators like `gt`, `lt`, `between`, `like`, etc.
- Nested OR (`posts.or:comments`) is planned for future versions.

---

### 3. Pagination

Pagination works with these sources in order:

1. URL query parameter (`?per_page=5`)
2. Passed as argument to `paginate($perPage)`
3. Default config (`config('query-filters.pagination.per_page')`)

Example:

```php
$users = User::filter(request())->paginate(); // follows per_page precedence
```

---

### 4. Global Search

Global search is enabled by default using `q` parameter:

```php
$users = User::filter(['q' => 'john'])->get();
```

You can change the key in config:

```php
'global_key' => 'search',
```

---

### 5. Operators

Supports operator filters:

```php
$users = User::filter([
    'age' => ['gt' => 18],
    'created_at' => ['between' => ['2023-01-01', '2023-12-31']]
])->get();
```

Config:

```php
'operators' => [
    'enabled' => true
]
```

---

### 6. Generating a Custom Filter Class

```bash
php artisan make:filter UserFilter
```

Example:

```php
namespace App\Filters;

use Obrainwave\LaravelQueryFilters\QueryFilter;

class UserFilter extends QueryFilter
{
    public function role($value)
    {
        $this->builder->where('role', $value);
        return $this;
    }

    public function status($value)
    {
        $this->builder->where('status', $value);
        return $this;
    }
}
```

Apply it:

```php
$users = QueryFilters::applyFilter(UserFilter::class, request(), User::query())->paginate();
```

---

### 7. Notes

- `->sort()` is not implemented yet. Sorting will follow config defaults if needed.  
- Supports Laravel 9–12.  
- Works with PHP 8.0+.  

---

### 8. Configuration

`config/query-filters.php`

```php
return [
    'global_key'      => 'q',       // Query param for global search
    'allowed_filters' => ['status','role','email','created_at'],
    'default_match'   => 'exact',   // exact | like | strict
    'filter_modes'    => [],
    'pagination' => [
        'per_page'    => 15,
        'max_per_page'=> 100,
    ],
    'sorting' => [
        'allowed_columns' => [],
        'default' => null,
    ],
    'operators' => [
        'enabled' => true,
    ],
];
```


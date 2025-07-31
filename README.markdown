# YSM Responsable

A Laravel package that provides standardized JSON response macros for consistent API responses. It adds `success`
and `error` macros to the `Response` facade and `response()` helper, making it easy to return structured JSON responses
with optional pagination metadata.

## Features

- `success` macro for standardized success responses with message, data, status code, and pagination support.
- `error` macro for consistent error responses with message, status code, and error details.
- Supports Laravel's pagination (`LengthAwarePaginator`, `Paginator`, `CursorPaginator`).
- Lightweight and easy to integrate into Laravel 12+ applications.

## Requirements

- PHP 7.2 or higher
- Laravel 6.0 or higher

## Installation

Install the package via Composer:

```bash
composer require ysm/responsable
```

The package uses Laravel's auto-discovery to register the `ResponsableServiceProvider`. If auto-discovery is disabled,
manually add the provider to `config/app.php`:

```php
'providers' => [
    // Other providers...
    YSM\Responsable\ResponsableServiceProvider::class,
],
```

## Usage

The package provides two response macros: `success` and `error`, accessible via the `Response` facade or `response()`
helper.

### Example 1: Fetching a Collection of Posts

Return a collection of posts with a success response:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Filters\PostFilter;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Response;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::filterable(PostFilter::class)->limit(5)->get();

        return Response::success(
            message: 'Posts fetched success',
            data: PostResource::collection($posts)
        );
    }
}
```

**Output**:

```json
{
    "status": true,
    "message": "Posts fetched success",
    "code": 200,
    "data": [
        {
            "id": 1,
            "title": "Post 1"
        },
        {
            "id": 2,
            "title": "Post 2"
        }
    ]
}
```

### Example 2: Fetching Paginated Posts

Return paginated posts with metadata:

```php
public function paginated()
{
    $posts = Post::filterable(PostFilter::class)->paginate(5);

    return Response::success(
        message: 'Posts fetched success',
        data: PostResource::collection($posts),
        paginator: $posts
    );
}
```

**Output**:

```json
{
    "status": true,
    "message": "Posts fetched success",
    "code": 200,
    "data": [
        {
            "id": 1,
            "title": "Post 1"
        },
        {
            "id": 2,
            "title": "Post 2"
        }
    ],
    "meta": {
        "total": 50,
        "per_page": 5,
        "current_page": 1,
        "last_page": 10,
        "first_item": 1,
        "last_item": 5
    }
}
```

### Example 3: Handling Errors

Return an error response when a condition fails:

```php
public function errors(Request $request)
{
    if (isset($request->case)) {
        return Response::success('Posts fetched success');
    }

    return Response::error('Posts fetched error');
}
```

**Output (Error)**:

```json
{
    "status": false,
    "message": "Posts fetched error",
    "code": 422,
    "errors": []
}
```

### Macro Signatures

- **`success` Macro**:
  ```php
  Response::success(
      string $message = '',
      array|object $data = [],
      int $code = 200,
      \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Pagination\CursorPaginator|null $paginator = null
  ): \Illuminate\Http\JsonResponse
  ```

- **`error` Macro**:
  ```php
  Response::error(
      string $message = '',
      int $code = 422,
      array $errors = []
  ): \Illuminate\Http\JsonResponse
  ```

You can also use the `response()` helper:

```php
return response()->success('Operation successful', ['data' => 'value'], 200);
return response()->error('Validation failed', 422, ['field' => 'Error']);
```

## IDE Support

To enable autocompletion for the `success` and `error` macros in your IDE (e.g., PHPStorm, VS Code):

1. Install the `barryvdh/laravel-ide-helper` package in your Laravel project:
   ```bash
   composer require --dev barryvdh/laravel-ide-helper
   ```

2. Generate the IDE helper file:
   ```bash
   php artisan ide-helper:generate
   ```

This creates a `_ide_helper.php` file that includes the `success` and `error` macros for autocompletion.

## Contributing

Contributions are welcome! Please submit issues or pull requests to
the [GitHub repository](https://github.com/your-username/ysm-responsable).

## License

This package is open-sourced under the [MIT License](LICENSE).

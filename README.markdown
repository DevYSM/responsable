# YSM Responsable

A Laravel package that provides standardized response macros for both JSON (API) and web (redirect) responses, along
with helper functions to manage session data. It adds `success` and `error` macros to the `Response` facade (for JSON)
and `RedirectResponse` class (for web), and provides `responsable()` and `responsable_forget()` helpers for session data
management in Laravel 12+ applications.

## Features

- `success` and `error` macros for JSON API responses with optional pagination metadata.
- `success` and `error` macros for web redirects with session-based flash or persistent data.
- `responsable()` helper to retrieve session data in a structured format.
- `responsable_forget()` helper to clear session data.
- Supports Laravel's pagination (`LengthAwarePaginator`, `Paginator`, `CursorPaginator`).
- Lightweight and easy to integrate.

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
    // Other providers
    YSM\Responsable\ResponsableServiceProvider::class,
],
```

## Usage

The package provides `success` and `error` macros for both JSON responses (via `Response` facade or `response()` helper)
and web redirects (via `RedirectResponse`), along with helper functions `responsable()` and `responsable_forget()` for
session data management.

### JSON Response Macros

#### Example 1: Fetching a Collection of Posts

Return a collection of posts with a JSON success response:

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

#### Example 2: Fetching Paginated Posts

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

#### Example 3: Handling JSON Errors

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

### Web Redirect Macros

#### Example 4: Success Redirect with Session Data

Redirect with a success message stored in the session:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
    ]);

    Post::create($validated);

    return redirect()->route('posts.index')->success('Post created successfully', 201, ['title' => $validated['title']]);
}
```

**Session Data** (accessible via `responsable()`):

```php
responsable();
// Returns:
// [
//     'type' => 'success',
//     'message' => 'Post created successfully',
//     'code' => 201,
//     'data' => ['title' => 'Post Title'],
//     'errors' => []
// ]
```

#### Example 5: Error Redirect with Validation Errors

Redirect with an error message and validation errors:

```php
public function update(Request $request, Post $post)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
    ]);

    if (!$post->update($validated)) {
        return redirect()->back()->error('Failed to update post', 422, ['title' => 'Update failed']);
    }

    return redirect()->route('posts.index')->success('Post updated successfully');
}
```

**Session Data** (accessible via `responsable()`):

```php
responsable();
// Returns:
// [
//     'type' => 'error',
//     'message' => 'Failed to update post',
//     'code' => 422,
//     'data' => [],
//     'errors' => ['title' => 'Update failed']
// ]
```

#### Example 6: Persistent Session Data

Use `$persist = true` to store data beyond the next request:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
    ]);

    Post::create($validated);

    return redirect()->route('posts.index')->success('Post created successfully', 201, ['title' => $validated['title']], true);
}
```

### Helper Functions

#### `responsable()`

Retrieve session data stored by web macros in a structured array:

```php
$data = responsable();
```

**Example in a Controller**:

```php
public function show()
{
    $response = responsable();
    if ($response['type'] === 'success') {
        // Handle success
        return view('posts.index', ['message' => $response['message']]);
    }
    // Handle error
    return view('posts.index', ['error' => $response['message']]);
}
```

#### `responsable_forget()`

Clear session data stored by web macros:

```php
responsable_forget();
```

**Example in a Controller**:

```php
public function clear()
{
    responsable_forget();
    return redirect()->route('posts.index');
}
```

### Accessing Session Data in Views

Use the `responsable()` helper in Blade views for a cleaner interface:

```blade
@php
    $response = responsable();
@endphp

@if ($response['type'])
    <div class="alert alert-{{ $response['type'] }}">
        <strong>{{ $response['message'] }}</strong>
        @if ($response['errors'])
            <ul>
                @foreach ($response['errors'] as $field => $error)
                    <li>{{ $field }}: {{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
```

Alternatively, access session data directly:

```blade
@if (session('response_type'))
    <div class="alert alert-{{ session('response_type') }}">
        <strong>{{ session('message') }}</strong>
        @if (session('errors'))
            <ul>
                @foreach (session('errors') as $field => $error)
                    <li>{{ $field }}: {{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
```

### Macro Signatures

- **JSON `success` Macro**:
  ```php
  Response::success(
      string $message = '',
      array|object $data = [],
      int $code = 200,
      \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Pagination\CursorPaginator|null $paginator = null
  ): \Illuminate\Http\JsonResponse
  ```

- **JSON `error` Macro**:
  ```php
  Response::error(
      string $message = '',
      int $code = 422,
      array $errors = []
  ): \Illuminate\Http\JsonResponse
  ```

- **Web `success` Macro**:
  ```php
  redirect()->success(
      string $message = '',
      int $code = 200,
      array|object $data = [],
      bool $persist = false
  ): \Illuminate\Http\RedirectResponse
  ```

- **Web `error` Macro**:
  ```php
  redirect()->error(
      string $message = '',
      int $code = 422,
      array $errors = [],
      bool $persist = false
  ): \Illuminate\Http\RedirectResponse
  ```

You can also use the `response()` helper for JSON responses:

```php
return response()->success('Operation successful', ['data' => 'value'], 200);
```

## IDE Support

To enable autocompletion for the `success`, `error`, `responsable()`, and `responsable_forget()` functions in your IDE (
e.g., PHPStorm, VS Code):

1. Install the `barryvdh/laravel-ide-helper` package:
   ```bash
   composer require --dev barryvdh/laravel-ide-helper
   ```

2. Generate the IDE helper file:
   ```bash
   php artisan ide-helper:generate
   ```

This creates a `_ide_helper.php` file that includes the `success` and `error` macros for both `Response`
and `RedirectResponse`, as well as the `responsable()` and `responsable_forget()` helpers.
 
## License

This package is open-sourced under the [MIT License](LICENSE).

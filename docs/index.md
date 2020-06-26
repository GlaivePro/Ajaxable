# Ajaxable

A simple HTTP interface for Laravel apps. You don't have to make any routes or controllers for basic CRUD.

Ajaxable is a Laravel package that allows you to control (create, edit, delete) Eloquent models without bothering you on the backend.

## Quick example

The package is enabled by adding a trait to your model.

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use \GlaivePro\Ajaxable\Traits\Ajaxable;
	//
}
```

Post the following request to `route('ajaxable.update')`

```js
{
	model: "App\Article",
	id: "13",
	key: "title",
	value: "A new title"
}
```

And you will update the title on article with id 13.

All the CRUD operations are supported as well as some more advanced features.

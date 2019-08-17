# Ajaxable

A simple HTTP interface for Laravel apps. You don't have to make any routes or controllers for basic CRUD.

Ajaxable is a Laravel package that allows you to control (create, edit, delete) Eloquent models without bothering you on the backend. Or even frontend. Add markup to your html or use helpers and it works!

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

## Doing the job on the frontend as well

> ⚠️ These features are still in the beta and will undergo breaking changes in the future.

As a beta feature we also have a jQuery library that watches your fields for changes and posts the updates. And there's even a field maker, so you could actually just do this in your view.

```html
{{$article->editor('title')}}

{{$article->deleteButton('Remove article')}}
```

And those helpers would generate the following HTML that will be watched by the jQuery lib.

```html
<input
	class="ajaxable-edit"
	data-model="App\Article"
	data-id="13"
	data-key="title"
	value="The old title" >

<button
	class="ajaxable-delete"
	data-model="App\Article"
	data-id="13" >
		Remove article
	</button>
```

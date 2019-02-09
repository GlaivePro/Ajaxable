# Ajaxable

Ajaxable is a Laravel package that allows you to control (create, edit, delete) Eloquent models without bothering you on the backend. Or even frontend. Add markup to your html or use helpers and it works!


## Getting started - installation, setup and examples

Install via composer.
``` bash
$ composer require glaivepro/ajaxable
```

Add the trait to your model.
```php
namespace App;

use Illuminate\Database\Eloquent\Model;
use GlaivePro\Ajaxable\Traits\Ajaxable;

class Article extends Model
{
    use Ajaxable;
	//
}
```

Create ajaxable input in your view, add the CSRF token and the javascript library (this one depends on jQuery):
```html
<meta name="csrf-token" content="{{ csrf_token() }}">

<label>
	Title
	{{$article->editor('title')}}
</label>

<!-- provide jQuery somewhere please! -->
@include('ajaxable::jquery')
```

Now you have a field that saves any changes to database (if the user is authenticated).

If you need it a bit more custom, you can create the HTML controls yourself. For example this is how you delete `App\Tag` with the specified ID and remove the row (this is why it's wrapped in `.ajaxable-row`):
```html
<table>
	<tr>
		<th> ID <th> Name <th> Controls
	<tbody id="tag-list">
		<tr class="ajaxable-row">
			<td>1
			<td>First tag
			<td><button
				class="ajaxable-delete"
				data-model="App\Tag"
				data-id="1" >
					Delete
				</button>
		<tr class="ajaxable-row">
			<td>2
			<td>Another tag
			<td><button
				class="ajaxable-delete"
				data-model="App\Tag"
				data-id="2" >
					Delete
				</button>
</table>
```

Of course, you are not obliged to use the provided javascript. Here's how you could create an article with title "Test article 4" using plain javascript:

```javascript
fetch("{{route('ajaxable.create')}}", {
  headers: {
	"Content-Type": "application/json",
	"Accept": "application/json",
	"X-Requested-With": "XMLHttpRequest",
	"X-CSRF-Token": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
  },
  method: "post",
  credentials: "same-origin",
  body: JSON.stringify({
	model: "App\Article",
	attributes: {title: "Test article 4"}
  })
});
```

## Usage in details

### Preliminaries
Make your model ajaxable.
```php
use Illuminate\Database\Eloquent\Model;
use GlaivePro\Ajaxable\Traits\Ajaxable;

class Article extends Model
{
    use Ajaxable;
	//
}
```

In case you want fields to update models (i.e. you are not working with HTTP interface directly), include the CSRF token and JS library (it depends on jQuery) in your views.
```html
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- other html -->

<!-- provide jQuery please! -->
@include('ajaxable::javascript')
```


### Generating HTML

#### Editing models

To obtain a field that synces changes to database, do this in your Blade template:

```html
<!-- this is a field that updates `name` on `$tag` object. -->
{{$tag->editor('name')}}
```

Some options can be supplied as well
```html
{{$tag->editor('disabled', ['type' => 'checkbox'])}}

{{$tag->editor('group', ['type' => 'select', 'options' => [['value' => 1, 'text' => 'Group 1'], ['value' => 2, 'text' => 'Group 2']]])}}
```

List of supported options:

Option | Description
---|---
`type` | `select` or `textarea` will make the respective field, anything else sets the type attribute on input element
`classes` | adds classes (string or array) to element
`attributes` | adds attributes (array of `'attribute' => 'value'`); doesn't work for type or class
`options` | array of arrays (containing value and text keys) or strings, for select elements

### Deleting models

To obtain a button that invokes deletion of element once clicked, use the `deleteButton` method and supply the content of the element.

```html
{{$tag->deleteButton('Delete this item')}}
```

You can specify some options:

```html
{{$tag->deleteButton('Remove', ['tag' => 'a'])}}
```

List of supported options:

Option | Description
---|---
`tag` | HTML tag
`classes` | adds classes (string or array) to element
`attributes` | adds attributes (array of `'attribute' => 'value'`); doesn't work for class

If you need button (and maybe something else) removed on successful delete, wrap it (including the button) in `.ajaxable-row`.

### Creating models

To create models, use the static `creatorButton()` method.

```html
{{App\Comment::creatorButton('Add')}}
```

Set some initial attribute values

```html
{{App\Comment::creatorButton('Post comment', ['values' => ['article_id' => $article->id]])}}
```

Add the fields so user could set some values before creation as well

```html
{{App\Comment::creatorField('name')}}
{{App\Comment::creatorField('text', ['type' => 'textarea'])}}

{{App\Comment::creatorButton('Post comment')}}
```

If you need multiple creators with fields for same model on the same page, supply an ID to distinguish the sets.

```html
{{App\Tag::creatorField('name', ['creator' => 'generic-tag-creator'])}}
{{App\Tag::creatorField('name', ['creator' => 'special-tag-creator'])}}

{{App\Tag::creatorButton('Create generic tag', ['creator' => 'generic-tag-creator'])}}
{{App\Tag::creatorButton('Crete special tag', ['creator' => 'special-tag-creator'])}}
```

In addition `creatorField` supports all the same methods that `editor` does and `creatorButton` supports all the options that `deleteButton` does.


### Writing your own HTML

In many cases you'll want to write your own html but still want it to work with the included javascript library. You'll just have to add a little markup.

We'll start with editing as that's the simplest.

#### Editing model attributes

Specify the model and add the `ajaxable-edit` class to your field.
```html
<!-- Example html: name of \App\Tag with id 12345 that updates in the database as you change the value in the field -->
<input
	class="ajaxable-edit"
	data-model="App\Tag"
	data-id="12345"
	data-key="name" >
```

In case of a [validation error](authorization-and-validation) user will get an alert. If you want to display it on html instead, wrap the input in `.form-group`. Error will be added in `span.error-block.help-block` and class `has-error` will be added to `.form-group`.


### Deleting models

A click on `.ajaxable-delete` will remove an item specified by `data-*` attributes. HTML will also get removed if you wrap it (including the button) in `.ajaxable-row`.

```html
<table>
	<tr>
		<th> ID <th> Name <th> Controls
	<tbody id="tag-list">
		<tr class="ajaxable-row">
			<td>1
			<td>First tag
			<td><button
				class="ajaxable-delete"
				data-model="App\Tag"
				data-id="1" >
					Delete
				</button>
		<tr class="ajaxable-row">
			<td>2
			<td>Another tag
			<td><button
				class="ajaxable-delete"
				data-model="App\Tag"
				data-id="2" >
					Delete
				</button>
</table>
```


#### Creating new models

A click on `.ajaxable-creator` will make a request to create an object. Specify model in `data-model` attribute.

You may also supply any additional `data-attribute_*` attributes to be set on the new model.
```html
<button
	class="ajaxable-creator"
	data-model="App\Comment"
	data-attribute_article_id="2545" >
		Create
</button>
```

#### User supplied values for new models

You can have an input that sets (on change) the value on creator:

```html
Tag name:
<input
	class="ajaxable-new-attribute"
	data-key="name"
	data-creator="#theCreatorInThisExample" >
<button
	class="ajaxable-creator"
	id="theCreatorInThisExample"
	data-model="App\Tag" >
		Create
</button>
```

In case of [validation error](#authorization-and-validation) user will get alerts. If you want to display alerts neatly instead, wrap input in `.form-group`:
```html
Tag name:
<div class="form-group"
	<input
		class="ajaxable-new-attribute"
		data-key="name"
		data-creator="#theCreatorInThisExample" >
</div>
```
This `.form-group` will then get class `has-error` added and `span.error-block.help-block` appended with the message.

#### Adding the created model to a list

Often times you have a list of entries and need the new item appended to the list. We can do this if your models [return html](#returning-html-instead-of-json).

Creator should have the list specified in `data-ajaxable-list`.
```html
<ul id="tag-list">
	<li>Existing tag 1
	<li>Existing tag 2
</ul>
<button
	class="ajaxable-creator"
	data-model="App\Tag"
	data-ajaxable-list="#tag-list" >
		Create
</button>
```

The above example would append created html (using `ajaxable.tag` view) to `#tag-list` and add class `ajaxable-highlight` for 1.5 seconds (for highlighting purposes). If you want to prepend instead, specify `data-ajaxable-list-position="first"`. If you want the new item scrolled into view, specify `data-ajaxable-scroll="true"`.


### Using HTTP interface directly

Use your own JavaScript (or whatever else) to invoke stuff happening on your models. Here's what we provide:

Route | Required parameters | Optional parameters | Response
---|---|---|---
`ajaxable.create` | `model` | Initial values (key:value pairs in `attributes`) | Model in JSON, optional HMTL.
`ajaxable.update` | `model`, `id`, `key`, `value` | | Model in JSON, optional HTML
`ajaxable.delete` | `model`, `id`  | | Confirmation only
`ajaxable.updateOrCreate` | `model` | Constraints (key:value pairs in `wheres`) and values (key:value pairs in `attributes`) | Model in JSON, optional HTML
`ajaxable.control` | `model`, `id`, `action` | `parameters` - supply whatever to be passed to called action. | Whatever you decide to return
`ajaxable.addMedia` | `model`, `id`, `media` | `collection`, `name` | Media object and URL
`ajaxable.deleteMedia` | `model`, `id`, `media_id` | | Confirmation only

**Example**. To update `title` to `New Title` on `App\Article` with ID 155 you'd POST `{model: 'App\Article', id: 155, key: 'title', value: 'New Title'}` to `{{route(ajaxable.update)}}`.

The `ajaxable.control` route could in theory call any method on your model. In practice the included checks refuse most actions and you should explicitly list what you want to allow in the model. Use sparingly!

The above routes will only work for POST requests and you must include XSRF token so Laravel would let you through.

Here are some get routes to retrieve data:

Route | Required parameters | Optional parameters | Response
---|---|---|---
`ajaxable.retrieve` | `model`, `id` | | Confirmation, model in JSON, optional HTML
`ajaxable.list` | `model` | `wheres` as key:value pairs, `scopes` as `scopeName` or `scopeName:parameter1,param2` | Model in JSON, optional HTML
`ajaxable.getMedia` | `model`, `id` | `collection` | Media object and URL

**Note**. The media routes will only work if your model uses [Laravel Medialibrary](https://docs.spatie.be/laravel-medialibrary). 

### Returning HTML

Some requests (`create`, `update`, `updateOrCreate`, `retrieve`) can include rendered HTML in the response if you specify `view: true` on the request.

To return rendered html you should create a view. By default `'ajaxable.'.camel_case(class_basename($model))` will be looked for. For example, `App\Tag` and `App\Stats\UserFault` will look for `ajaxable.tag` and `ajaxable.userFault` respectively so you should create `tag.blade.php` and `userFault.blade.php` inside `resources/views/ajaxable`.

This can be overriden by specifying `$rowView` property on the model or supplying `viewname` in the request.

Camel of basename will be passed to view. For example `$tags` or `$userFault`.

The `list` method is able to return HTML as well if you specify `view: true`. The response will be rendered through template selected in this order:

- Template specified in requests `viewname` (plural of camel of basename will be passed, i.e. `$tags` or `$userFaults`).
- Concatenation of template specified in requests `rowviewname`.
- Template specified in models `$listView`.
- Template `ajaxable.`.str_plural(camel_case(class_basename($model))).
- Concatenation of template specified in models `$rowView`.
- Concatenation of template `ajaxable.`.camel_case(class_basename($model)).

### Authorization and validation

By default the actions are allowed to all authenticated users. This is only appropriate when there is just a single class of users. In most cases you'd override the `allowAjaxableTo` method on your model

Here's an example that one might use for the contact form messages

```php
public function allowAjaxableTo(string $action) : bool
{
	if ('create' == $action)
	{
		request()->validate([
			'name' => 'required',
			'email' => 'required|email',
			'message' => 'required',
		]);
		
		return true;
	}

	$allowedActionsForAuthorized = [ 'update', 'delete', 'retrieve', 'list', ];
	
	if (in_array($action, $allowedActionsForAuthorized))
		return auth()->check();
	
	return false;
}
```

### Customizing responses

By default a successful request is responded with a JSON array that contains `"success": 1`. Something else like `object`, `collection`, `view` could also be set for the appropriate actions.

However, all of this can be overriden. The requests go through a `respondAfter` method on the model that will call try to call a specific method like `respondAfterDelete`, `respondAfterCreate`, `respondAfterRetrieve` etc, the last word corresponding to route name or the `action` value in case of `ajaxable.control` route. If the specific method is not found, `fallbackResponse` will be called which does some sensible defaults.

If you want to customize responses after retrieval, you overwrite the corresponding method on your model:

```php
respondAfterDelete(bool $success)
{
	if ($success)
		return [
			'success' => 1,
			'deleted_on' => \Carbon\Carbon::now()->format('M d');
		];
		
	return 'Deletion was refused because of reasons.';
}
```

You may override all of the `respondAfter` method if you feel the need.
```php
public function respondAfter(string $action, bool $success)
{
	return ['success' => 'uncertain'];
}
```


## Tips & Tricks

This section tells you how to do some stuff that the package doesn't explicitly implement.

### Value should be transformed before saving or retrieving
Use the Laravels mechanic of getters and setters (accessors).

### I must do something else when action is happening.
Use [Laravel Events](https://laravel.com/docs/master/events). If you need to distinguish ajaxable events from other, check `request()`.

And remember that you can add small handlers directly in the model. For example, refuse deleting if the model is used and clean up otherwise:

```php
protected static function boot()
{
    parent::boot();
    	
    static::deleting(function($tag) {
		if ($tag->articles()->count())
			return false;
		
		$tag->synonyms()->detach();
    });
}
```

### Field name is uncertain or it's json or it's complicated...
Usually this is a sign that you should ride on your own. This library makes the simple stuff simpler. If it's complicated, the overhead of making your own route and controller will be miniscule.

However, if you insist that you really want to use this and it's only this one time... well, there are a few paths to hack around.

You can do whatever you want in the `allowAjaxableTo` method before you allow ajaxable to work further.

```php
public function allowAjaxableTo(string $action)
{
	if (!Gate::allows($action, $this))
		return false;
	
	if ('create' == request()->action 
	     && isset(request()->attributes['user_id'])
		 && 'self' == request()->attributes['user_id'] )
	{
		// you can tinker with request here
		request()->attributes['user_id'] = auth()->id();
		
		// and you can also work with an object as it's already instantiated
		$object->updatedByJohn = 'probably';
	}
}
```


## Goals and possible goals

- Develop the frontend helpers to support customization.
- Allow specifying the ajaxable list on `creatorButton` more easily.
- Default classes/attributes for HTML.
- Support for option groups?
- Or maybe leave the frontend helpers for the simplest cases only?
- Add optional change confirmation?
- Make delete confirmation message customizable.
- Create more javascript libraries. Both `ajaxable::jquery` and `ajaxable::es6` should be available at least.
- Provide config to insert JS automatically. Something like `ajaxable.auto` config that accepts `jquery`, `es6` or falsy.
- Introduce some javascript events and drop stuff like scroll, highlight, reseting inputs and reporting errors to user.
- Support uploads in javascript library.
- Write some tests.
- Improve documentation.
- Implement option to prepare responses through Eloquent resources?
- Retrieve single fields?
- BelongsToMany support?
- Refactor Traits/AjaxableHtml.
- Support upload field... with `ajaxable-file` and `ajaxable-files` classes?
- Split the ReadMe into files?

## Change log

0.10 is a major rewrite of library. We intend to have less changes (especially less breaking changes) in the future and we are aiming to make this library production-ready.

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Security

If you discover any security related issues, please email juris@glaive.pro instead of using the issue tracker.


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

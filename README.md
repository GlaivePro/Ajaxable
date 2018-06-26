# Ajaxable

Ajaxable is a Laravel package that allows you to control (create, edit, delete) models without bothering you on the PHP or JavaScript side. Just add markup to your html, use the trait in the model and it works!

## Install

Via Composer.
``` bash
$ composer require glaivepro/ajaxable
```

## Usage

### Editing model attributes

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

Add the csrf token and include the library (it depends on jQuery).
```html
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- your html -->
<!-- Example html: name of \App\Tag with id 12345 that updates in the database as you change the value in the field -->
<input
	class="ajaxable-edit"
	data-model="tag"
	data-id="12345"
	data-key="name" >

<!-- provide jQuery here please! -->

<!-- finally include our library -->
@include('ajaxable::javascript')
```

In case of a validation error (read on that later) user will get an alert. If you want to display it on html instead, wrap the input in `.form-group`. Error will be added in `span.error-block.help-block` and class `has-error` will be added to `.form-group`.


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
				data-model="tag"
				data-id="1" >
					Delete
				</button>
		<tr class="ajaxable-row">
			<td>2
			<td>Another tag
			<td><button
				class="ajaxable-delete"
				data-model="tag"
				data-id="2" >
					Delete
				</button>
</table>
```


### Creating new models

A click on `.ajaxable-creator` will make a request to create an object. Make sure to specify `data-model` attribute!

You may also supply any additional `data-*` attributes to be set (if columns exist) on the new model.
```html
<button
	class="ajaxable-creator"
	data-model="comment"
	data-article_id="2545" >
		Create
</button>
```

Usually you have a list of entries and you would like the new item appended to the list. We have to define the template for new entries and reference the list.

Make a directory `resources/views/ajaxable/yourModels` where `yourModels` is the camelCase plural of you actual model. Inside the directory create file `yourModel.blade.php` - a single row of model (for your list of models). That will receive `$yourModel`. This will be ajaxed to you upon creation and added to the list of items.

```html
<li>{{$tag->name}}
```

You can now refrence the list in the creator - specify the selector in `data-ajaxable-list`.
```html
<ul id="tag-list">
	<li>Existing tag 1
	<li>Existing tag 2
<button
	class="ajaxable-creator"
	data-model="tag"
	data-ajaxable-list="#tag-list" >
		Create
</button>
```

The above example would append created html (using `ajaxable.tags.tag` view) to `#tag-list`, scroll it into view and add class `ajaxable-highlight` for 1.5 seconds (for highlighting purposes).

#### Specifying attribute values for new models

In many cases you also want the user to specify some attributes for the new entry. Make a `.ajaxable-new-attribute` input and specify `data-key` and `data-creator` attributes. On change the mentioned creator will get the data (specified by `data-key`) added or updated according to value of this input.

```html
Tag name:
<input
	class="ajaxable-new-attribute"
	data-key="name"
	data-creator="#theCreatorInThisExample" >
<button
	class="ajaxable-creator"
	id="theCreatorInThisExample"
	data-model="tag" >
		Create
</button>
```

In case of validation error, user will get alerts. If you want to display alerts neatly instead, wrap input in `.form-group`:
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


### Reordering and hiding

To use these functions, you should add `hide` (boolean or int) and `order` (integer) columns respectively to the database table. This will allow you to use some handy scopes.
```php
$visibleArticles = Article::active()->get();

$orderedArticles = Article::ordered()->get();

$visibleAndOrderedArticles = Article::items()->get();
```

Inside the `resources/views/ajaxable/yourModels` directory create a `list.blade.php` - list of models that will receive `$yourModels` and whatever else you supply through `YourModel::getDataForList()`. Usually this file is simply `@each('ajaxable.yourModels.yourModel', $yourModels, 'yourModel')`.

To reorder a list, add two buttons to each row.
```html
<table>
	<tbody>
		<tr> 
			<td> Tag 1
			<td> <button 
				class="ajaxable-control" 
				data-model="tag"
				data-action="up"
				data-id="1" >
					UP
				</button>
			<td><button 
				class="ajaxable-control" 
				data-model="tag" 
				data-action="down"
				data-id="1" >
					DOWN
				</button>
</table>
```

User will receive an updated list (prepared according to your `ajaxable.tags.list` view). The new list will replace the `.ajaxable-list` that you have wrapped this in. For specific cases you can specify selector for the list in `data-ajaxable-list`.

To change visibility of an item, make a `.ajaxable-control` with `toggle`, `show` or `hide` action. We do it like this:
```html
<button
	class="ajaxable-control" 
	data-model="tag" 
	data-action="toggle"
	data-id="123">
		HIDE
</button>
<button
	class="ajaxable-control hidden" 
	data-model="tag" 
	data-action="toggle"
	data-id="123">
		SHOW
</button>
```

The toggling action also toggles the visibility (the `hidden` and `d-none` classes) on these buttons so they swap as visibility is changed.


## Advanced usage

### Doing the JavaScript side yourself

You can call all the stuff using HTTP calls and implementing JavaScript yourself if you wish so!
```javascript
// a jQuery example
$.post(
	"{{route('ajaxable.create')}}",
	{model: 'article'},
	function(response)
	{
		if (1 == response['success'])
			console.log(response['row']);
			// Received html from ajaxable.yourModels.yourModel view
	}
);
```

#### Available HTTP calls:
Route | Data | Response on success
---|---|---
`ajaxable.create` | `{model: 'yourModel'}` | HTML for model's row. *Note: request may also include other key: value pairs that you want to set.*
`ajaxable.update` | `{model: 'yourModel', id: 12345, key: 'field_name', val: 'value'};` | `['success' => 1]`
`ajaxable.updateOrCreate` | `{model: 'yourModel', ':whereKey': 'whereVal', key: 'value'};` | `['success' => 1]` *Note: supply all wheres prefixing key with a colon (':name': 'john') and the attributes to be set as 'key': 'value'.*
`ajaxable.delete` | `{model: 'yourModel', id: 12345}` | `['success' => 1]`
`ajaxable.putFile` | `{model: 'yourModel', id: 12345, key: 'fileKey', fileModel: 'relation', file: file }` | Local path to file. *Note: tries to mass-assign: `$yourModel->relation()->create(['name' => originalName, 'path' => storedFilePath])`. If no `relation` supplied, stores the path in `$yourModel->fileKey` field.*
`ajaxable.removeFile` | `{model: 'yourModel', id: 12345, key: 'fileKey' }` | `['success' => 1]` *Note: if files have a separate model, use the delete action on that and rewrite it's `cleanUpForDeleting()` to handle file purge.*
`ajaxable.control` | `{model: 'yourModel', id: 12345, action: 'up'}` or `{model: 'yourModel', id: 12345, action: 'down'}` | HTML for list with updated order.
`ajaxable.control` | `{ model: 'yourModel', id: 12345, action: anotherAction}` | See notes below
`ajaxable.control` | `{ model: 'yourModel', id: 12345, action: anotherAction, parameters: myParams}` | See notes below

The `ajaxable.control` will execute the named `action` on the model supplying `parameters` if any are given and return whatever the method returns. In case it completes, but returns `null`, you will get `['success' => 1]`.

Out of the box the `up`, `down` (for ordering), `hide`, `show`, `toggle` (for visibility) actions are supported. You could also call some of the previous actions (like `delete`) using this to skip some validation and preparations (not advised unless you know what you're doing).

Other actions that Ajaxable doesn't implement will be forbidden so this wouldn't call random methods. To allow any, the model must override `isAllowedTo($action)` method. Default is this:

```php
public function isAllowedTo($action)
{
	$allowedActionsForAuthorized = [ 'create', 'update', 'updateOrCreate', 'delete', 'up', 'down', 'hide', 'show', 'toggle', 'putFile', 'removeFile', ];
	
	if (in_array($action, $allowedActionsForAuthorized))
		return \Auth::check();
	
	return false;
}
```

### Custom handling on the PHP side

Using the Ajaxable trait will carry over a bunch of methods that you can override for more specific needs. To ensure compatibility with our controller, you should make sure to implement our interface:
```php
use Illuminate\Database\Eloquent\Model;
use GlaivePro\Ajaxable\AjaxableInterface;
use GlaivePro\Ajaxable\Traits\Ajaxable;

class Article extends Model implements AjaxableInterface
{
    use Ajaxable;
	
	// Ajaxable checks if the model is used before deleting. 
	// If you want to forbid deleting an article that has other articles linked, you can do this:
	
	public function isUsed()
	{
		return $this->linkedArticles()->count();
	}
	
	// And let the articles be ordered within a section not with a global ordering
	public function listNeighbours()
	{
		return $this->section->articles();  // Return the query builder here!
	}
}
```

#### List of methods
Method | Description | Default
---|---|---
`isUsed()` | If the object is being used (forbidden to delete). | `false`
`cleanUpForDeleting()` | Called before deleting. | `//`
`isAllowedTo($action)` | Test if an `$action` is allowed. | `Auth::check()`
`validateForCreation($request)` | Validate data for creation. | Validate data using `$this->validationRulesForCreation` property if it's set.
`prepareForCreation($request)` | Called before saving newly created model. | `//`
`prepareUpdateOrCreate($request)` | Called before saving updated or created model. Return false to prevent saving. | `//`
`validate($request)` | Validate data for update. | Validate data using `$this->validationRules` property if it's set.
`getDataForList()` | Supply additional data for list view. | `return [];`
`show()` | Make object visible. | Sets `$object->hide` to `false`.
`hide()` | Make object hidden. | Sets `$object->hide` to `true`.
`toggle()` | Toggle visibility. | Checks `$object->hide` and calls one of the above.
`listNeighbours()` | Query to list of item and it's neighbours. | `get_class($this)::query()`
`scopeOrdered($query)` | Scope that orders. | `$query->orderBy('order')`
`up()` | Move object up in ordering. | Swap the order with above and clean up orderings for all the family.
`down()` | Move object down in ordering. | Swap the order with below and clean up orderings for all the family.
`putFile($file, $relation, $fileKey)` | Save a file. | Puts file in `ajaxable` directory, writes a related file model or field (depending on request) and responds with path.
`removeFile($fileKey)` | Remove a file. | Deletes the file and clears `fileKey` field.

You can also override `scopeActive`, `scopeOrdered` and `scopeItems` if you feel like it.

The HTTP response is usually handled in our controller, but if you want to override them, create a `respondAjaxableList()` and/or `respondAjaxableObject()` method.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email juris@glaive.pro instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-packagist]: https://packagist.org/packages/GlaivePro/Ajaxable
[link-author]: https://github.com/tontonsb
[link-downloads]: https://packagist.org/packages/GlaivePro/Ajaxable

[ico-version]: https://img.shields.io/packagist/v/:vendor/:package_name.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/:vendor/:package_name.svg?style=flat-square

# Ajaxable

Ajaxable is a Laravel package that allows you to control models through ajax calls without bothering you on the PHP side. You still got to make the views however.

## Install

Via Composer.
``` bash
$ composer require glaivepro/ajaxable
```

## Usage

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

Make a directory `resources/views/ajaxable/yourModels` where `yourModels` is the camelCase plural of you actual model. Inside this directory create two files:

1) `yourModel.blade.php` - a single row of model that will receive `$yourModel`.
2) `list.blade.php` - list of models that will receive `$yourModels` and whatever else you supply through `YourModel::getDataForList()`. Usually this file is simply `@each('ajaxable.yourModels.yourModel', $yourModels, 'yourModel')`.

Now you can manage the model with HTTP calls.
```javascript
// a jQuery example
$.post({
	"{{route('ajaxable.create')}}",
	data: {model: 'article'},
	function(response)
	{
		if (1 == response['success'])
			console.log(response['row']);
			// Received html from ajaxable.yourModel view
	}
});
```

If you want to use the visibility and ordering functionality, add `hide` (boolean or int) and `order` (integer) columns respectively to the database table. This will allow you to use some handy scopes.
```php
$visibleArticles = Article::active()->get();

$orderedArticles = Article::ordered()->get();

$visibleAndOrderedArticles = Article::items()->get();
```

### Available HTTP calls:
Route | Data | Response
---|---|---
`ajaxable.list` | `{model: 'yourModel',
					id: 12345}` | HTML for list of models
`ajaxable.create` | `{model: 'yourModel'}` | HTML for model`s row. *Note: request may also include other key: value pairs that you want to set.*
`ajaxable.update` | `{ model: 'yourModel',
					   id: 12345,
					   key: 'field_name',
					   val: 'value'};` | `['success' => 1]`
`ajaxable.delete` | `{model: 'yourModel', 
					  id: 12345}` | `['success' => 1]`
`ajaxable.putFile` | `{model: 'yourModel', 
					  id: 12345,
					  key: 'fileKey',
					  fileModel: 'relation',
					  file: file }` | Local path to file. *Note: tries to mass-assign: `$yourModel->relation()->create(['name' => originalName, 'path' => storedFilePath])`. If no `relation` supplied, stores the path in `$yourModel->fileKey` field.*
`ajaxable.removeFile` | `{	model: 'yourModel', 
							id: 12345,
							key: 'fileKey' }` | `['success' => 1]` *Note: if files have a separate model, use the delete action on that and rewrite it's `cleanUpForDeleting()` to handle file purge.*
`ajaxable.control` | `{model: 'yourModel',
					   id: 12345,
					   action: 'up'}` or `{model: 'yourModel',
					   id: 12345,
					   action: 'down'}` | HTML for list with updated order.
`ajaxable.control` | `{ model: 'yourModel',
						id: 12345,
						action: anotherAction}` | See notes below
`ajaxable.control` | `{ model: 'yourModel',
					    id: 12345,
						action: anotherAction,
						parameters: myParams}` | See notes below

The `ajaxable.control` will execute the named `action` on the model supplying `parameters` if any are given and return whatever the method returns. In case it completes, but returns `null`, you will get `['success' => 1]`.

Out of the box the `up`, `down` (for ordering), `hide`, `show`, `toggle` (for visibility) actions are supported. You could also call some of the previous actions (like `delete`) using this to skip some validation and preparations (not advised unless you know what you're doing).

Other actions that Ajaxable doesn't implement will be forbidden so this wouldn't call random methods. To allow any, the model must override `isAllowedTo($action)` method. Default is this:

```php
public function isAllowedTo($action)
{
	$allowedActionsForAuthorized = [ 'create', 'update', 'delete', 'up', 'down', 'hide', 'show', 'toggle', 'putFile', 'removeFile', ];
	
	if (in_array($action, $allowedActionsForAuthorized))
		return \Auth::check();
	
	return false;
}
```

## Customisation

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

### List of methods
Method | Description | Default
---|---|---
`isUsed()` | If the object is being used (forbidden to delete). | `false`
`cleanUpForDeleting()` | Called before deleting. | `//`
`checkPermission($action)` | Test if an `$action` is allowed. | `Auth::check()`
`validateForCreation($request)` | Validate data for creation. | Validate data using `$this->validationRulesForCreation` property if it's set.
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

## Javascript

This package also includes javascript library to take care of some requests if you format the html appropriately. There are some dependancies that you should boilerplate for:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- your html - see examples in the following subsections -->

<!-- provide jQuery here please! -->

<!-- finally include our library -->
@include('ajaxable:javascript')
```

Now let's focus on the uses!

### Editor
Changing the value in `.ajaxable-edit` will update the value of an attribute.

This is everything you need to set/edit your tag's name field (also works with checkboxes, selects):
```html
<input
	class="ajaxable-edit"
	data-model="tag"
	data-id="12345"
	data-key="name" >
```

In case of a validation error user will get an alert. If you want to display it on html, wrap the input in `.form-group`. Error will be added in `span.error-block.help-block` and class `has-error` will be added to `.form-group`.


### Creator
Click on `.ajaxable-creator` will make a request to create an object. Make sure to specify `data-model` attribute!

You may also supply any additional `data-*` attributes to be set (if columns exist) on the new model.

```html
<button
	class="ajaxable-creator"
	data-model="comment"
	data-article_id="2545" >
		Create
</button>
```

For user supplied content make a `.ajaxable-new-attribute` input and specify `data-key` and `data-creator` attributes. On change the mentioned creator will get the data (specified by `data-key`) added or updated according to value of this input.

```html
Tag name:
<input
	class="ajaxable-new-attribute"
	data-key="name"
	data-creator="theCreatorInThisExample" >
<button
	class="ajaxable-creator"
	id="theCreatorInThisExample"
	data-model="tag" >
		Create
</button>
```

You probably want the new row appended to a list. Specify the list on button and you will get it done! Just make sure to create a proper view.
```html
<ul id="tag-list">
	<li>Existing tag 1
	<li>Existing tag 2
<button
	class="ajaxable-creator"
	id="theCreatorInThisExample"
	data-model="tag"
	data-ajaxable-list="tag-list" >
		Create
</button>
```

The above example would append created html (using `ajaxable.tags.tag` view) to `.tag-list`, scroll it into view and add class `info` for 1.5 seconds (for highlighting purposes).

In case of validation error, user will get alerts. If you want to display alerts neatly instead, wrap input in `.form-group`:
```html
Tag name:
<div class="form-group"
	<input
		class="ajaxable-new-attribute"
		data-key="name"
		data-creator="theCreatorInThisExample" >
</div>
```
This `.form-group` will then get class `has-error` added and `span.error-block.help-block` appended with the message.

### Deleter
Click on `.ajaxable-delete` will remove an item specified in `data-*` attributes. HTML will also get removed if you wrap it (including the button) in `.ajaxable-row`.

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

### Control
To reorder a list, add two buttons to each row.
```html
<table>
	<tbody id="tag-list">
		<tr> 
			<td> Tag 1
			<td> <button 
					class="ajaxable-control" 
					data-model="tag"
					data-ajaxable-list="tag-list"
					data-action="up"
					data-id="1" >
						UP
				</button>
			<td>
				<button 
					class="ajaxable-control" 
					data-model="tag" 
					data-ajaxable-list="tag-list"
					data-action="down"
					data-id="1" >
						DOWN
				</button>
</table>
```

User will receive an updated list (prepared according to your `ajaxable.tags.list` view). It will get inserted in the list specified in `data-ajaxable-list`.

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
```html

The toggling action also toggles the visibility on these buttons so they swap as visibility is changed.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email juris@glaive.pro instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-packagist]: https://packagist.org/packages/GlaivePro/Ajaxable
[link-author]: https://github.com/tontonsb
[link-contributors]: ../../contributors

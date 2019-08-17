# HTTP Interface

HTTP backend is the core of this library. You [have added](setup) a trait to your model and now you can interact with that model. Without making any routes or controllers yourself.

* TOC
{:toc}

## Requests

The HTTP interface supports appropriate HTTP verbs but does not require them. You can use the proper method, you could spoof it like the Laravel supports the spoofing or you could just use POST and GET for everything.

The requests that change something (post, put, patch, delete) will also require the CSRF token if your app uses CSRF protection (on by default in Laravel).


### The CRUD

Verb | Route | Required parameters | Optional parameters | Response
-----|-------|---------------------|---------------------|---------
POST | `ajaxable.create` | `model` | `attributes`, `view` | Model
GET | `ajaxable.retrieve` | `model` | `view` | Model
PATCH/POST | `ajaxable.update` | `model`, `id`, `key`, `value` | `view` |  Model
DELETE/POST | `ajaxable.delete` | `model`, `id`  | | Confirmation


### Some extras

Verb | Route | Required parameters | Optional parameters | Response
-----|-------|---------------------|---------------------|----------
PUT/POST | `ajaxable.updateOrCreate` | `model` | `attributes`,  `values`, `view` | Model
GET | `ajaxable.list` | `model` | `wheres`, `scopes`, `view` | Models
POST | `ajaxable.control` | `model`, `id`, `action` | `arguments` | You decide


### Media

Working with files if your model happens to use spatie/laravel-medialibrary.

Verb | Route | Required parameters | Optional parameters | Response
-----|-------|---------------------|---------------------|----------
POST | `ajaxable.addMedia` | `model`, `id`, `media` | `collection`, `name`, `filename`, `properties` | Medium and URL
GET | `ajaxable.getMedia` | `model`, `id` | `collection` | Media and URLs
DELETE/POST | `ajaxable.deleteMedia` | `model`, `id`, `media_id` | | Confirmation


## Responses 

The responses are formed according to [JSend standard](https://github.com/omniti-labs/jsend) and accompanied by the appropriate status code.

A response will contain `status` and either `data` or `message` with latter being used for `status: error`.

The data will contain a model in JSON unless a delete was performed. Optionally you may also require a rendered HTML of the model.

### Rendered HTML

Most requests can ask for a rendered view in the response. Those are the ones with an optional `view` parameter above. You just have to specify the view name as you would in the Laravel's `view()` helper or `View` facade.

The view will receive your model in a variable named after the model class basename, in lowercase. It means that when rendering `\App\Project` your view would receive `$project`.

An exception to this rule is the `ajaxable.list` which receives the list in a pluralized name. So view that renders a list of `\App\Project` would receive `$projects`.


## Deeper dive and examples

### Create

Creates a new record and returns it. You can optionally supply the initial values.

#### Simple request and response

```js
// POST request to ajaxable/create
{
	model: "App\Article"
}

// response, code 201
{
	status: "success",
	data: {
		model: {
			id: 55,
			title: "",
			created_at: "2019-08-17 04:33:12",
			updated_at: "2019-08-17 04:33:12"
		}
	}
}
```

#### Request with intial values and response

```js
// POST request to ajaxable/create
{
	model: "App\Tag",
	attributes: {
		name: 'Corgi'
	}
}

// response, code 201
{
	status: "success",
	data: {
		model: {
			id: 4,
			name: "Corgi",
			created_at: "2019-08-17 04:33:12",
			updated_at: "2019-08-17 04:33:12"
		}
	}
}
```

#### Request with intial values and requiring a rendered view

```js
// POST request to ajaxable/create
{
	model: "App\Comment",
	attributes: {
		text: "i aren't think that",
		article_id: 55
	},
	view: 'articles.comment'
}

// response, code 201
{
	status: "success",
	data: {
		model: {
			id: 40,
			text: "Corgi",
			article_id: 55,
			user_id: 34,
			created_at: "2019-08-17 04:34:02",
			updated_at: "2019-08-17 04:34:02"
		},
		view: "<div><h3>WorldUnderNews</h3><p>i aren't think that</p></div>"
	}
}
```

#### Bad requests

```js
// POST request to ajaxable/create
{
	model: "App\User"
}

// response, code 500
{
	status: "fail",
	message: "Ajaxable not implemented on App\User"
}
```

```js
// POST request to ajaxable/create
{
	model: "App\Comment"
}

// response, code 403
{
	status: "fail",
	message: "Action not allowed"
}
```

```js
// POST request to ajaxable/create
{
	model: "App\Comment"
}

// response, code 400
{
	status: "error",
	data: {
		article_id: "article_id is required",
		text: "text is required"
	}
}
```


### Retrieve a.k.a. Read

Returns a record, optionally rendered as HTML through one of your views.

```js
// GET request to ajaxable/retrieve
{
	model: "App\Badge",
	id: 15
}

// response, code 200
{
	status: "success",
	data: {
		model: {
			id: 15,
			title: "gawd",
			icon: "ikea-idea"
		}
	}
}
```

```js
// GET request to ajaxable/retrieve
{
	model: "App\Badge",
	id: 15,
	view: 'elements.badge'
}

// response, code 200
{
	status: "success",
	data: {
		model: {
			id: 15,
			title: "gawd",
			icon: "ikea-idea"
		},
		view: "<div class=badge><span class='icon icon-ikea-idea'></span> gawd</div>"
	}
}
```

```js
// GET request to ajaxable/retrieve
{
	model: "App\Badge",
	id: 16
}

// response, code 404
{
	status: "error",
	message: "Model not found"
}
```


### Update

Updates a single field and returns the model, optionally rendered to HTML.

> 💡 The HTML responses and errors can be seen among Create and Retrieve examples.

```js
// PATCH or POST request to ajaxable/update
{
	model: "App\User",
	id: 13,
	key: "email",
	value: "rype@example.com"
}

// response, code 200
{
	status: "success",
	data: {
		model: {
			id: 13,
			name: "Rype S. Rype",
			email: "rype@example.com",
			created_at: "2019-08-12 23:21:58",
			updated_at: "2019-08-17 05:13:57"
		}
	}
}
```

### Delete

Removes a record.

> 💡 The errors can be seen among Create and Retrieve examples.

```js
// DELETE or POST request to ajaxable/delete
{
	model: "App\User",
	id: 14
}

// response, code 200
{
	status: "success",
	data: null
}
```

### Update or Create

Updates a fields no model or creates a model and sets the field values. Returns the model, optionally rendered to HTML.

> 💡 The HTML responses and errors can be seen among Create and Retrieve examples.

```js
// PUT or POST request to ajaxable/update-or-create
{
	model: "App\Availability",
	attributes: {
		date: "2019-08-17",
		user_id: "666"
	},
	values: {
		status: "vacation"
	}
}

// response, code 200
{
	status: "success",
	data: {
		model: {
			id: 13234,
			user_id: "666",
			status: "vacation",
			created_at: "2019-08-17 05:27:32",
			updated_at: "2019-08-17 05:27:3"
		}
	}
}
```

### List

Retrieves a collection of models. Optionally filtered or scoped. Optionally rendered via a view.

> 💡 The HTML responses and errors can be seen among Create and Retrieve examples.

```js
// GET request to ajaxable/list
{
	model: "App\Vote"
}

// response, code 200
{
	status: "success",
	data: {
		collection: [
			{
				id: 1,
				vote: "Rarely"
			},
			{
				id: 2,
				vote: "Almost never"
			},
			{
				id: 3,
				vote: "Never"
			}
		]
	}
}
```

#### Filtered list

```js
// GET request to ajaxable/list
{
	model: "App\Comment"
	wheres: [
		article_id: 21
	]
}

// response, code 200
{
	status: "success",
	data: {
		collection: [
			{
				id: 33,
				text: "Papaya socks"
			},
			{
				id: 37,
				text: "Papaya socks"
			},
			{
				id: 38,
				vote: "new pasta?"
			}
		]
	}
}
```

#### Scoped and rendered list

```js
// GET request to ajaxable/list
{
	model: "App\User"
	scopes: [
		"online",
		"rank:commoner"
	]
}

// response, code 200
{
	status: "success",
	data: {
		collection: [
			{
				id: 1,
				name: "EdgeBrowser",
				email: "mse@example.com"
			}
		],
		view: "<aside><h3>Onliners</h3><ul><li>EdgeBrowser</ul></aside>"
	}
}
```

### Control

Invoke a method on your model.

> ⚠️ These actions must be explicitly permitted on your model, otherwise they are blocked.

> 💡 The errors can be seen among Create and Retrieve examples.

```js
// GET request to ajaxable/list
{
	model: "App\User"
	id: 20,
	action: "invite"
}

// response, code 200
{
	status: "success",
	data: {
		response: "anything that the `invite` method returned"
	}
}
```

#### Control with parameters

```js
// GET request to ajaxable/list
{
	model: "App\User"
	id: 20,
	action: "block",
	parameters: {
		duration: "3 days"
	}
}

// response, code 200
{
	status: "success",
	data: {
		response: "we've blocked the fellow, boss"
	}
}
```

### Add media

> ⚠️ The media routes will only work if your model uses [Laravel Medialibrary](https://docs.spatie.be/laravel-medialibrary).

Adds media (I mean a file) to a model.


> 💡 The HTML responses and errors can be seen among Create and Retrieve examples.

```js
// POST request to ajaxable/get-media
{
	model: "App\Article",
	id: 21,
	media: /** a file **/
}

// response, code 201
{
	status: "success",
	data: {
		medium: {
			/** everything of the media model **/
		},
		url: "https://example.com/storage/34/urfile.bat"
	}
}
```

### Get media

Retrieves media.

```js
// POST request to ajaxable/get-media
{
	model: "App\Article",
	id: 21,
	media: /** a file **/
}

// response, code 200
{
	status: "success",
	data: {
		media: [
			{ /** a media model **/ },
			{ /** another media model **/ }
		],
		urls: [
			"https://example.com/storage/34/urfile.bat",
			"https://example.com/storage/36/myfile.bat"
		]
	}
}
```


### Delete media

Removes media.

```js
// POST request to ajaxable/delete-media
{
	model: "App\Article",
	id: 21,
	media_id: 36
}

// response, code 200
{
	status: "success",
	data: null
}
```

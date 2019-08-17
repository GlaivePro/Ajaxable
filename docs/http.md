# HTTP Interface

The following routes are provided by this package

Route | Required parameters | Optional parameters | Response
---|---|---|---
`ajaxable.create` | `model` | Initial values (key:value pairs in `attributes`) | Model in JSON, optional HMTL.
`ajaxable.update` | `model`, `id`, `key`, `value` | | Model in JSON, optional HTML
`ajaxable.delete` | `model`, `id`  | | Confirmation only
`ajaxable.updateOrCreate` | `model` | Constraints (key:value pairs in `wheres`) and values (key:value pairs in `attributes`) | Model in JSON, optional HTML
`ajaxable.control` | `model`, `id`, `action` | `parameters` - supply whatever to be passed to called action. | Whatever you decide to return
`ajaxable.addMedia` | `model`, `id`, `media` | `collection`, `name` | Media object and URL
`ajaxable.deleteMedia` | `model`, `id`, `media_id` | | Confirmation only



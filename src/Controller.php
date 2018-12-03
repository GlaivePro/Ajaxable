<?php

namespace GlaivePro\Ajaxable;

use Illuminate\Http\Request;

class Controller
{	
	/**
	 * Create a model
	 * 
	 * Include contents (key:value pairs) in `$request->attributes`
	 * 
	 * @param Request $request Must contain `model`
	 * @return mixed
	 */
	public function create(Request $request)
	{
		$object = $this->getObject(__FUNCTION__, $request);

		$object->fill($request->input('attributes') ?? []);
		
		$success = $object->save();
		
		// Reacquire from DB to obtain default field values
		$object->refresh();
		
		return $object->respondAfter(__FUNCTION__, $success);
	}
	
	/**
	 * Retrieve a model
	 * 
	 * @param Request $request  Must contain `model` and `id`
	 * @return mixed
	 */
	public function retrieve(Request $request)
	{
		$object = $this->getObject(__FUNCTION__, $request);
		
		return $object->respondAfter(__FUNCTION__);
	}
	
	/**
	 * Update a model
	 * 
	 * @param Request $request Must contain `model`, `id`, `key` and `value`
	 * @return mixed
	 */
	public function update(Request $request)
	{
		$object = $this->getObject(__FUNCTION__, $request);
		
		$key = $request->key;
		$object->$key = $request->value;
		
		$success = $object->save();
		
		return $object->respondAfter(__FUNCTION__, $success);
	}
	
	/**
	 * Delete a model
	 * 
	 * @param Request $request  Must contain `model` and `id`
	 * @return mixed
	 */
	public function delete(Request $request)
	{
		$object = $this->getObject(__FUNCTION__, $request);
		
		$success = $object->delete();
		
		return $object->respondAfter(__FUNCTION__, $success);
	}

	/**
	 * Update existing model or create a new one
	 * 
	 * Include constraints (key:value pairs) in `$request->wheres`
	 * Include updatables (key:value pairs) in `$request->attributes`
	 * 
	 * @param Request $request Must contain `model` and `id`
	 * @return mixed
	 */
	public function updateOrCreate(Request $request)
	{
		$object = $this->getObject(__FUNCTION__, $request);
		
		$object->fill($request->input('attributes') ?? []);
		
		$success = $object->save();

		return $object->respondAfter(__FUNCTION__, $success);
	}

	/**
	 * Get a collection of models
	 * 
	 * Pass any constraints (key:value pairs) in `$request->wheres`
	 * Pass any scopes in `$request->scopes`
	 * 
	 * @param Request $request Must contain `model`
	 * @return mixed
	 */
	public function list(Request $request)
	{
		$object = $this->getObject(__FUNCTION__, $request);

		$query = $object::query();

		if ($request->has('wheres'))
			$query->where($request->wheres);

		if ($request->has('scopes'))
			foreach ($request->scopes as $scope)
			{
				if ($colonPos = strpos($scope, ':')) // intentional assignment
				{
					[$scope, $parameters] = explode(':', $scope, 2);
					$parameters = explode(',', $parameters, 2);

					$query->$scope(...$parameters);
				}
				else
					$query->$scope();
			}

		return $object->respondAfter(__FUNCTION__, $query->get());
	}
	
	/**
	 * Invoke an action on model
	 * 
	 * Pass any parameter or parameters in `$request->parameters`
	 * If `parameters` is an array it will be unpacked
	 * So wrap array in array if you want to pass just a single array
	 * 
	 * @param Request $request Must contain `model`, `id` and `action`
	 * @return mixed
	 */
	public function control(Request $request)
	{
		$object = $this->getObject(__FUNCTION__, $request);
		$action = $request->action;
		
		if ($request->has('parameters'))
		{
			$parameters = $request->parameters;

			if (is_array($parameters))
				$response = $object->$action(...$parameters);
			else
				$response = $object->$action($parameters);
		}
		else
			$response = $object->$action();
		
		return $object->respondAfter($action, $success);
	}

	
	/**
	 * Add media to model
	 * 
	 * Specify `collection`, `name`, `filename`, `properties` if needed
	 * 
	 * @param Request $request Must contain `model`, `id` and `media`
	 * @return mixed
	 */
	public function addMedia(Request $request)
	{
		$object = $this->getObject(__FUNCTION__, $request);
	
		$fileAdder = $object->addMediaFromRequest('media');

		if ($request->has('name'))
			$fileAdder = $fileAdder->setName($request->name);

		if ($request->has('filename'))
			$fileAdder = $fileAdder->setFileName($request->filename);

		if ($request->has('properties'))
			$fileAdder = $fileAdder->withCustomProperties($request->properties);

		if ($request->has('collection'))
			$media = $fileAdder->toMediaCollection($request->collection);
		else
			$media = $fileAdder->toMediaCollection();

		return $object->respondAfter(__FUNCTION__, $media);
	}
	
	/**
	 * Get media of a model
	 * 
	 * Specify `collection` if needed
	 * 
	 * @param Request $request 
	 * @return type
	 */
	public function getMedia(Request $request)
	{
		$object = $this->getObject(__FUNCTION__, $request);

		if ($request->has('collection'))
			$media = $object->getMedia('collection');
		else
			$media = $object->getMedia();
		
		return $object->respondAfter(__FUNCTION__, $media);
	}
	
	/***************/
	/*   HELPERS   */
	/***************/
	private function getObject(string $method, Request $request)
	{
		$this->verifyRequest($method, $request);

		$class = $request->model;
		
		if (in_array($method, ['retrieve', 'update', 'delete', 'control', 'addMedia', 'getMedia']))
			$object = $class::findOrFail($request->id);
		else if ('updateOrCreate' == $method)
			$object = $class::firstOrNew($request->wheres);
		else
			$object = new $class;

		$this->verifyObject($object);

		if (!$object->allowAjaxableTo($method))
			return response('Action not allowed', 403);

		return $object;
	}

	private $requiredFields = [
		'create' => ['model'],
		'retrieve' => ['model', 'id'],
		'update' => ['model', 'id', 'key', 'value'],
		'delete' => ['model', 'id'],

		'updateOrCreate' => ['model', 'id'],
		'list' => ['model'],
		'control' => ['model', 'id', 'action'],

		'addMedia' => ['model', 'id', 'media'],
		'getMedia' => ['model', 'id'],
	];
	private function verifyRequest(string $method, Request $request)
	{
		abort_unless(isset($this->requiredFields[$method]), 500, 'Ajaxable crashed.');

		foreach ($this->requiredFields[$method] as $field)
			abort_unless($request->has($field), 400, 'Ajaxable request to '.$method.' must include '.$field.'.');
	}
	
	private $requiredMethods = [
		'allowAjaxableTo',
		'respondAfter',
	];
	private function verifyObject($object)
	{
		foreach ($this->requiredMethods as $method)
			abort_unless(is_callable([$object, $method]), 501, 'Ajaxable interface is not implemented.');
	}
	
	private function verifyObjectForMedia($object)
	{
		$this->verifyObject($object);
		abort_unless($object instanceof \Spatie\MediaLibrary\HasMedia\HasMedia, 501, 'Medialibrary interface is not implemented.');
	}
}
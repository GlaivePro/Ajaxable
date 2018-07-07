<?php

namespace GlaivePro\Ajaxable;

use Illuminate\Http\Request;

class Controller
{	
	public function create(Request $request)
	{
		$class = $this->getClass($request);
		$model = $request->model;
		
		$object = new $class;
		if (!$object->isAllowedTo('create'))
			return response('Action not allowed', 403);
		
		if (\Schema::hasColumn($object->getTable(), 'order'))
		{
			$lastItem = $class::ordered()->get()->last();
			$order = 1;
			if ($lastItem)
				$order = $lastItem->order + 1;
			
			$object->order = $order;
		}
		
		$object->validateForCreation($request);
		
		foreach ($request->all() as $key => $value)
			if (\Schema::hasColumn($object->getTable(), $key))
				$object->$key = $value;
		
		if (false === $object->prepareForCreation($request))
			return 0;
		
		$object->save();
		$object = $object->refresh();
		
		if (\Schema::hasColumn($object->getTable(), 'order'))
			$object->tidyUp();
		
		return $object->respondRow();
	}
	
	public function update(Request $request)
	{
		$object = $this->getObject($request);
		if (!$object->isAllowedTo('update'))
			return response('Action not allowed', 403);
		
		$object->validate($request);
		
		$key = $request->key;
		$object->$key = $request->val ? $request->val : null;
		
		$object->save();
		
		return $this->respondOK();
	}

	public function updateOrCreate(Request $request)
	{
		$class = $this->getClass($request);

		$wheres = [];
		$attributes = [];

		foreach ($request->except(['model']) as $key => $value)
			if (':' == substr($key, 0, 1))
				$wheres[substr($key, 1)] = $value;
			else 
				$attributes[$key] = $value;

		$object = $class::firstOrNew($wheres);
		foreach($attributes as $key => $value)
			$object->$key = $value;
		
		if (!$object->isAllowedTo('updateOrCreate'))
			return response('Action not allowed', 403);

		if (false === $object->prepareUpdateOrCreate($request))
			return response('Action not allowed', 400);

		$object->save();

		return $this->respondOK();
	}
	
	public function delete(Request $request)
	{
		$object = $this->getObject($request);
		if (!$object->isAllowedTo('delete'))
			return response('Action not allowed', 403);
		
		if ($object->isUsed())
			return response('Object in use', 400);
		
		$object->cleanUpForDeleting();
		$object->delete();
		
		return $this->respondOK();
	}
	
	public function control(Request $request)
	{
		$object = $this->getObject($request);
		if (!$object->isAllowedTo($request->action))
			return response('Action not allowed', 403);
		
		if ($request->has('parameters'))
			$response = $object->{$request->action}($request->parameters);
		else
			$response = $object->{$request->action}();
		
		if (null !== $response)
			return $response;
		
		if (!in_array($request->action, ['up', 'down']))
			return $this->respondOK();
		
		return $this->respondList($request);
	}
	
	public function putFile(Request $request)
	{
		$object = $this->getObject($request);
		if (!$object->isAllowedTo('putFile'))
			return response('Action not allowed', 403);;
				
		$file = $object->putFile($request->file, $request->relation, $request->fileKey);
		
		if (false === $file)
			return response('Upload failed', 400);
	
		$response = [
			'success' => 1,
			'file' => $file,
		];
		
		return response()->json($response);
	}
	
	public function removeFile(Request $request)
	{
		$object = $this->getObject($request);
		if (!$object->isAllowedTo('removeFile'))
			return response('Action not allowed', 403);
		
		$object->removeFile($request->fileKey);
		
		return $this->respondOK();
	}
	
	/***************/
	/*   HELPERS   */
	/***************/
	
	private function getClass(Request $request)
	{
		return 'App\\'.studly_case($request->model);
	}
	
	private function getObject(Request $request)
	{
		$class = $this->getClass($request);
		
		return $class::findOrFail($request->id);
	}
	
	private function respondOK()
	{
		$response = [
			'success' => 1,
		];
		
		return response()->json($response);
	}
	
	private function respondList(Request $request)
	{
		$responseMethod = 'respondAjaxableList';
		
		$object = $this->getObject($request);
		if ($object)
			return $object->resopndList();
		
		$class = $this->getClass($request);
		
		return $class::respondStaticList();
	}
}
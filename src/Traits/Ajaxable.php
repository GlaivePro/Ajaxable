<?php

namespace GlaivePro\Ajaxable\Traits;

trait Ajaxable
{
	use HideableOrderable, Attachable;
	
	public function getRowViewAttribute()
	{
		$className = end(explode('\\', get_class($this)));
		return 'ajaxable.'.camel_case($className);
	}
	
	public function drawRow()
	{	
		$view = $this->rowView;
		if (!view()->exists($view))
			abort(500, 'View '.$this->rowView.' not found.');
		
		$data = [get_class($this) => $this];
		
		return view($view, $data)->render();
	}
	
	public function respondRow()
	{
		return response()->json([
			'success' => 1,
			'row' => $this->drawRow(),
		]);
	}
	
	public function drawList()
	{
		$view = $this->rowView;
		if (!view()->exists($view))
			abort(500, 'View '.$this->rowView.' not found.');
		
		$list = '';
		foreach ($this->listNeighbours()->ordered() as $item)
			$list .= $item->drawRow();
			
		return $list;
	}
	
	public function respondList()
	{
		return response()->json([
			'success' => 1,
			'row' => $this->drawList(),
		]);
	}
	
	public static function drawStaticList()
	{
		return '';
	}
	
	public static function respondStaticList()
	{
		return response()->json([
			'success' => 1,
			'row' => self::drawStaticList(),
		]);
	}
	
	public function isUsed()
	{
		return false;
	}
	
	public function cleanUpForDeleting()
	{
		//
	}
	
	public function isAllowedTo($action)
	{
		$allowedActionsForAuthorized = [ 'create', 'update', 'updateOrCreate', 'delete', 'up', 'down', 'hide', 'show', 'toggle', 'putFile', 'removeFile', ];
		
		if (in_array($action, $allowedActionsForAuthorized))
			return \Auth::check();
		
		return false;
	}
	
	public function validate($request)
	{
		if (!isset($this->validationRules))
			return;
		
		if (!isset($this->validationRules[$request->key]))
			return;
		
		$rules = [
			'val' => $this->validationRules[$request->key],
		];
		
		$request->validate($rules);
	}
	
	public function validateForCreation($request)
	{
		if (isset($this->validationRulesForCreation))
			$request->validate($this->validationRulesForCreation);
	}
	
	public function prepareForCreation($request)
	{
		//
	}

	public function prepareUpdateOrCreate($request)
	{
		//
	}
}
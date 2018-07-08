<?php

namespace GlaivePro\Ajaxable\Traits;

trait Ajaxable
{
	use HideableOrderable, Attachable;
	
	private function getRawClassName()
	{
		$explodedFullName = explode('\\', get_class($this));
		return camel_case(end($explodedFullName));
	}
	
	public function getRowViewAttribute()
	{
		return 'ajaxable.'.$this->getRawClassName();
	}
	
	public function drawRow()
	{	
		$view = $this->rowView;
		if (!view()->exists($view))
			abort(500, 'View '.$this->rowView.' not found.');
		
		$data = [$this->getRawClassName() => $this];
		
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
		foreach ($this->listNeighbours()->ordered()->get() as $item)
			$list .= $item->drawRow();
			
		return $list;
	}
	
	public function respondList()
	{
		return response()->json([
			'success' => 1,
			'list' => $this->drawList(),
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
			'list' => self::drawStaticList(),
		]);
	}
	
	public function isUsed()
	{
		return false;
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
}
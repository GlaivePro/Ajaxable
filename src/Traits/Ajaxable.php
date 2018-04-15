<?php

namespace GlaivePro\Ajaxable\Traits;

trait Ajaxable
{
	use HideableOrderable, Attachable;
	
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
	
	public function getDataForList()
	{
		return [];
	}
}
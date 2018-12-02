<?php

namespace GlaivePro\Ajaxable\Traits;

trait Ajaxable
{
	use AjaxableResponses, AjaxableHtml;

	/**
	 * Permission checking
	 * 
	 * @param string $action 
	 * @return boolean
	 */
	public function allowAjaxableTo(string $action) : bool
	{
		$allowedActionsForAuthorized = [
			'create', 
			'retrieve', 
			'update', 
			'delete', 
			'updateOrCreate', 
			'list', 
			'addMedia', 
			'getMedia'
		];
		
		if (in_array($action, $allowedActionsForAuthorized))
			return auth()->check();
		
		return false;
	}

	protected function getPlainClassName()
	{
		return camel_case(class_basename($this));
	}
}
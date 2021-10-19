<?php

namespace GlaivePro\Ajaxable\Traits;

trait Ajaxable
{
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
			'getMedia',
			'deleteMedia',
		];

		if (in_array($action, $allowedActionsForAuthorized))
			return auth()->check();

		return false;
	}
}

<?php

namespace GlaivePro\Ajaxable;

//!! Can implement all by using Ajaxable trait.
interface AjaxableInterface
{
	// Actions before creating or updating (checking submitted info and setting mandatory fields).
	public function allowAjaxableTo(string $action) : bool;

	// Actions after 
	public function respondAfter(string $action, $result);
}
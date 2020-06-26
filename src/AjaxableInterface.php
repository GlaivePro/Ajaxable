<?php

namespace GlaivePro\Ajaxable;

// Can implement all by using Ajaxable trait.
interface AjaxableInterface
{
	public function allowAjaxableTo(string $action) : bool;
}

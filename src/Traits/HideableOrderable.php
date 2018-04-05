<?php

namespace GlaivePro\Ajaxable\Traits;

trait HideableOrderable
{
	use Hideable, Orderable;
	
	public function scopeItems($query)
	{
		return $query->active()->ordered();
	}
}
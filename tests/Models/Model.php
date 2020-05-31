<?php

namespace GlaivePro\Ajaxable\Tests\Models;

class Model extends \Illuminate\Database\Eloquent\Model
{
	use \GlaivePro\Ajaxable\Traits\Ajaxable;

	public function allowAjaxableTo()
	{
		return true;
	}
}

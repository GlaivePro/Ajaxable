<?php

namespace GlaivePro\Ajaxable\Traits;

trait Hideable
{
	public function hide()
	{
		$this->hide = true;
		$this->save();
	}
	
	public function show()
	{
		$this->hide = false;
		$this->save();
	}
	
	public function toggle()
	{
		if ($this->hide)
			return $this->show();
		
		return $this->hide();
	}
	
	public function scopeActive($query)
	{
		return $query->where(function ($q) {
			$q->where('hide', '!=', true)->orWhereNull('hide');
		});
	}
}
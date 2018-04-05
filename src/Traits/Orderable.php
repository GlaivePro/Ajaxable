<?php

namespace GlaivePro\Ajaxable\Traits;

trait Orderable
{	
	public function scopeOrdered($query)
	{
		return $query->orderBy('order');
	}
	
	public function listNeighbours()
	{
		// This should probably be overriden when using the trait. This method should return a query for list of items among which should the item be ordered.
		return get_class($this)::query();
	}
	
	public function tidyUp()
	{
		$objects = $this->listNeighbours()->ordered()->get();
		
		$order = 1;
		foreach ($objects as $object)
		{
			$object->order = $order;
			$object->save();
			$order = $order + 1;
		}
	}
	
	public function up()
	{
		$oldOrder = $this->order;
		
		$swapItem = $this->listNeighbours()->where('order', $oldOrder-1)->first();
		
		if(!$swapItem)
		{
			$this->tidyUp();
			return;
		}
		
		$this->order = $oldOrder - 1;
		$swapItem->order = $oldOrder;
		
		$this->save();
		$swapItem->save();
	}
	
	public function down()
	{
		$oldOrder = $this->order;

		$swapItem = $this->listNeighbours()->where('order', $oldOrder+1)->first();
		
		if(!$swapItem)
		{
			$this->tidyUp();
			return;
		}
		
		$this->order = $oldOrder + 1;
		$swapItem->order = $oldOrder;
		
		$this->save();
		$swapItem->save();
	}
}
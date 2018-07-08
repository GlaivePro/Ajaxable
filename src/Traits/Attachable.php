<?php

namespace GlaivePro\Ajaxable\Traits;

use Storage;

trait Attachable
{
	public function putFile($file, $key)
	{
		if (!$file->isValid())
			return false;
	
		$name = $file->getClientOriginalName();
		$path = $file->store('ajaxable');
		
		if (!method_exists($this, $key))
		{
			$this->$key = $path;
			$this->save();
			
			return $path;
		}
		
		$file = $this->$key()->create([
			'name' => $name,
			'path' => $path,
		]);
		
		if ($this->$key() instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo)
		{
			$foreignKey = $this->$key()->getForeignKey();
			
			$this->$foreignKey = $file->id;
	
			$this->save();
		}
		
		return $path;
	}
	
	public function removeFile($key)
	{
		
		if (!method_exists($this, $key))
		{
			\Storage::delete($this->$key);
			
			$this->$fileKey = null;
			$this->save();
			
			return true;
		}
		
		$file = $this->$key;
		\Storage::delete($file->path);
		
		if ($file instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo)
		{
			$foreignKey = $file->getForeignKey();
			
			$this->$foreignKey = null;
	
			$this->save();
		}
			
		$file->delete();
	}
}
<?php

namespace GlaivePro\Ajaxable\Traits;

use Storage;

trait Attachable
{
	public function putFile($file, $relation, $fileKey)
	{
		if (!$file->isValid())
			return false;
		
		$name = $file->getClientOriginalName();
		$path = $file->store('ajaxable');
		
		if (!$relation)
		{
			$this->$fileKey = $path;
			$this->save();
			
			return $path;
		}
		
		$file = $this->$relation()->create([
			'name' => $name,
			'path' => $path,
		]);
		
		return $path;
	}
	
	public function removeFile($fileKey)
	{
		\Storage::delete($this->$fileKey);
		
		$this->$fileKey = null;
		$this->save();
		
		return true;
	}
}
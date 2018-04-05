<?php

namespace GlaivePro\Ajaxable;

//!! Can implement all by using Ajaxable trait.
interface AjaxableInterface
{
	// Tell if the object is being used (forbidden to delete).
	public function isUsed();
	
	// Actions before deleting.
	public function cleanUpForDeleting();
	
	// Actions before creating or updating (checking submitted info and setting mandatory fields).
	public function isAllowedTo($action);
	public function validate($request);
	public function validateForCreation($request);
	public function prepareForCreation($request);
	
	// Supply a keyed array if the list view requires additional information
	public function getDataForList();
	
	// Visibility functions. !! Can implement by using Hideable trait.
	public function show();
	public function hide();
	public function toggle();
	
	// Ordering functions. !! Can implement by using Orderable trait. Check if you need to override the listNeighbours() functions.
	public function scopeOrdered($query);
	public function up();
	public function down();
	
	// File functions. !! Can implement by using Attachable trait.
	public function putFile($file, $fileModel, $fileKey);
	public function removeFile($fileKey);
}
<?php

namespace GlaivePro\Ajaxable;

//!! Can implement all by using Ajaxable trait.
interface AjaxableInterface
{
	// Supply the name of the view of single row.
	public function getRowViewAttribute();
	
	// Draw single row or list of rows or list with no object specified. Create responses.
	public function drawRow();
	public function drawList();
	public static function drawStaticList();
	public function respondRow();
	public function respondList();
	public static function respondStaticList();
	
	// Tell if the object is being used (forbidden to delete).
	public function isUsed();
	
	// Actions before deleting.
	public function cleanUpForDeleting();
	
	// Actions before creating or updating (checking submitted info and setting mandatory fields).
	public function isAllowedTo($action);
	public function validate($request);
	public function validateForCreation($request);
	public function prepareForCreation($request);
	public function prepareUpdateOrCreate($request);
	
	// Visibility functions. !! Can implement by using Hideable trait.
	public function show();
	public function hide();
	public function toggle();
	
	// Ordering functions. !! Can implement by using Orderable trait. Check if you need to override the listNeighbours() functions.
	public function scopeOrdered($query);
	public function up();
	public function down();
	public function tidyUp();
	
	// File functions. !! Can implement by using Attachable trait.
	public function putFile($file, $fileModel, $fileKey);
	public function removeFile($fileKey);
}
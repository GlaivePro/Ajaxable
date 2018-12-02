<?php

namespace GlaivePro\Ajaxable\Traits;

use Illuminate\Support\HtmlString;

trait AjaxableHtml
{
	public function editor($field, $options = [])
	{
		$classes = $options['classes'] ?? [];
		if (!(is_array($classes) || $classes instanceof ArrayAccess))
			$classes = [$classes];
			
		$classes[] = 'ajaxable-edit';
		$options['classes'] = $classes;

		$attributes = $options['attributes'] ?? [];
		$attributes['data-model'] = __CLASS__;
		$attributes['data-id'] = $this->id;
		$attributes['data-key'] = $field;
		$options['attributes'] = $attributes;

		$tag = 'input';
		if ($options['type'] ?? false && 'input' != $options['type'])
		{
			if ('select' == $options['type'])
			{
				$options['selected'] = $this->$field;
				$tag = 'select';
			}
			else if ('textarea' == $options['type'])
			{
				$options['text'] = $this->$field;
				$tag = 'textarea';
			}
			else
				$options['attributes']['type'] = $options['type'];
		}

		$options['tag'] = $tag;
 
		return self::ajaxableHtml($options);
	}

	public function deleteButton($title, $options = [])
	{
		$classes = $options['classes'] ?? [];
		if (!(is_array($classes) || $classes instanceof ArrayAccess))
			$classes = [$classes];
			
		$classes[] = 'ajaxable-delete';
		$options['classes'] = $classes;
		
		$options['text'] = $title;

		$attributes = $options['attributes'] ?? [];
		$attributes['data-model'] = __CLASS__;
		$attributes['data-id'] = $this->id;
		$options['attributes'] = $attributes;

		if (!($options['tag'] ?? false))
			$options['tag'] = 'button';
 
		return self::ajaxableHtml($options);
	}

	public static function creatorButton($title, $options = [])
	{
		$classes = $options['classes'] ?? [];
		if (!(is_array($classes) || $classes instanceof ArrayAccess))
			$classes = [$classes];
		
		$classes[] = 'ajaxable-creator';
		$options['classes'] = $classes;
		
		$options['text'] = $title;

		$attributes = $options['attributes'] ?? [];
		$attributes['data-model'] = __CLASS__;

		foreach ($options['values'] ?? [] as $property => $value)
			$attributes['data-attribute_'.$property] = $value;

		$attributes['id'] = self::getPlainClassName().'-creator';
		if ($options['creator'] ?? false)
			$attributes['id'] = $options['creator'];

		$options['attributes'] = $attributes;

		if (!($options['tag'] ?? false))
			$options['tag'] = 'button';
 
		return self::ajaxableHtml($options);
	}

	public static function creatorField($field, $options = [])
	{
		$classes = $options['classes'] ?? [];
		if (!(is_array($classes) || $classes instanceof ArrayAccess))
			$classes = [$classes];
		
		$classes[] = 'ajaxable-new-attribute';
		$options['classes'] = $classes;

		$attributes = $options['attributes'] ?? [];
		$attributes['data-model'] = __CLASS__;
		$attributes['data-key'] = $field;

		$attributes['data-creator'] = '#'.self::getPlainClassName().'-creator';
		if ($options['creator'] ?? false)
			$attributes['data-creator'] = '#'.$options['creator'];

		$options['attributes'] = $attributes;

		$tag = 'input';
		if ($options['type'] ?? false && 'input' != $options['type'])
		{
			if ('select' == $options['type'])
			{
				$options['selected'] = $this->$field;
				$tag = 'select';
			}
			else if ('textarea' == $options['type'])
			{
				$options['text'] = $this->$field;
				$tag = 'textarea';
			}
			else
				$options['attributes']['type'] = $options['type'];
		}

		$options['tag'] = $tag;
 
		return self::ajaxableHtml($options);
	}

	protected static function ajaxableHtml($options)
	{
		$string = '<'.$options['tag'];
	
		$options['attributes']['class'] = implode(' ', $options['classes']);

		foreach ($options['attributes'] as $attribute => $value)
			$string .= ' '.$attribute.'="'.$value.'"';

		$string .= '>';

		if ('input' == $options['tag'])
			return new HtmlString($string);

		if ($options['text'] ?? false)
			$string .= $options['text'];

		if ('select' == $options['tag'])
			foreach ($options['option'] as $option)
				$string .= self::ajaxableOptionHtml($option);

		$string .= '</'.$options['tag'].'>';

		return new HtmlString($string);
	}

	protected static function ajaxableOptionHtml($option)
	{
		if ($option instanceof HtmlString)
			return $option->toHtml();

		if (is_array($option))
			return '<option value="'.$option['value'].'">'.$option['text'];

		return '<option value="'.$option.'">'.$option;
	}
}
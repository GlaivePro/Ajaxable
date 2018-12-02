<?php

namespace GlaivePro\Ajaxable\Traits;

use Illuminate\Support\HtmlString;

trait AjaxableHtml
{
	public function editor($field, $options = [])
	{
		$classes = $options['classes'] ?? [];
		$classes[] = 'ajaxable-edit';
		$options['classes'] = $classes;

		$attributes = $options['attributes'] ?? [];
		$attributes['data-model'] = get_class($this);
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
 
		return $this->ajaxableHtml($options);
	}

	public function deleteButton($title, $options = [])
	{
		$classes = $options['classes'] ?? [];
		$classes[] = 'ajaxable-delete';
		$options['classes'] = $classes;

		$attributes = $options['attributes'] ?? [];
		$attributes['data-model'] = get_class($this);
		$attributes['data-id'] = $this->id;
		$options['attributes'] = $attributes;

		if (!($options['tag'] ?? false))
			$options['tag'] = 'button';
 
		return $this->ajaxableHtml($options);
	}

	public static function creatorButton($title, $options = [])
	{
		$classes = $options['classes'] ?? [];
		$classes[] = 'ajaxable-creator';
		$options['classes'] = $classes;

		$attributes = $options['attributes'] ?? [];
		$attributes['data-model'] = get_class($this);
		$attributes['data-id'] = $this->id;

		foreach (optional($options['values']) as $property => $value)
			$attributes['data-attribute_'.$property] = $value;

		if ($options['creator'] ?? false)
			$attributes['id'] = $options['creator'];

		$options['attributes'] = $attributes;

		if (!($options['tag'] ?? false))
			$options['tag'] = 'button';
 
		return $this->ajaxableHtml($options);
	}

	public static function creatorField($options = [])
	{
		$classes = $options['classes'] ?? [];
		$classes[] = 'ajaxable-new-attribute';
		$options['classes'] = $classes;

		$attributes = $options['attributes'] ?? [];
		$attributes['data-model'] = get_class($this);
		$attributes['data-id'] = $this->id;
		$attributes['data-key'] = $field;

		if ($options['creator'] ?? false)
			$options['data-creator'] = '#'.$options['creator'];

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
 
		return $this->ajaxableHtml($options);
	}

	private function ajaxableHtml($options)
	{
		$string = '<'.$tag;

		$options['attributes']['classes'] = implode(' ', $options['classes']);

		foreach ($options['attributes'] as $attribute => $value)
			$string .= ' '.$attribute'="'.$value.'"';

		$string .= '>';

		if ('input' == $options['tag'])
			return new HtmlString($string);

		if ($options['text'] ?? false)
			$string .= $options['text'];

		if ('select' == $options['tag'])
			foreach ($options['option'] as $option)
				$string .= $this->ajaxableOptionHtml($option);

		$string .= '</'.$tag.'>';

		return new HtmlString($string);
	}

	private function ajaxableOptionHtml($option)
	{
		if ($option instanceof HtmlString)
			return $option->toHtml();

		if (is_array($option))
			return '<option value="'.$option['value'].'">'.$option['text'];

		return '<option value="'.$option.'">'.$option;
	}
}
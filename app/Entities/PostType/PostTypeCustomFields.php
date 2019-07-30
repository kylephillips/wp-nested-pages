<?php
namespace NestedPages\Entities\PostType;

/**
* Enables the filtering of custom fields in the quick edit interface 
* Basic field types currently supported : text|date|select
* 
* Filter should return an array of fields
* 
* Example:
* $fields = [
		[
			'key' => 'custom_field', 
			'label' => __('Custom Field', 'text-domain'),
			'type' => 'date', // date|text|select
			'format' => 'm/d/Y', // Required for date
			'required' => false,
			'validation_message' => __('A deadline is required.'),
			'choices' => [] // For select type
		]
	];
*/
class PostTypeCustomFields 
{
	public function outputFields($post_type, $column = 'left')
	{
		$fields = apply_filters('nestedpages_quickedit_custom_fields', [], $post_type, $column);
		if ( empty($fields) ) return;
		$out = '';
		foreach ( $fields as $field ){
			$method = $field['type'] . 'Field';
			if ( method_exists($this, $method) ) $out .= $this->$method($field);
		}
		return $out;
	}

	public function dateField($field)
	{
		$out = '<div class="form-control np-datepicker-container">';
		$out .= '<label>' . $field['label'] . '</label>';
		$out .= '<div class="datetime"><input type="text" data-datepicker-format="' . $field['format_datepicker'] . '" name="np_custom_' . $field['key'] . '" class="np_datepicker full" value="" data-np-custom-field="' . $field['key'] . '" /></div>';
		$out .= '</div>';
		return $out;
	}

	public function textField($field)
	{
		$out = '<div class="form-control">';
		$out .= '<label>' . $field['label'] . '</label>';
		$out .= '<input type="text" name="np_custom_' . $field['key'] . '" value="" data-np-custom-field="' . $field['key'] . '" />';
		$out .= '</div>';
		return $out;
	}

	public function selectField($field)
	{
		$out = '<div class="form-control">';
		$out .= '<label>' . $field['label'] . '</label>';
		$out .= '<select name="np_custom_' . $field['key'] . '" value="" data-np-custom-field="' . $field['key'] . '">';
		foreach ( $field['choices'] as $key => $label ){
			$out .= '<option value="' . $key . '">' . $label . '</option>';
		}
		$out .= '</select>';
		$out .= '</div>';
		return $out;
	}

	/**
	* Output the data attributes for the post in quick edit for initial population
	*/
	public function dataAttributes($post, $post_type)
	{
		$custom_fields_left = apply_filters('nestedpages_quickedit_custom_fields', [], $post_type, $column = 'left');
		$custom_fields_right = apply_filters('nestedpages_quickedit_custom_fields', [], $post_type, $column = 'right');
		$custom_fields = array_merge($custom_fields_left, $custom_fields_right);
		$out = '';
		if ( empty($custom_fields) ) return $out;
		foreach ( $custom_fields as $field ) :
			$custom_value = ( isset($post->meta[$field['key']]) ) ? $post->meta[$field['key']] : null;
			if ( $custom_value ) :
				$value = $custom_value[0];
				if ( $field['type'] == 'date' && $field['format_save'] && $value !== '' ) $value = date($field['format_save'], strtotime($value));
				$out .= ' data-npcustom-' . $field['key'] . '="' . $value . '"';
			endif;
		endforeach;
		return $out;
	}
}
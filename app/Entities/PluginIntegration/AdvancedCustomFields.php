<?php 
namespace NestedPages\Entities\PluginIntegration;

/**
* Advanced Custom Fields Integration
* @link https://www.advancedcustomfields.com/
*/

class AdvancedCustomFields 
{
	/**
	* Installed
	* @var boolean
	*/
	public $installed = false;

	/**
	* Unsupported Field Types
	* @var array
	*/
	private $unsupported;

	public function __construct()
	{
		return;
		if ( class_exists('acf') ){
			$this->installed = true;
			$this->setUnsupportedFieldTypes();
			return;
		} 
	}

	/**
	* Set the unsupported field types
	*/
	private function setUnsupportedFieldTypes()
	{
		$this->unsupported = array(
			'relationship'
		);
	}

	/**
	* Get the ACF fields associated with a post type
	* @param string post_type
	* @return array
	*/
	public function getFieldsForPostType($post_type)
	{
		if ( !function_exists('get_field') ) return false;
		$field_groups = acf_get_field_groups(array(
			'post_type' => $post_type
		));
		if ( !$field_groups ) return false;
		$all_fields = array();
		$c = 0;
		foreach ( $field_groups as $key => $group ){
			$fields = acf_get_fields($group);
			foreach($fields as $field){
				if ( in_array($field['type'], $this->unsupported) ) continue;
				$all_fields[$c]['key'] = $field['key'];
				$all_fields[$c]['label'] = $field['label'];
				$all_fields[$c]['type'] = $field['type'];
				$c++;
			}
			$c++;
		}
		return $all_fields;
	}
}
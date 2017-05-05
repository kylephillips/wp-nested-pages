<?php
namespace NestedPages\Activation\Updates;

class CustomFieldsToHidden
{
	/**
	* Meta Keys to Convert
	* @var array
	*/
	private $meta_keys;

	public function __construct()
	{
		if ( get_option('nested_pages_custom_fields_hidden') ) return;
		$this->setKeys();
		$this->convertFields();
	}

	/**
	* Set the Keys to convert
	*/
	private function setKeys()
	{
		$this->meta_keys = array(
			'np_nav_title',
			'nested_pages_status',
			'np_title_attribute',
			'np_nav_css_classes',
			'np_link_target',
			'np_nav_status',
			'np_nav_menu_item_type',
			'np_nav_menu_item_object',
			'np_nav_menu_item_object_id'
		);
	}

	/**
	* Convert the fields
	*/
	private function convertFields()
	{
		global $wpdb;
		$meta_table = $wpdb->prefix . 'postmeta';
		foreach ( $this->meta_keys as $key ){
			$newKey = '_' . $key;
			$sql = $wpdb->update(
				$meta_table,
				array('meta_key' => $newKey),
				array('meta_key' => $key)
			);
		}
		$this->setOption();
	}

	/**
	* Set the Updated to Hidden Fields Option so this process doesn't run again
	*/
	private function setOption()
	{
		update_option('nested_pages_custom_fields_hidden', 'true', true);
	}
}
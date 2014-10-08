<?php namespace NestedPages;
/**
* Plugin Settings
*/
class Settings {

	public function __construct()
	{
		add_action( 'admin_menu', [ $this, 'adminMenu' ] );
		add_action( 'admin_init', [ $this, 'registerSettings' ] );
		$this->addImageSize();
	}

	/**
	* Add the admin menu item
	*/
	public function adminMenu()
	{
		add_menu_page( 
			'Pages',
			'Pages',
			'manage_options',
			'nestedpages', 
			array( $this, 'settingsPage' ),
			'dashicons-admin-page',
			20
		);
	}


	/**
	* Register the settings
	*/
	public function registerSettings()
	{
		register_setting( 'wp-duel-general', 'wpduel_post_type' );
		register_setting( 'wp-duel-general', 'wpduel_track_votes');
		register_setting( 'wp-duel-general', 'wpduel_single_view');
		register_setting( 'wp-duel-display', 'wpduel_output_styles');
		register_setting( 'wp-duel-display', 'wpduel_output_js');
		register_setting( 'wp-duel-display', 'wpduel_all_complete');
		register_setting( 'wp-duel-display', 'wpduel_highlight_color');
		register_setting( 'wp-duel-display', 'wpduel_results_view');
		register_setting( 'wp-duel-display', 'wpduel_chart_color_one');
		register_setting( 'wp-duel-display', 'wpduel_chart_color_two');
		register_setting( 'wp-duel-thumbnails', 'wpduel_show_image' );
		register_setting( 'wp-duel-thumbnails', 'wpduel_custom_image_size');
		register_setting( 'wp-duel-thumbnails', 'wpduel_wp_image_size');
		register_setting( 'wp-duel-thumbnails', 'wpduel_image_width', [$this, 'validateSize']);
		register_setting( 'wp-duel-thumbnails', 'wpduel_image_height', [$this, 'validateSize']);
	}


	/**
	* The Settings Page
	*/
	public function settingsPage()
	{
		$tab = ( isset($_GET['tab']) ) ? $_GET['tab'] : 'general';
		include( dirname( dirname(__FILE__) ) . '/views/settings.php');
	}


	/**
	* Get option list of post types
	*/
	public function postTypes()
	{
		$selected = get_option('wpduel_post_type');
		$types = get_post_types(['public'=>true, 'publicly_queryable'=>true ], 'objects');
		$output = "";

		foreach( $types as $type ){
			if ( ($type->name !== 'duel') && ($type->name !== 'attachment')) :
				$output .= '<option value="' . $type->name . '"';
				if ( $type->name == $selected ) $output .= ' selected';
				$output .= '>';
				$output .= ( $type->name == 'contender' ) ? 'Contenders (WP Duel Default)' : $type->labels->name;
			endif; 
			$output .= '</option>';
		}
		return $output;
	}

	/**
	* Add Custom Image Size
	*/
	public function addImageSize()
	{
		if ( ( get_option('wpduel_custom_image_size') == 'yes' ) && ( get_option('wpduel_image_width') !== '' ) && ( get_option('wpduel_image_height') !== '' ) ){
			$width = get_option('wpduel_image_width');
			$height = get_option('wpduel_image_height');
			add_image_size('wpduel', $width, $height, true);
		}
	}

	/**
	* Validate Custom Size
	*/
	public function validateSize($data)
	{
		if ( is_numeric($data) ) return $data;
	}

}
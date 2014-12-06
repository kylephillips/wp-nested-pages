<?php
/**
* Plugin JS & CSS Dependencies
*/
class NP_Dependencies {

	/**
	* Plugin Directory
	*/
	private $plugin_dir;

	public function __construct()
	{
		add_action( 'admin_enqueue_scripts', array($this, 'styles') );
		add_action( 'admin_enqueue_scripts', array($this, 'scripts') );
		$this->plugin_dir = plugins_url() . '/wp-nested-pages';
	}


	/**
	* Admin Styles
	*/
	public function styles()
	{
		wp_enqueue_style(
			'nestedpages', 
			$this->plugin_dir . '/assets/css/nestedpages.css', 
			array(), 
			'1.1.7'
		);
	}


	/**
	* Admin Scripts required by plugin
	* Only Loads on the Nested Pages screen
	*/
	public function scripts()
	{
		$screen = get_current_screen();
		if ( strpos( $screen->id, 'nestedpages' ) ) :
			wp_enqueue_script('suggest');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script(
				'bootstrap-modal', 
				$this->plugin_dir . '/assets/js/lib/bs-modal.js', 
				array('jquery'), 
				'1.0'
			);
			wp_enqueue_script(
				'ui-touch-punch', 
				$this->plugin_dir . '/assets/js/lib/jquery.ui.touch-punch.min.js', 
				array('jquery', 'jquery-ui-sortable'), 
				'1.0'
			);
			wp_enqueue_script(
				'nested-sortable', 
				$this->plugin_dir . '/assets/js/lib/jquery.mjs.nestedSortable.js', 
				array('jquery', 'jquery-ui-sortable'), 
				'1.0'
			);
			wp_enqueue_script(
				'nestedpages', 
				$this->plugin_dir . '/assets/js/nestedpages.min.js', 
				array('jquery'), 
				'1.1.7'
			);
			$localized_data = array(
				'np_nonce' => wp_create_nonce( 'nestedpages-nonce' ),
				'expand_text' => __('Expand Pages', 'nestedpages'),
				'collapse_text' => __('Collapse Pages', 'nestedpages'),
				'show_hidden' => __('Show Hidden', 'nestedpages'),
				'hide_hidden' => __('Hide Hidden', 'nestedpages'),
				'add_link' => __('Add Link', 'nestedpages'),
				'add_child_link' => __('Add Child Link', 'nestedpages')
			);
			$syncmenu = ( get_option('nestedpages_menusync') == 'sync' ) ? true : false;
			$localized_data['syncmenu'] = $syncmenu;
			wp_localize_script( 
				'nestedpages', 
				'nestedpages', 
				$localized_data
			);
		endif;
	}

}
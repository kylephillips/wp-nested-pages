<?php 

namespace NestedPages\Activation;

use NestedPages\Entities\PostType\PostTypeRepository;
use NestedPages\Entities\PluginIntegration\IntegrationFactory;
use NestedPages\Config\SettingsRepository;
use NestedPages\Helpers;

/**
* Plugin JS & CSS Dependencies
*/
class Dependencies 
{

	/**
	* Plugin Directory
	*/
	private $plugin_dir;

	/**
	* Plugin Version
	*/
	private $plugin_version;

	/**
	* Post Type Repository
	*/
	private $post_type_repo;

	/**
	* Settings Repository
	*/
	private $settings;

	/**
	* Integrations
	*/
	private $integrations;
	
	public function __construct()
	{
		$this->post_type_repo = new PostTypeRepository;
		$this->integrations = new IntegrationFactory;
		$this->settings = new SettingsRepository;
		$this->setPluginVersion();
		add_action( 'admin_enqueue_scripts', array($this, 'styles') );
		add_action( 'admin_enqueue_scripts', array($this, 'scripts') );
		add_action( 'admin_enqueue_scripts', array($this, 'settingsScripts') );
		$this->plugin_dir = Helpers::plugin_url();
	}

	/**
	* Set the Plugin Version
	*/
	private function setPluginVersion()
	{
		global $np_version;
		$this->plugin_version = $np_version;
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
			$this->plugin_version
		);
		if ( $this->integrations->plugins->acf->installed ) wp_enqueue_style('acf-input');
	}

	/**
	* Admin Scripts required by plugin
	* Only Loads on the Nested Pages screen
	*/
	public function scripts()
	{
		$screen = get_current_screen();
		global $np_env;
		if ( strpos( $screen->id, 'nestedpages' ) ) :
			wp_enqueue_script('suggest');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script(
				'bootstrap', 
				$this->plugin_dir . '/assets/js/lib/bootstrap.min.js', 
				array('jquery'), 
				'3.3.2'
			);
			wp_enqueue_script(
				'ui-touch-punch', 
				$this->plugin_dir . '/assets/js/lib/jquery.ui.touch-punch.min.js', 
				array('jquery', 'jquery-ui-sortable'), 
				$this->plugin_version
			);
			wp_enqueue_script(
				'nested-sortable', 
				$this->plugin_dir . '/assets/js/lib/jquery.mjs.nestedSortable.js', 
				array('jquery', 'jquery-ui-sortable'), 
				$this->plugin_version
			);
			if ( $np_env == 'dev' ){
				wp_enqueue_script(
					'nestedpages', 
					$this->plugin_dir . '/assets/js/lib/nestedpages.js', 
					array('jquery'), 
					$this->plugin_version
				);
			} else {
				wp_enqueue_script(
					'nestedpages', 
					$this->plugin_dir . '/assets/js/nestedpages.min.js', 
					array('jquery'), 
					$this->plugin_version
				);
			}
			$localized_data = array(
				'np_nonce' => wp_create_nonce( 'nestedpages-nonce' ),
				'expand_text' => __('Expand All', 'nestedpages'),
				'collapse_text' => __('Collapse All', 'nestedpages'),
				'show_hidden' => __('Show Hidden', 'nestedpages'),
				'hide_hidden' => __('Hide Hidden', 'nestedpages'),
				'add_link' => __('Add Link', 'nestedpages'),
				'add_child_link' => __('Add Child Link', 'nestedpages'),
				'title' => __('Title', 'nestedpages'),
				'quick_edit' => __('Quick Edit', 'nestedpages'),
				'page_title' => __('Page Title', 'nestedpages'),
				'view' => __('View', 'nestedpages'),
				'add_child_short' => __('Add Child', 'nestedpages'),
				'add_child'  => __('Add Child Page', 'nestedpages'),
				'add_child_pages' => __('Add Child Pages', 'nestedpages'),
				'add' => __('Add', 'nestedpages'),
				'add_page' => __('Add Page', 'nestedpages'),
				'add_pages' => __('Add Pages', 'nestedpages'),
				'add_multiple' => __('Add Multiple', 'nestedpages'),
				'trash_confirm' => __('Are you sure you would like to empty the trash? This action is not reversable.', 'nestedpages'),
				'hidden' => __('Hidden', 'nestedpages'),
				'bulk_actions' => __('Bulk Actions', 'nestedpages'),
				'link_delete_confirmation' => __('Your selection includes link items, which cannot be recovered after deleting. Would you like to continue? (Other items are moved to the trash)', 'nestedpages'),
				'link_delete_confirmation_singular' => __('Are you sure you would like to delete this item? This action is not reversable.', 'nestedpages'),
				'delete' => __('Delete', 'nestedpages'),
				'trash_delete_links' => __('Trash Posts and Delete Links'),
				'manual_menu_sync' => $this->settings->autoMenuDisabled(),
				'manual_order_sync' => $this->settings->autoPageOrderDisabled(),
			);
			$syncmenu = ( get_option('nestedpages_menusync') == 'sync' ) ? true : false;
			$localized_data['syncmenu'] = $syncmenu;
			$localized_data['post_types'] = $this->post_type_repo->getPostTypesObject();
			wp_localize_script( 
				'nestedpages', 
				'nestedpages', 
				$localized_data
			);
			if ( $this->integrations->plugins->acf->installed ) wp_enqueue_script('acf-input');
		endif;
	}

	/**
	* Settings
	*/
	public function settingsScripts()
	{
		$screen = get_current_screen();
		if ( strpos( $screen->id, 'nested-pages-settings' ) ) :
			wp_enqueue_script(
				'nestedpages-settings', 
				$this->plugin_dir . '/assets/js/nestedpages.settings.min.js', 
				array('jquery'), 
				$this->plugin_version
			);
		endif;
	}

}
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
		global $np_page_params;
		global $np_env;
		$settings_page = ( strpos($screen->id, 'nested-pages-settings') ) ? true : false;
		if ( strpos( $screen->id, 'nestedpages' ) || $settings_page ) :
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
					$this->plugin_dir . '/assets/js/nestedpages.js', 
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
				'expand_text' => __('Expand All', 'wp-nested-pages'),
				'collapse_text' => __('Collapse All', 'wp-nested-pages'),
				'show_hidden' => __('Show Hidden', 'wp-nested-pages'),
				'hide_hidden' => __('Hide Hidden', 'wp-nested-pages'),
				'add_link' => __('Add Link', 'wp-nested-pages'),
				'add_child_link' => __('Add Child Link', 'wp-nested-pages'),
				'title' => __('Title', 'wp-nested-pages'),
				'quick_edit' => __('Quick Edit', 'wp-nested-pages'),
				'page_title' => __('Page Title', 'wp-nested-pages'),
				'view' => __('View', 'wp-nested-pages'),
				'add_child_short' => __('Add Child', 'wp-nested-pages'),
				'add_child'  => __('Add Child Page', 'wp-nested-pages'),
				'add_child_pages' => __('Add Child Pages', 'wp-nested-pages'),
				'add' => __('Add', 'wp-nested-pages'),
				'add_page' => __('Add Page', 'wp-nested-pages'),
				'add_pages' => __('Add Pages', 'wp-nested-pages'),
				'add_multiple' => __('Add Multiple', 'wp-nested-pages'),
				'trash_confirm' => __('Are you sure you would like to empty the trash? This action is not reversable.', 'wp-nested-pages'),
				'hidden' => __('Hidden', 'wp-nested-pages'),
				'bulk_actions' => __('Bulk Actions', 'wp-nested-pages'),
				'link_delete_confirmation' => __('Your selection includes link items, which cannot be recovered after deleting. Would you like to continue? (Other items are moved to the trash)', 'wp-nested-pages'),
				'link_delete_confirmation_singular' => __('Are you sure you would like to delete this item? This action is not reversable.', 'wp-nested-pages'),
				'delete' => __('Delete', 'wp-nested-pages'),
				'trash_delete_links' => __('Trash Posts and Delete Links', 'wp-nested-pages'),
				'manual_menu_sync' => $this->settings->autoMenuDisabled(),
				'manual_order_sync' => $this->settings->autoPageOrderDisabled(),
				'currently_assigned_to' => __('Currently assigned to:', 'wp-nested-pages'),
				'remove' => __('Remove', 'wp-nested-pages'),
				'settings_page' => $settings_page,
				'wpml' => ( $this->integrations->plugins->wpml->installed ) ? true : false,
				'add_translation' => __('Add Translation', 'wp-nested-pages'),
				'edit' => __('Edit', 'wp-nested-pages')
			);
			$syncmenu = ( get_option('nestedpages_menusync') == 'sync' ) ? true : false;
			$localized_data['syncmenu'] = $syncmenu;
			$localized_data['post_types'] = $this->post_type_repo->getPostTypesObject();
			if ( isset($np_page_params) && is_array($np_page_params) && array_key_exists($screen->id, $np_page_params) ){
				$localized_data['current_post_type'] = $np_page_params[$screen->id]['post_type'];
			}
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
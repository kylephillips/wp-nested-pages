<?php 
namespace NestedPages\Entities\Post;

use NestedPages\Config\SettingsRepository;
use NestedPages\Entities\PostType\PostTypeRepository;

/**
* WP Actions tied to a Post on Save
*/
class PostSaveActions 
{
	/**
	* Settings Repository
	* @var object
	*/
	private $settings;

	/**
	* Post Type Repository
	* @var object
	*/
	private $post_type_repo;

	public function __construct()
	{
		$this->settings = new SettingsRepository;
		$this->post_type_repo = new PostTypeRepository;
		add_action( 'save_post', [$this, 'defaultNavStatus'], 10, 3);
	}

	/**
	* If this is the initial save and the "hide in nav menu by default" option is selected, update the post meta
	*/
	public function defaultNavStatus($post_id, $post_object, $update)
	{
		if ( $update ) return;
		if ( !$this->settings->defaultHideInNav() ) return;
		if ( $post_object->post_type !== 'page' ) return;
		$types = $this->post_type_repo->getPostTypesObject();
		if ( isset($types['page']) && isset($types['page']->np_enabled) && $types['page']->np_enabled ){
			update_post_meta($post_id, '_np_nav_status', 'hide');
		}
	}
}
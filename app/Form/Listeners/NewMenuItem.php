<?php
namespace NestedPages\Form\Listeners;

use NestedPages\Helpers;

/**
* Creates new Menu item and saves redirect post
* @return json response
*/
class NewMenuItem extends BaseHandler
{
	public function __construct()
	{
		parent::__construct();
		if ( !current_user_can('publish_posts') ) return;
		$this->validateFields();
		$this->saveRedirect();
		$this->syncMenu();
		$this->sendResponse();
	}

	/**
	* Validate
	*/
	private function validateFields()
	{
		if ( $_POST['menuType'] == 'custom' && $_POST['navigationLabel'] == "" ) return wp_send_json(array('status' => 'error', 'message' => __('Custom Links must have a label.', 'wp-nested-pages')));
		if ( $_POST['menuType'] == 'custom' && $_POST['url'] == "" ) return wp_send_json(array('status' => 'error', 'message' => __('Please provide a valid URL.', 'wp-nested-pages')));
	}

	/**
	* Save the item as a redirect post type
	*/
	private function saveRedirect()
	{
		$new_link = $this->post_update_repo->saveRedirect($this->data);
		if ( !$new_link ) $this->sendErrorResponse();

		$this->data['post'] = $_POST;
		$this->data['post']['id'] = $new_link;
		$this->data['post']['content'] = esc_url($_POST['url']);
		$this->data['post']['delete_link'] = get_delete_post_link($new_link, '', true);
		$this->addExtras($new_link);

		$this->response = [
			'status' => 'success',
			'message' => __('Link successfully updated.', 'wp-nested-pages'),
			'post_data' => $this->data['post']
		];
	}

	/**
	* Add extra post data
	*/
	private function addExtras($post)
	{
		$this->data['post']['link_target'] = ( isset($_POST['linkTarget']) && $_POST['linkTarget'] == "_blank" ) ? "_blank" : "";

		// Add custom menu data
		$type = $this->data['post']['menuType'];
		if ( $type == 'custom' ){
			$this->data['post']['original_link'] = null;
			$this->data['post']['original_title'] = null;
			return;
		}
		if ( $type == 'taxonomy' ){
			$term = get_term_by('id', $this->data['post']['objectId'], $this->data['post']['objectType']);
			$this->data['post']['original_link'] = get_term_link($term);
			$this->data['post']['original_title'] = $term->name;
			return;
		}
		if ( $type == 'post_type_archive' ){
			$this->data['post']['original_link'] = get_post_type_archive_link($this->data['post']['objectType']);
			$this->data['post']['original_title'] = sanitize_text_field($this->data['post']['menuTitle']);
			return;
		}
		$id = $this->data['post']['objectId'];
		$this->data['post']['original_link'] = get_the_permalink($id);
		$this->data['post']['original_title'] = get_the_title($id);
	}
}
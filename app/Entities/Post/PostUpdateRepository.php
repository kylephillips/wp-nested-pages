<?php namespace NestedPages\Entities\Post;

use NestedPages\Form\Validation\Validation;
/**
* Post Create/Update Methods
*/
class PostUpdateRepository {

	/**
	* Validation Class
	* @var object NP_Validation
	*/
	protected $validation;

	public function __construct()
	{
		$this->validation = new Validation;
	}


	/**
	* Update Order
	* @param array posts
	* @param int parent
	* @since 1.0
	*/
	public function updateOrder($posts, $parent = 0)
	{
		$this->validation->validatePostIDs($posts);
		foreach( $posts as $key => $post )
		{
			wp_update_post(array(
				'ID' => sanitize_text_field($post['id']),
				'menu_order' => $key,
				'post_parent' => $parent
			));

			if ( isset($post['children']) ){
				$this->updateOrder($post['children'], $post['id']);
			}
		}
		return true;
	}

}
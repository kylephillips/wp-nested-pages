<?php 
namespace NestedPages\Entities\Post;

use NestedPages\Entities\Post\PostRepository;
use NestedPages\Entities\Post\PostUpdateRepository;

/**
* Factory class for adding new posts
*/
class PostFactory 
{
	/**
	* Post Repository
	* @var object
	*/
	private $post_repo;

	/**
	* Post Repository
	* @var object
	*/
	private $post_update_repo;

	/**
	* New Page IDs
	* @var array
	*/
	private $new_ids = array();


	public function __construct()
	{
		$this->post_repo = new PostRepository;
		$this->post_update_repo = new PostUpdateRepository;
	}

	/**
	* Create New Child Pages
	*/
	public function createChildPosts($data)
	{
		foreach($data['post_title'] as $key => $title){
			$post_type = sanitize_text_field($data['post_type']);
			$post = array(
				'post_title' => sanitize_text_field($title),
				'post_status' => sanitize_text_field($data['_status']),
				'post_author' => sanitize_text_field($data['post_author']),
				'post_parent' => sanitize_text_field($data['parent_id']),
				'post_type' => $post_type
			);
			$new_page_id = wp_insert_post($post);
			$data['post_id'] = $new_page_id;
			if ( isset($data['page_template']) ) $this->post_update_repo->updateTemplate($data);
			if ( isset($data['nav_status']) ) $this->post_update_repo->updateNavStatus($data);
			$this->new_ids[$key] = $new_page_id;
		}
		return $this->getNewPosts($post_type);
	}

	/**
	* Get Array of New Pages
	*/
	private function getNewPosts($post_type)
	{
		$new_posts = $this->post_repo->postArray($this->new_ids, $post_type);
		return $new_posts;
	}
}
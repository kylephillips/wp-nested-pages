<?php namespace NestedPages\Entities\Post;

use NestedPages\Entities\Post\PostRepository;

/**
* Factory class for adding new posts
*/
class PostFactory {

	/**
	* Post Repository
	* @var object
	*/
	private $repo;

	/**
	* New Page IDs
	* @var array
	*/
	private $new_ids = array();


	public function __construct()
	{
		$this->repo = new PostRepository;
	}
	

	/**
	* Create New Child Pages
	*/
	public function createChildPages($data)
	{
		foreach($data['post_title'] as $key => $title){
			$post = array(
				'post_title' => sanitize_text_field($title),
				'post_type' => 'page',
				'post_status' => sanitize_text_field($data['_status']),
				'post_author' => sanitize_text_field($data['post_author']),
				'post_parent' => sanitize_text_field($data['parent_id'])
			);
			$new_page_id = wp_insert_post($post);
			$this->new_ids[$key] = $new_page_id;
		}
		return $this->getNewPages();
	}


	/**
	* Get Array of New Pages
	*/
	private function getNewPages()
	{
		$new_pages = $this->repo->pageArray($this->new_ids);
		return $new_pages;
	}


}
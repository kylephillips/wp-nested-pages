<?php
require_once('class-np-repository-post.php');
require_once('class-np-validation.php');
/**
* Factory class for adding new posts
*/
class NP_PostFactory {

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
		$this->repo = new NP_PostRepository;
	}
	

	/**
	* Create New Child Pages
	*/
	public function createChildPages($data)
	{
		foreach($data['post_title'] as $key => $title){
			$post = array(
				'post_title' => $title,
				'post_type' => 'page',
				'post_status' => $data['_status'],
				'post_author' => $data['post_author'],
				'post_parent' => $data['parent_id']
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
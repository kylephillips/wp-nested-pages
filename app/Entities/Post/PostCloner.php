<?php

namespace NestedPages\Entities\Post;

class PostCloner
{
	/**
	* Original Post ID
	* @var int
	*/
	private $original_id;

	/**
	* Original Post Object
	* @var int
	*/
	private $original_post;

	/**
	* The New Post ID
	* @var int
	*/
	private $new_id;

	/**
	* The New Post
	* @var object
	*/
	private $new_post;

	/**
	* Clone Options
	* @var array
	*/
	private $clone_options = array();

	/**
	* New Posts
	* @var array of post IDs
	*/
	private $new_posts;

	/**
	* Clone the post
	* @var int $id
	*/
	public function clonePost($id, $quantity = 1, $status = 'publish', $author = null)
	{
		$this->original_id = $id;
		$this->original_post = get_post( $id );
		$this->clone_options['quantity'] = $quantity;
		$this->clone_options['status'] = $status;
		$this->clone_options['author'] = $author;
		$this->loopClone();	
		return $this->new_posts;
	}

	/**
	* Clone Post for quantity specified
	*/
	private function loopClone()
	{
		for ( $i = 0; $i < $this->clone_options['quantity']; $i++ ){
			$this->clonePostData();
			$this->cloneTaxonomies();
			$this->cloneMeta();
		}
	}

	/**
	* Clone the standard post data
	*/
	private function clonePostData()
	{
		$args = array(
			'comment_status' => $this->original_post->comment_status,
			'ping_status'    => $this->original_post->ping_status,
			'post_author'    => $this->clone_options['author'],
			'post_content'   => $this->original_post->post_content,
			'post_excerpt'   => $this->original_post->post_excerpt,
			'post_name'      => $this->original_post->post_name,
			'post_parent'    => $this->original_post->post_parent,
			'post_password'  => $this->original_post->post_password,
			'post_status'    => $this->clone_options['status'],
			'post_title'     => $this->original_post->post_title,
			'post_type'      => $this->original_post->post_type,
			'to_ping'        => $this->original_post->to_ping,
			'menu_order'     => $this->original_post->menu_order
		);
		$this->new_id = wp_insert_post($args);
		$this->new_posts[] = $this->new_id;
	}

	/**
	* Clone the taxonomies
	*/
	private function cloneTaxonomies()
	{
		$taxonomies = get_object_taxonomies($this->original_post->post_type);
 		foreach ($taxonomies as $taxonomy) {
 			$post_terms = wp_get_object_terms($this->original_id, $taxonomy, array('fields' => 'slugs'));
 			wp_set_object_terms($this->new_id, $post_terms, $taxonomy, false);
 		}
	}

	/**
	* Clone the custom fields
	*/
	private function cloneMeta()
	{
		$meta = get_post_meta($this->original_id);
		foreach($meta as $key => $value){
			add_post_meta($this->new_id, $key, $value[0]);
		}
	}
}
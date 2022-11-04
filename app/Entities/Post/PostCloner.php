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
	private $clone_options = [];

	/**
	* Clone the post
	* @param int $id - The ID of the original post to clone
	* @param int $quantity - The number of copies to make
	* @param str $status - The post status
	* @param int $author - The author id to assign the new post(s) to
	* @param bool $clone_children - Whether to clone the post tree (hierarchical only)
	* @param int $parent_id - The parent post to assign the clone to
	* @param bool $original_clone - Whether this is the original clone. Used in post tree cloning only
	*/
	public function clonePost($id, $quantity = 1, $status = 'publish', $author = null, $clone_children = false, $parent_id = null, $original_clone = true)
	{
		if ( !current_user_can('edit_post', $id) ) return;
		$this->original_id = $id;
		$this->original_post = get_post( $id );
		$this->clone_options['quantity'] = $quantity;
		$this->clone_options['status'] = $status;
		$this->clone_options['author'] = $author;
		$this->clone_options['clone_children'] = $clone_children;
		$this->clone_options['parent_id'] = $parent_id;
		$this->clone_options['original_clone'] = $original_clone;
		$this->loopClone();	
	}

	/**
	* Clone Post for quantity specified
	*/
	private function loopClone()
	{
		$quantity = ( $this->clone_options['original_clone'] ) ? $this->clone_options['quantity'] : 1;
		for ( $i = 0; $i < $quantity; $i++ ){
			$this->clonePostData();
			$this->cloneTaxonomies();
			$this->cloneMeta();
			$this->cloneChildren();
		}
	}

	/**
	* Loop through and clone the children if set to do so
	*/
	private function cloneChildren()
	{
		if ( !$this->clone_options['clone_children'] ) return;
		$post_type = $this->original_post->post_type;
		if ( $post_type = 'page' ) $post_type = ['page', 'np-redirect'];
		$child_ids = [];
		$children = new \WP_Query([
			'post_type' => $post_type, 
			'post_parent' => $this->original_id, 
			'posts_per_page' => -1, 
			'fields' => 'ids'
		]);
		if ( $children->have_posts() ) $child_ids = $children->posts;
		wp_reset_postdata();
		if ( empty($child_ids) ) return;
		foreach ( $child_ids as $child_id ){
			$cloner = (new PostCloner)
				->clonePost(
					$child_id, 
					$this->clone_options['quantity'], 
					$this->clone_options['status'], 
					$this->clone_options['author'], 
					$this->clone_options['clone_children'],
					$this->new_id,
					false
				);
		}
	}

	/**
	* Clone the standard post data
	*/
	private function clonePostData()
	{
		$parent = ( $this->clone_options['parent_id'] ) ? $this->clone_options['parent_id'] : $this->original_post->post_parent;
		$args = [
			'comment_status' => $this->original_post->comment_status,
			'ping_status'    => $this->original_post->ping_status,
			'post_author'    => $this->clone_options['author'],
			'post_content'   => $this->original_post->post_content,
			'post_excerpt'   => $this->original_post->post_excerpt,
			'post_name'      => $this->original_post->post_name,
			'post_parent'    => $parent,
			'post_password'  => $this->original_post->post_password,
			'post_status'    => $this->clone_options['status'],
			'post_title'     => $this->original_post->post_title,
			'post_type'      => $this->original_post->post_type,
			'to_ping'        => $this->original_post->to_ping,
			'menu_order'     => $this->original_post->menu_order
		];
		$this->new_id = wp_insert_post(wp_slash($args));
	}

	/**
	* Clone the taxonomies
	*/
	private function cloneTaxonomies()
	{
		$taxonomies = get_object_taxonomies($this->original_post->post_type);
 		foreach ($taxonomies as $taxonomy) {
 			$post_terms = wp_get_object_terms($this->original_id, $taxonomy, ['fields' => 'slugs']);
 			wp_set_object_terms($this->new_id, $post_terms, $taxonomy, false);
 		}
	}

	/**
	* Clone the post meta
	*/
	private function cloneMeta()
	{
		$original_id = $this->original_id;
		$new_id = $this->new_id;
		$meta_keys = get_post_custom_keys($original_id);
		foreach ( $meta_keys as $meta_key ) {
			$meta_values = \get_post_custom_values($meta_key, $original_id);
			delete_post_meta( $new_id, $meta_key );
			foreach ( $meta_values as $meta_value ) {
				$meta_value = \maybe_unserialize($meta_value );
				add_post_meta( $new_id, $meta_key, wp_slash( $meta_value ) );
			}
		}
	}
}
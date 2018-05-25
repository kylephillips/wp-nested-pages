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
	private $new_ids = [];


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
			$post = [
				'post_title' => sanitize_text_field($title),
				'post_status' => sanitize_text_field($data['_status']),
				'post_author' => sanitize_text_field($data['post_author']),
				'post_parent' => sanitize_text_field($data['parent_id']),
				'post_type' => $post_type
			];
			$new_page_id = wp_insert_post($post);
			$data['post_id'] = $new_page_id;
			if ( isset($data['page_template']) ) $this->post_update_repo->updateTemplate($data);
			if ( isset($data['nav_status']) ) $this->post_update_repo->updateNavStatus($data);
			$this->new_ids[$key] = $new_page_id;
		}
		return $this->getNewPosts($post_type);
	}

	/**
	* Create new Posts before/after a specified post
	*/
	public function createBeforeAfterPosts($data)
	{
		// Get the source post, so the reference point can be determined
		global $wpdb;
		$parent = null;
		$menu_order = 0;
		$post_type = sanitize_text_field($data['post_type']);
		$before = ( isset($data['before_id']) && $data['before_id'] !== '' ) ? true : false;
		$reference_post = ( $before ) ? intval($data['before_id']) : intval($data['after_id']);
		$pq = new \WP_Query([
			'post_type' => $post_type,
			'posts_per_page' => 1,
			'p' => $reference_post
		]);
		if ( $pq->have_posts() ) :
			$parent = $pq->posts[0]->post_parent;
			$menu_order = $pq->posts[0]->menu_order;
		endif; wp_reset_postdata();
		if ( $parent ){
			$data['parent_id'] = $parent;
			$new_posts = $this->createChildPosts($data);
		}

		// Reorder to match
		if ( $before && $menu_order > 0 ) $menu_order = $menu_order - 1;
		if ( !$before ) $menu_order = $menu_order + 1;

		// Loop through new child posts and set new order for them
		$new_post_count = count($new_posts);


		$sql = ( $before ) 
			? ""
			: $wpdb->prepare("UPDATE $wpdb->posts SET menu_order = menu_order+%i WHERE post_parent = %i AND (post_status = 'publish' OR post_status = 'draft') AND (post_type = '%s') AND (menu_order >= %i) ORDER BY menu_order;", $new_post_count, $parent, $post_type, $reference_post);

		// If insert before or after, Reorder all posts after the new ones starting at the count of the new posts

		return wp_send_json(['status' => 'error', 'message' => $menu_order, 'before' => $before, 'reference' => $reference_post, 'new_posts' => $new_posts]);
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
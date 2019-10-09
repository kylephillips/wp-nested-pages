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
		$post_type = sanitize_text_field($data['post_type']);

		// Set the initial menu order
		$pq = new \WP_Query([
			'post_type' => $post_type,
			'post_parent' => sanitize_text_field($data['parent_id']),
			'posts_per_page' => -1,
			'fields' => 'ids'
		]);
		$menu_order = ( $pq->have_posts() ) ? count($pq->posts) : 0;
		wp_reset_postdata();

		foreach($data['post_title'] as $key => $title){
			$post = [
				'post_title' => sanitize_text_field($title),
				'post_status' => sanitize_text_field($data['_status']),
				'post_author' => sanitize_text_field($data['post_author']),
				'post_parent' => sanitize_text_field($data['parent_id']),
				'post_type' => $post_type,
				'menu_order' => $menu_order
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
		global $wpdb;
		$menu_order = 0;
		$parent = false;
		$post_type = sanitize_text_field($data['post_type']);
		$after = ( isset($data['after_id']) && $data['after_id'] !== '' ) ? true : false;
		$reference_post = ( $after ) ? intval($data['after_id']) : intval($data['before_id']);

		// Get the source post, so the reference points for menu order can be determined
		$pq = new \WP_Query([
			'post_type' => $post_type,
			'posts_per_page' => 1,
			'p' => $reference_post
		]);
		if ( $pq->have_posts() ) :
			$parent = intval($pq->posts[0]->post_parent);
			$menu_order = $pq->posts[0]->menu_order; 
		endif; wp_reset_postdata();

		if ( $parent ) $data['parent_id'] = $parent;
		$new_posts = $this->createChildPosts($data);

		if ( $after ) $menu_order = $menu_order + 1;
		$new_post_count = count($new_posts);
		$first_new_id = $new_posts[0]['id'];
		$last_new_id = $new_posts[count($new_posts) - 1]['id'];

		$sql = "UPDATE `$wpdb->posts` SET menu_order = menu_order+%d WHERE post_parent = %d AND (post_status = 'publish' OR post_status = 'draft') AND (post_type = '%s'";
		if ( $post_type == 'page' ) $sql .= " OR post_type = 'np-redirect'";
		$sql .= ") AND (menu_order >= %d) ORDER BY menu_order;";

		// Reorder All posts after the new ones
		$wpdb->query($wpdb->prepare($sql, [$new_post_count, $parent, $post_type, $menu_order]));
		// Reorder the new posts menu_order
		$wpdb->query($wpdb->prepare("SET @start_order := %d;", [$menu_order-1]));
		$wpdb->query($wpdb->prepare("UPDATE `$wpdb->posts` SET menu_order = (@start_order:=@start_order+1) WHERE post_parent = %d AND (post_type = '%s') AND (ID BETWEEN %d AND %d) ORDER BY menu_order;", [$parent, $post_type, $first_new_id, $last_new_id]));

		return $new_posts;
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
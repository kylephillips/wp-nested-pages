<?php namespace NestedPages\Entities\Post;

use NestedPages\Entities\User\UserRepository;
use NestedPages\Entities\NavMenu\NavMenuSyncListing;

/**
* WP Actions tied to a Post
*/
class PostActions {

	/**
	* User Repository
	* @var object
	*/
	private $user_repo;

	public function __construct()
	{
		$this->user_repo = new UserRepository;
		add_action( 'trashed_post', array( $this, 'trashHook' ) );
	}

	
	/**
	* Trash hook - make sure child pages of trashed page are visible
	*/
	public function trashHook($post_id)
	{
		$post_type = get_post_type($post_id);
		if ( ($post_type == 'page') || ($post_type == 'np-redirect') ) {
			$post_type = 'page';
			$sync = new NavMenuSyncListing;
			$sync->sync();
		}
		$this->resetToggles($post_id, $post_type);
	}


	/**
	* Make sure children of trashed pages are viewable in Nested Pages
	*/
	private function resetToggles($post_id, $post_type)
	{
		$visible_pages = unserialize(get_user_meta(get_current_user_id(), 'np_visible_posts', true));
		$visible_pages = $visible_pages[$post_type];
		
		$child_pages = array();
		
		$children = new \WP_Query(array('post_type'=>$post_type, 'posts_per_page'=>-1, 'post_parent'=>$post_id));
		if ( $children->have_posts() ) : while ( $children->have_posts() ) : $children->the_post();
			array_push($child_pages, get_the_id());
		endwhile; endif; wp_reset_postdata();
		
		foreach($child_pages as $child_page){
			if ( !in_array($child_page, $visible_pages) ) array_push($visible_pages, $child_page);
		}

		$this->user_repo->updateVisiblePages($post_type, $visible_pages);
	}

}
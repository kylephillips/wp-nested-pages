<?php namespace NestedPages\Entities\Post;
/**
* WP Actions tied to a Post
*/
class PostActions {

	public function __construct()
	{
		add_action( 'trashed_post', array( $this, 'trashHook' ) );
	}

	
	/**
	* Trash hook - make sure child pages of trashed page are visible
	*/
	public function trashHook($post_id)
	{
		$post_type = get_post_type($post_id);
		if ( $post_type == 'page' ) $this->resetToggles($post_id);
	}


	/**
	* Make sure children of trashed pages are viewable in Nested Pages
	*/
	private function resetToggles($post_id)
	{
		$visible_pages = unserialize(get_user_meta(get_current_user_id(), 'np_visible_pages', true));
		$child_pages = array();
		$children = new \WP_Query(array('post_type'=>'page', 'posts_per_page'=>-1, 'post_parent'=>$post_id));
		if ( $children->have_posts() ) : while ( $children->have_posts() ) : $children->the_post();
			array_push($child_pages, get_the_id());
		endwhile; endif; wp_reset_postdata();
		foreach($child_pages as $child_page){
			if ( !in_array($child_page, $visible_pages) ) array_push($visible_pages, $child_page);
		}
		update_user_meta(get_current_user_id(), 'np_visible_pages', serialize($visible_pages));
	}

}
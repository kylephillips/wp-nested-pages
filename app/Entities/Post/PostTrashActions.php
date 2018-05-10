<?php 
namespace NestedPages\Entities\Post;

use NestedPages\Entities\User\UserRepository;
use NestedPages\Entities\NavMenu\NavMenuSyncListing;
use NestedPages\Entities\NavMenu\NavMenuRemoveItem;
use NestedPages\Entities\NavMenu\NavMenuRepository;

/**
* WP Actions tied to a Post
*/
class PostTrashActions 
{
	/**
	* User Repository
	* @var object
	*/
	private $user_repo;

	/**
	* Nav Menu Repository
	*/
	private $nav_menu_repo;

	public function __construct()
	{
		$this->user_repo = new UserRepository;
		$this->nav_menu_repo = new NavMenuRepository;
		add_action( 'trashed_post', [$this, 'trashHook']);
		add_action( 'delete_post', [$this, 'removeLinkNavItem'], 10 );
	}
	
	/**
	* Trash hook - make sure child pages of trashed page are visible
	* @since 1.3.4
	*/
	public function trashHook($post_id)
	{
		$post_type = get_post_type($post_id);
		$this->resetToggles($post_id, $post_type);
		if ( get_option('nestedpages_menusync') !== 'sync' ) return;
		$this->removeNavMenuItem($post_id);
		if ( $post_type == 'page' ){
			$sync = new NavMenuSyncListing;
			$sync->sync();
		}
	}

	/**
	* Link Post Types are immediately deleted, so trash hook isn't called
	* Must remove nav menu items when they're deleted
	*/
	public function removeLinkNavItem($post_id)
	{
		$post_type = get_post_type($post_id);
		if ( $post_type == 'nav_menu_item' ) return;
		if ( $post_type == 'np-redirect' ) $this->removeNavMenuItem($post_id);
	}

	/**
	* Remove the nav menu item
	*/
	private function removeNavMenuItem($post_id)
	{
		$query_type = ( get_post_type($post_id) == 'np-redirect' ) ? 'xfn' : 'object_id';
		$nav_item_id = $this->nav_menu_repo->getMenuItem($post_id, $query_type);
		if ( $nav_item_id ) new NavMenuRemoveItem($nav_item_id);
	}

	/**
	* Make sure children of trashed pages are viewable in Nested Pages
	*/
	private function resetToggles($post_id, $post_type)
	{
		$visible_pages = $this->user_repo->getVisiblePages();
		if ( !isset($visible_pages[$post_type]) ) return;
		$visible_pages = $visible_pages[$post_type];

		if ( !isset($visible_pages[$post_type]) ) return;

		$child_pages = [];
		
		$children = new \WP_Query(['post_type'=>$post_type, 'posts_per_page'=>-1, 'post_parent'=>$post_id]);
		if ( $children->have_posts() ) : while ( $children->have_posts() ) : $children->the_post();
			array_push($child_pages, get_the_id());
		endwhile; endif; wp_reset_postdata();
		
		foreach($child_pages as $child_page){
			if ( !in_array($child_page, $visible_pages) ) array_push($visible_pages, $child_page);
		}

		$this->user_repo->updateVisiblePages($post_type, $visible_pages);
	}
}
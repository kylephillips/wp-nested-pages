<?php 
namespace NestedPages;

/**
* Page Redirects in admin
*/
class Redirects 
{
	public function __construct()
	{
		add_action('load-edit.php', [$this, 'pageTrashed']);
		add_action('load-edit.php', [$this, 'pageRestored']);
		add_action('deleted_post', [$this, 'linkDeleted'], 10, 1);
	}

	/**
	* Redirect back to nested pages after pages moved to trashed
	*/
	public function pageTrashed()
	{	
		$screen = get_current_screen();
		if (
			($screen->id == 'edit-page')   &&
			(isset($_GET['trashed']))      &&
			(intval($_GET['trashed']) > 0) &&
			(!isset($_GET['bulk'])) &&
			$this->arePagesNested()
		){
			$query_args = array(
				'page' => 'nestedpages',
				'trashed' => true
			);
			if ( isset($_GET['ids']) ) $query_args['ids'] = urlencode($_GET['ids']);
			$redirect = add_query_arg(['page'=>'nestedpages', 'trashed' => true ]);
			wp_redirect($redirect);
			exit();
		}
	}

	/**
	* Redirect to nested pages after page moved out of trash
	*/
	public function pageRestored()
	{
		$screen = get_current_screen();
		if (
			($screen->id == 'edit-page')     &&
			(isset($_GET['untrashed']))      &&
			(intval($_GET['untrashed']) > 0) &&
			(!isset($_GET['bulk'])) &&
			$this->arePagesNested()
		){
			$redirect = add_query_arg(['page'=>'nestedpages', 'untrashed' => true, 'untrashed' => urlencode($_GET['untrashed']) ]);
			wp_redirect($redirect);
			exit();
		}
	}

	/**
	* Add link trashed param to URL after delete (for notification)
	*/
	public function linkDeleted($post_id)
	{
		$screen = get_current_screen();
		if ( !$screen ) return;
		if (
			(get_post_type($post_id) == 'np-redirect') &&
			($screen->id == 'np-redirect')             &&
			$this->arePagesNested()
		){
			$post_type = ( isset($_GET['parent_post_type']) && $_GET['parent_post_type'] !== '' )
				? sanitize_text_field($_GET['parent_post_type']) : null;
			if ( $post_type ) $page = 'nestedpages-' . $post_type;

			$redirect = ( $post_type ) 
				? add_query_arg(['page' => $page, 'linkdeleted' => true, '_wpnonce' => false, 'post' => false, 'action' => false])
				: add_query_arg(['page' => 'nestedpages', 'linkdeleted' => true, '_wpnonce' => false, 'post' => false, 'action' => false]);

			wp_redirect($redirect);
			exit();
		}
	}

	/**
	* Return true/false if nested pages are enabled for Page post types
	*/
	private function arePagesNested() {
		$postTypeRepository = new \NestedPages\Entities\PostType\PostTypeRepository();
		$enabledPostTypes   = $postTypeRepository->enabledPostTypes();
		return array_key_exists( 'page', $enabledPostTypes );
	}
}
<?php 
namespace NestedPages\Form\Listeners;

/**
* Trash a post and all of its children
*/
class TrashWithChildren extends BaseHandler
{
	/**
	* Original Post ID
	*/
	private $parent_id;

	/**
	* The Post Type
	*/
	private $post_type;

	/**
	* The Redirect URL
	*/
	private $redirect;

	/**
	* Post IDs to Trash
	* @var array
	*/
	private $trash_ids = [];

	public function __construct()
	{
		parent::__construct();
		$this->setParentId();
		$this->getAllPosts($this->parent_id);
		$this->trashPosts();
		$this->setRedirect();
		$this->response = [
			'status' => 'success',
			'redirect' => $this->redirect
		];
		$this->sendResponse();
	}

	/**
	* Set the Post ID to Clone
	*/ 
	private function setParentId()
	{
		if ( !isset($_POST['post_id']) ){
			return $this->sendResponse(['status' => 'error', 'message' => __('Post Not Found', 'wp-nested-pages')]);
		}
		if ( !isset($_POST['screen']) ){
			return $this->sendResponse(['status' => 'error', 'message' => __('Current page could not be determined', 'wp-nested-pages')]);
		}
		$this->parent_id = intval(sanitize_text_field($_POST['post_id']));
		$this->post_type = get_post_type($this->parent_id);
		$this->trash_ids[] = $this->parent_id;
	}

	/**
	* Trash the post and its children
	*/
	private function getAllPosts($parent)
	{
		$q = new \WP_Query([
			'post_type' => $this->post_type,
			'posts_per_page' => -1,
			'post_parent' => $parent,
			'fields' => 'ids'
		]);
		$posts = [];
		if ( $q->have_posts() ) :
			$this->trash_ids = array_merge($this->trash_ids, $q->posts);
			while ( $q->have_posts() ) : $q->the_post();
				$this->getAllPosts(get_the_id());
			endwhile;
		endif; wp_reset_postdata();
	}

	/**
	* Trash the Posts
	*/
	private function trashPosts()
	{
		foreach ( $this->trash_ids as $post_id ){
			wp_trash_post($post_id);
		}
	}

	/**
	* Set the URL to redirect to
	*/
	private function setRedirect()
	{
		$current_screen = sanitize_text_field($_POST['screen']);
		$url = ( $current_screen == 'nestedpages' ) ? 'admin.php' : 'edit.php';
		$url .= '?page=' . $current_screen . '&trashed=1&ids=';
		foreach ( $this->trash_ids as $key => $id ){
			$url .= $id;
			if ( ($key +1) < count($this->trash_ids) ) $url .= ',';
		}
		$this->redirect = admin_url($url);
	}
}
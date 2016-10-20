<?php
namespace NestedPages;

/**
* Fixes issue when pages are nested under non-page post types (np-redirect)
*/
class RedirectsFrontEnd
{
	public function __construct()
	{
		// add_filter('page_link', array($this, 'pageLinks'),10,2);
		// add_action('parse_request', array($this, 'parseRequest'));
	}

	/**
	* Remove np-redirect slugs from links
	*/
	public function pageLinks( $post_link, $id = 0 )
	{
		$post = get_post($id);
		$post_link = $this->removeParentSlugs($post, $post_link);
		return $post_link;
	}

	public function parseRequest($wp)
	{

		if ( isset($wp->query_vars['error']) ) $slug = basename($wp->request);
		if ( isset($wp->query_vars['pagename']) && ! empty($wp->query_vars['pagename']) ) $slug = $wp->query_vars['pagename'];
		if ( isset($wp->query_vars['name']) && ! empty($wp->query_vars['name']) ) $slug = $wp->query_vars['name'];
		if ( isset($wp->query_vars['attachment']) && ! empty($wp->query_vars['attachment']) ) $slug = $wp->query_vars['attachment'];
		if ( !isset($slug) ) return;		

		$segments = explode('/', $slug);
		$slug = basename($slug);

		if ( count($segments) > 1 ){
			$parent_slug = $segments[count($segments) - 2];
			$parent_post = get_posts(array(
				'name' => $parent_slug,
				'post_type' => 'any',
				'posts_per_page' => 1
			));
		}

		$page_args = array(
			'name' => $slug, 
			'post_type' => 'any', 
			'posts_per_page' => 1
		);
		$page_args['post_parent'] = ( isset($parent_post) ) ? $parent_post[0]->ID : 0;
		
		$page = get_posts($page_args);
		if ( !$page ) return;

		$parent_type = get_post_type($page[0]->post_parent);

		if ( $parent_type !== 'np-redirect' && !isset($wp->query_vars['attachment']) && $parent_type !== 'page' ) return;
		
		unset($wp->query_vars['pagename']);
		unset($wp->query_vars['name']);
		unset($wp->query_vars['attachment']);
		unset($wp->query_vars['error']);
		
		$wp->query_vars['page_id'] = $page[0]->ID;
	}

	/**
	* Recursive function removes non-page parent slugs
	*/
	private function removeParentSlugs($post, $slug)
	{
		if ( is_admin() ) return $slug;
		if ( $post->post_parent > 0 ) {
			$parent_post = get_post($post->post_parent);
			if ( $parent_post->post_type == 'np-redirect' ){
				$slug = str_replace($parent_post->post_name . '/', '', $slug);
			}
			return $this->removeParentSlugs($parent_post, $slug);
		}
		return $slug;
	}
}

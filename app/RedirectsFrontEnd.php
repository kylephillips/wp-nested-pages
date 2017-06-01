<?php
namespace NestedPages;

/**
* Fixes issue when pages are nested under non-page post types (np-redirect)
*/
class RedirectsFrontEnd
{
	public function __construct()
	{
		add_filter('page_link', array($this, 'pageLinks'),10,2);
		add_action('parse_request', array($this, 'parseRequest'));
	}

	/**
	* Remove np-redirect slugs from links
	*/
	public function pageLinks( $post_link, $id = 0 )
	{
		$post = get_post($id);
		$post_link = $this->renameLinkSlugs($post, $post_link);
		return $post_link;
	}

	public function parseRequest($wp)
	{
		if ( isset($wp->query_vars['error']) ) $slug = basename($wp->request);
		if ( isset($wp->query_vars['pagename']) && ! empty($wp->query_vars['pagename']) ) $slug = $wp->query_vars['pagename'];
		if ( isset($wp->query_vars['name']) && ! empty($wp->query_vars['name']) ) $slug = $wp->query_vars['name'];
		if ( isset($wp->query_vars['attachment']) && ! empty($wp->query_vars['attachment']) ) $slug = $wp->query_vars['attachment'];
		if ( !isset($slug) ) return;	

		$segments = explode('/', $wp->request);
		$slug = basename($slug);

		// If this is a redirect link, strip out the np-r and go to the original
		if ( substr($slug, -4) == 'np-r' ){
			$slug = substr($slug, 0, -5);
			$wp->request = $slug;
			$wp->query_vars['pagename'] = $slug;
			$wp->query_vars['name'] = $slug;
			return;
		};

		$redirect = false;
		if ( count($segments) == 1 ) return;
		
		$parent_slug = $segments[count($segments) - 2];
		if ( substr($parent_slug, -4) == 'np-r' ){
			$redirect = true;
			$parent_slug = substr($parent_slug, 0, -5);
		}
		$parent_args = array(
			'name' => sanitize_text_field($parent_slug),
			'posts_per_page' => 1
		);
		$parent_args['post_type'] = ( $redirect ) ? 'np-redirect' : 'any';
		$parent_post = get_posts($parent_args);

		$page_args = array(
			'name' => sanitize_text_field($slug), 
			'post_type' => 'any', 
			'posts_per_page' => 1
		);
		$page_args['post_parent'] = ( isset($parent_post) && $redirect ) ? $parent_post[0]->ID : null;
		$page = get_posts($page_args);
		if ( !$page ) return;
		
		unset($wp->query_vars['attachment']);
		unset($wp->query_vars['error']);
		
		$wp->query_vars['page_id'] = $page[0]->ID;
	}

	/**
	* Recursive function removes non-page parent slugs
	*/
	private function renameLinkSlugs($post, $slug)
	{
		if ( is_admin() ) return $slug;
		if ( $post->post_parent > 0 ) {
			$parent_post = get_post($post->post_parent);
			if ( $parent_post->post_type == 'np-redirect' ){
				$slug = str_replace($parent_post->post_name . '/', $parent_post->post_name . '-np-r/', $slug);
			}
			return $this->renameLinkSlugs($parent_post, $slug);
		}
		return $slug;
	}
}
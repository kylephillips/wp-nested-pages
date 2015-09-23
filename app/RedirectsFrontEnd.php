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
		add_action('request', array($this, 'request'));
	}

	public function pageLinks( $post_link, $id = 0 )
	{
		$post = get_post($id);
		$post_link = $this->removeParentSlugs($post, $post_link);
		return $post_link;
	}

	/**
	* Recursive function removes non-page parent slugs
	*/
	private function removeParentSlugs($post, $slug)
	{
		$parent_type = get_post_type($post->post_parent);
		if ( $parent_type == 'np-redirect' ){
			$parent_post = get_post($post->post_parent);
			$slug = str_replace($parent_post->post_name . '/', '', $slug);
			return $this->removeParentSlugs($parent_post, $slug);
		}
		return $slug;
	}

	/**
	* Add page query
	*/
	public function request($request)
	{
		if ( !isset($request['name']) ) return $request;
		$slug = $request['name'];
		$dpost = get_posts(array('name' => $slug, 'post_type' => 'page'));
		if ( $dpost && $dpost[0]->post_type == 'page' ){
			add_filter('pre_get_posts', array($this, 'query'));
		}		
		return $request;
	}

	/**
	* Set the query to page
	*/
	public function query($query)
	{ 
		if (!$query->is_main_query()) return;
		$query->set('post_type', 'page');
	}

}
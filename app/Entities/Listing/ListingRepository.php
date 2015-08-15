<?php namespace NestedPages\Entities\Listing;

class ListingRepository {

	/**
	* User's Toggled Pages
	*/
	public function visiblePages($post_type)
	{
		$visible = unserialize(get_user_meta(get_current_user_id(), 'np_visible_posts', true));
		if ( !isset($visible[$post_type]) ) $visible[$post_type] = array();
		return $visible[$post_type];
	}

	/**
	* Taxonomies
	*/
	public function taxonomies()
	{
		$taxonomies = get_taxonomies(array(
			'public' => true,
		), 'objects');
		return $taxonomies;
	}

	/**
	* Get all non-empty Terms for a given taxonomy
	*/
	public function terms($taxonomy)
	{
		return get_terms($taxonomy);
	}

}
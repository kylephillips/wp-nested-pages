<?php namespace NestedPages\Entities\Listing;

class ListingRepository {

	/**
	* User's Toggled Pages
	*/
	public function visiblePages()
	{
		$visible = unserialize(get_user_meta(get_current_user_id(), 'np_visible_pages', true));
		if ( !$visible ) $visible = array();
		return $visible;
	}

}
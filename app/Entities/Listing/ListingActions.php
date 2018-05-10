<?php
namespace NestedPages\Entities\Listing;

class ListingActions 
{
	public function __construct()
	{
		add_filter('posts_where', [$this, 'titleSearch'], 10, 2 );
	}

	/**
	* For performing search query on titles
	*/
	public function titleSearch( $where, $wp_query )
	{
		global $wpdb;
		if ( $post_title_like = $wp_query->get( 'post_title_like' ) ){
			$like = $wpdb->esc_like( $post_title_like );
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $like ) . '%\'';
		}
		return $where;
	}
}
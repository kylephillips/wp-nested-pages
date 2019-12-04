<?php
namespace NestedPages\Entities\Post;

/**
* Enables Private Posts/Pages in the Post Edit Dropdown
* Fixes bug in WP Core where child posts of a private/draft post become un-nested when editing
* @link https://core.trac.wordpress.org/ticket/8592#comment:129
*/
class PrivatePostParent
{
	public function __construct()
	{
		add_filter('wp_dropdown_pages', [$this, 'metabox'], 10, 3);
	}

	public function metabox($output, $arguments = [], $pages = [])
	{
		global $post;
		if ( !$post ) return $output;
		if ( !isset($arguments['post_type']) ) return $output;
		if ( !isset($arguments['name']) ) return $output;
		if ( $arguments['post_type'] !== $post->post_type ) return $output;
		if ( $arguments['name'] !== 'parent_id' ) return $output;

		$args = [
			'post_type'	=> $post->post_type,
			'exclude_tree' => $post->ID,
			'selected' => $post->post_parent,
			'name' => 'parent_id',
			'show_option_none' => __('(no parent)'),
			'sort_column' => 'menu_order, post_title',
			'echo' => 0,
			'post_status' => ['publish', 'private', 'draft'],
		];

		$defaults = [
			'depth'	=> 0,
			'child_of' => 0,
			'selected' => 0,
			'echo' => 1,
			'name' => 'page_id',
			'id' => '',
			'show_option_none' => '',
			'show_option_no_change'	=> '',
			'option_none_value'	=> '',
		];
		
		$r = wp_parse_args($args, $defaults);
		extract($r, EXTR_SKIP);

		$pages = get_pages($r);
		$name = esc_attr($name);
	
		// Back-compat with old system where both id and name were based on $name argument
		if (empty($id)) $id = $name;

		if (empty($pages)) return;

		$output = "<select name=\"$name\" id=\"$id\">\n";

		if ($show_option_no_change)	$output .= "\t<option value=\"-1\">$show_option_no_change</option>";
		if ($show_option_none) $output .= "\t<option value=\"" . esc_attr($option_none_value) . "\">$show_option_none</option>\n";
		$output .= walk_page_dropdown_tree($pages, $depth, $r);
		$output .= "</select>\n";

		return $output;
	}
}
<?php namespace NestedPages\Entities\Post;


/**
* Build Post Data Object
*/
class PostDataFactory {

	/**
	* Post Data
	* @var object
	*/
	private $post_data;

	/**
	* Build the Object
	*/
	public function build($post)
	{
		$this->post_data = new \stdClass();
		$this->addPostVars($post);
		$this->addPostMeta($post);
		$this->addMenuOptions($post);
		$this->addDate($post);
		$this->author($post);

		return $this->post_data;
	}

	/**
	* Post Items
	*/
	public function addPostVars($post)
	{
		$this->post_data->id = $post->ID;
		$this->post_data->parent_id = $post->post_parent;
		$this->post_data->title = $post->post_title;
		$this->post_data->template = get_post_meta($post->ID, '_wp_page_template', true);
		$this->post_data->password = $post->post_password;
		$this->post_data->status = $post->post_status;
		$this->post_data->type = $post->post_type;
		$this->post_data->comment_status = $post->comment_status;
		$this->post_data->content = $post->post_content;
		$this->post_data->hierarchical = is_post_type_hierarchical($post->post_type);
		$this->post_data->link = get_the_permalink($post->ID);
	}

	/**
	* Post Meta
	*/
	public function addPostMeta($post)
	{
		// Hidden in Nested Pages?
		$np_status = get_post_meta( $post->ID, 'nested_pages_status', true );
		$this->post_data->np_status = ( $np_status == 'hide' ) ? 'hide' : 'show';

		// Yoast Score
		if ( function_exists('wpseo_auto_load') ) {
			$yoast_score = get_post_meta($post->ID, '_yoast_wpseo_linkdex', true);
			$this->post_data->score = \WPSEO_Utils::translate_score($yoast_score);
		};
	}

	/**
	* Menu Options
	*/
	private function addMenuOptions($post)
	{
		$this->post_data->nav_title = get_post_meta($post->ID, 'np_nav_title', true);
		$this->post_data->link_target = get_post_meta($post->ID, 'np_link_target', true);
		$this->post_data->nav_title_attr = get_post_meta($post->ID, 'np_title_attribute', true);
		$this->post_data->nav_css = get_post_meta($post->ID, 'np_nav_css_classes', true);
		$nav_status = get_post_meta( $post->ID, 'np_nav_status', true);
		$this->post_data->nav_status = ( $nav_status == 'hide' ) ? 'hide' : 'show';
	}

	/**
	* Date Vars
	*/
	private function addDate($post)
	{
		$this->post_data->date = new \stdClass();
		$this->post_data->date->d = get_the_time('d', $post->ID);
		$this->post_data->date->month = get_the_time('m', $post->ID);
		$this->post_data->date->y = get_the_time('Y', $post->ID);
		$this->post_data->date->h = get_the_time('H', $post->ID);
		$this->post_data->date->m = get_the_time('i', $post->ID);
		$this->post_data->date->datepicker = get_the_time('U', $post->ID);
	}

	/**
	* Add Author Info
	*/
	private function author($post)
	{
		$this->post_data->author = get_the_author_meta('display_name', $post->post_author);
		$this->post_data->author_link = admin_url('edit.php?post_type=' . $post->post_type . '&author=' . $post->post_author);
	}

}
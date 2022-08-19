<?php 
namespace NestedPages\Entities\Post;

use NestedPages\Entities\PluginIntegration\IntegrationFactory;

/**
* Build Post Data Object
*/
class PostDataFactory 
{
	/**
	* Post Data
	* @var object
	*/
	private $post_data;

	/**
	* Plugin Integrations
	* @var object
	*/
	private $integrations;

	/**
	* Build the Object
	*/
	public function build($post, $h_taxonomies = null, $f_taxonomies = null)
	{
		if ( is_int($post) ) $post = get_post($post);
		$this->integrations = new IntegrationFactory;
		$this->post_data = new \WP_Post($post);
		$this->addPostVars($post);
		$this->addPostMeta($post);
		$this->addOriginalLink($post);
		$this->addDate($post);
		$this->author($post);
		if ( $h_taxonomies || $f_taxonomies ) $this->addTaxonomies($post, $h_taxonomies, $f_taxonomies);
		return $this->post_data;
	}

	/**
	* Post Items
	*/
	public function addPostVars($post)
	{
		$this->post_data->id = $post->ID;
		$this->post_data->ID = $post->ID;
		$this->post_data->parent_id = $post->post_parent;
		$this->post_data->title = $post->post_title;
		$this->post_data->password = $post->post_password;
		$this->post_data->status = $post->post_status;
		$this->post_data->type = $post->post_type;
		$this->post_data->comment_status = $post->comment_status;
		$this->post_data->content = $post->post_content;
		$this->post_data->hierarchical = is_post_type_hierarchical($post->post_type);
		$this->post_data->post_type = $post->post_type;
		$this->post_data->link = get_the_permalink($post->ID);
	}

	/**
	* Post Meta
	*/
	public function addPostMeta($post)
	{
		$meta = get_metadata('post', $post->ID);
		$this->post_data->meta = $meta;
		$this->post_data->nav_title = ( isset($meta['_np_nav_title'][0]) ) ? $meta['_np_nav_title'][0] : null;
		$this->post_data->link_target = ( isset($meta['_np_link_target'][0]) ) ? $meta['_np_link_target'][0] : null;
		$this->post_data->nav_title_attr = ( isset($meta['_np_title_attribute'][0]) ) ? $meta['_np_title_attribute'][0] : null;
		$this->post_data->nav_css = ( isset($meta['_np_nav_css_classes'][0]) ) ? $meta['_np_nav_css_classes'][0] : null;
		$this->post_data->nav_object = ( isset($meta['_np_nav_menu_item_object'][0]) ) ? $meta['_np_nav_menu_item_object'][0] : null;
		$this->post_data->nav_object_id = ( isset($meta['_np_nav_menu_item_object_id'][0]) ) ? $meta['_np_nav_menu_item_object_id'][0] : null;
		$this->post_data->nav_type = ( isset($meta['_np_nav_menu_item_type'][0]) ) ? $meta['_np_nav_menu_item_type'][0] : null;
		$this->post_data->nav_status = ( isset($meta['_np_nav_status'][0]) && $meta['_np_nav_status'][0] == 'hide' ) ? 'hide' : 'show';
		$this->post_data->np_status = ( isset($meta['_nested_pages_status'][0]) && $meta['_nested_pages_status'][0] == 'hide' ) ? 'hide' : 'show';
		$this->post_data->nav_custom_url = ( isset($meta['_np_nav_custom_url'][0]) && $meta['_np_nav_custom_url'][0] !== '' ) ? $meta['_np_nav_custom_url'][0] : null;
		$this->post_data->template = ( isset($meta['_wp_page_template'][0]) ) ? $meta['_wp_page_template'][0] : false;

		// Yoast Score
		if ( $this->integrations->plugins->yoast->installed ) {
			$this->post_data->score = $this->integrations->plugins->yoast->getScore($post->ID);
		}
	}

	/**
	* Add original item/link to link
	*/
	private function addOriginalLink($post)
	{
		if ( $post->post_type !== 'np-redirect' ) {
			$this->post_data->nav_original_link = null;
			$this->post_data->nav_original_type = null;
			return;
		}

		if ( $this->post_data->nav_type && $this->post_data->nav_type == 'taxonomy' ){
			$term = get_term_by('id', $this->post_data->nav_object_id, $this->post_data->nav_object);
			$this->post_data->nav_original_link = get_term_link($term);
			$this->post_data->nav_original_title = $term->name;
			return;
		}

		if ( $this->post_data->nav_type && $this->post_data->nav_type == 'post_type_archive' ){
			$post_type = get_post_type_object($this->post_data->nav_object);
			$this->post_data->nav_original_link = get_post_type_archive_link($this->post_data->nav_object);
			$this->post_data->nav_original_title = sprintf(__('%s (Archive)', 'wp-nested-pages'), $post_type->labels->name);
			return;
		}

		$id = $this->post_data->nav_object_id;
		$this->post_data->nav_original_link = get_the_permalink($id);
		$this->post_data->nav_original_title = get_the_title($id);
	}

	/**
	* Date Vars
	*/
	private function addDate($post)
	{
		$this->post_data->date = new \stdClass();
		$time = get_the_time('U', $post->ID);
		$this->post_data->date->d = date('d', $time);
		$this->post_data->date->month = date('m', $time);
		$this->post_data->date->y = date('Y', $time);
		$this->post_data->date->h = date('H', $time);
		$this->post_data->date->m = date('i', $time);
		$this->post_data->date->datepicker = $time;
	}

	/**
	* Add Author Info
	*/
	private function author($post)
	{
		$this->post_data->author = get_the_author_meta('display_name', $post->post_author);
		$this->post_data->author_link = admin_url('edit.php?post_type=' . $post->post_type . '&author=' . $post->post_author);
	}

	/**
	* Add taxonomies
	*/
	public function addTaxonomies($post, $h_taxonomies, $f_taxonomies)
	{
		// Add taxonomies
		if ( count($h_taxonomies) > 0 ) {
			foreach($h_taxonomies as $tax){
				$taxname = $tax->name;
				if ( !isset($post->$taxname) ) continue;
				$this->post_data->$taxname = explode(',', $post->$taxname);
			}
		}
		if ( count($f_taxonomies) > 0 ) {
			foreach($f_taxonomies as $tax){
				$taxname = $tax->name;
				if ( !isset($post->$taxname) ) continue;
				$this->post_data->$taxname = explode(',', $post->$taxname);
			}
		}
	}
}
<?php

require_once('class-np-validation.php');

class NP_PostRepository {

	/**
	* Validation Class
	* @var object NP_Validation
	*/
	protected $validation;


	/**
	* New Post ID
	*/
	protected $new_id;


	public function __construct()
	{
		$this->validation = new NP_Validation;
	}


	/**
	* Update Order
	* @param array posts
	* @param int parent
	* @since 1.0
	*/
	public function updateOrder($posts, $parent = 0)
	{
		$this->validation->validatePostIDs($posts);
		foreach( $posts as $key => $post )
		{
			wp_update_post(array(
				'ID' => sanitize_text_field($post['id']),
				'menu_order' => $key,
				'post_parent' => $parent
			));

			if ( isset($post['children']) ){
				$this->updateOrder($post['children'], $post['id']);
			}
		}
		return true;
	}


	/**
	* Update Post
	* @param array data
	* @since 1.0
	*/
	public function updatePost($data)
	{
		$this->validation->checkEmpty($data['post_title'], __('Title', 'nestedpages'));
		$date = $this->validation->validateDate($data);
		if ( !isset($_POST['comment_status']) ) $data['comment_status'] = 'closed';

		if ( isset($_POST['keep_private']) && $_POST['keep_private'] == 'private' ){
			$status = 'private';
		} else {
			$status = ( isset($data['_status']) ) ? sanitize_text_field($data['_status']) : 'publish';
		}

		$updated_post = array(
			'ID' => sanitize_text_field($data['post_id']),
			'post_title' => sanitize_text_field($data['post_title']),
			'post_author' => sanitize_text_field($data['post_author']),
			'post_name' => sanitize_text_field($data['post_name']),
			'post_date' => $date,
			'comment_status' => sanitize_text_field($data['comment_status']),
			'post_status' => $status,
			'post_password' => sanitize_text_field($data['post_password'])
		);
		wp_update_post($updated_post);

		$this->updateTemplate($data);
		$this->updateNestedPagesStatus($data);

		// Taxonomies
		$this->updateCategories($data);
		$this->updateTaxonomies($data);

		// Menu Options
		$this->updateNavStatus($data);
		$this->updateNavTitle($data);
		$this->updateLinkTarget($data);
		$this->updateTitleAttribute($data);
		$this->updateNavCSS($data);

		return true;
	}


	/**
	* Update Page Template
	* @param array data
	* @since 1.0
	*/
	private function updateTemplate($data)
	{
		$template = sanitize_text_field($data['page_template']);
		update_post_meta( 
			$data['post_id'], 
			'_wp_page_template', 
			$template
		);
	}


	/**
	* Update Nav Status (show/hide in nav menu)
	* @since 1.0
	* @param array data
	*/
	private function updateNavStatus($data)
	{
		$status = ( isset($data['nav_status']) ) ? 'hide' : 'show';
		$id = ( isset($data['post_id']) ) ? $data['post_id'] : $this->new_id;
		update_post_meta( 
			$id, 
			'np_nav_status', 
			$status
		);
	}


	/**
	* Update Nested Pages Visibility (how/hide in Nested Pages interface)
	* @since 1.0
	* @param array data
	*/
	private function updateNestedPagesStatus($data)
	{
		$status = ( isset($data['nested_pages_status']) ) ? 'hide' : 'show';
		$id = ( isset($data['post_id']) ) ? $data['post_id'] : $this->new_id;
		update_post_meta( 
			$id, 
			'nested_pages_status', 
			$status
		);
	}


	/**
	* Update Nested Pages Menu Navigation Label
	* @since 1.0
	* @param array data
	*/
	private function updateNavTitle($data)
	{
		if ( isset($data['np_nav_title']) ){
			$title = sanitize_text_field($data['np_nav_title']);
			update_post_meta( 
				$data['post_id'], 
				'np_nav_title', 
				$title
			);
		}
	}


	/**
	* Update Nested Pages Menu Navigation CSS Classes
	* @since 1.0
	* @param array data
	*/
	private function updateNavCSS($data)
	{
		if ( isset($data['np_nav_css_classes']) ){
			$css_classes = sanitize_text_field($data['np_nav_css_classes']);
			update_post_meta( 
				$data['post_id'], 
				'np_nav_css_classes', 
				$css_classes
			);
		}
	}


	/**
	* Update Nested Pages Menu Title Attribute
	* @since 1.0
	* @param array data
	*/
	private function updateTitleAttribute($data)
	{
		if ( isset($data['np_title_attribute']) ){
			$title_attr = sanitize_text_field($data['np_title_attribute']);
			update_post_meta( 
				$data['post_id'], 
				'np_title_attribute', 
				$title_attr
			);
		}
	}


	/**
	* Update Categories
	* @since 1.0
	* @param array data
	*/
	private function updateCategories($data)
	{
		if ( isset($data['post_category']) )
		{
			$this->validation->validateIntegerArray($data['post_category']);
			$cats = array();
			foreach($data['post_category'] as $cat) {
				if ( $cat !== 0 ) $cats[] = (int) $cat;
			}
			wp_set_post_terms($data['post_id'], $cats, 'category');
		}
	}


	/**
	* Update Hierarchical Taxonomy Terms
	* @since 1.0
	* @param array data
	*/
	private function updateTaxonomies($data)
	{
		if ( isset($data['tax_input']) ) {
			foreach ( $data['tax_input'] as $taxonomy => $term_ids ){
				$tax = get_taxonomy($taxonomy);
				if ( $tax->hierarchical ){
					$this->validation->validateIntegerArray($term_ids);
					$this->updateHierarchicalTaxonomies($data, $taxonomy, $term_ids);
				} else {
					$this->updateFlatTaxonomy($data, $taxonomy, $term_ids);
				}
			}
		}
	}


	/**
	* Update Hierarchical Taxonomy Terms
	* @since 1.1.4
	* @param array data
	*/
	private function updateHierarchicalTaxonomies($data, $taxonomy, $term_ids)
	{
		$terms = array();
		foreach ( $term_ids as $term ){
			if ( $term !== 0 ) $terms[] = (int) $term;
		}
		wp_set_post_terms($data['post_id'], $terms, $taxonomy);
	}


	/**
	* Update Flat Taxonomy Terms
	* @since 1.1.4
	* @param array data
	*/
	private function updateFlatTaxonomy($data, $taxonomy, $terms)
	{
		$terms = explode(',', sanitize_text_field($terms));
		$new_terms = array();
		foreach($terms as $term)
		{
			if ( $term !== "" )	array_push($new_terms, $term);
		}
		wp_set_post_terms($data['post_id'], $new_terms, $taxonomy);
	}


	/**
	* Update Link Target for Redirects
	* @since 1.1
	* @param array data
	*/
	private function updateLinkTarget($data)
	{
		$link_target = ( isset($data['link_target']) ) ? "_blank" : "";
		$id = ( isset($data['post_id']) ) ? $data['post_id'] : $this->new_id;
		update_post_meta( 
			$id, 
			'np_link_target', 
			$link_target
		);
	}


	/**
	* Update a Redirect
	* @since 1.1
	* @param array data
	*/
	public function updateRedirect($data)
	{
		$this->validation->checkEmpty($data['post_title'], __('Label', 'nestedpages'));
		$updated_post = array(
			'ID' => sanitize_text_field($data['post_id']),
			'post_title' => sanitize_text_field($data['post_title']),
			'post_status' => sanitize_text_field($data['_status']),
			'post_content' => sanitize_text_field($data['post_content']),
			'post_parent' => sanitize_text_field($data['parent_id'])
		);
		wp_update_post($updated_post);

		$this->updateNavStatus($data);
		$this->updateNestedPagesStatus($data);
		$this->updateLinkTarget($data);
		$this->updateTitleAttribute($data);
		$this->updateNavCSS($data);

		return true;
	}


	/**
	* Save a new Redirect
	* @since 1.1
	* @param array data
	*/
	public function saveRedirect($data)
	{
		$this->validation->validateRedirect($data);
		$new_link = array(
			'post_title' => sanitize_text_field($data['np_link_title']),
			'post_status' => sanitize_text_field($data['_status']),
			'post_content' => sanitize_text_field($data['np_link_content']),
			'post_parent' => sanitize_text_field($data['parent_id']),
			'post_type' => 'np-redirect'
		);
		$this->new_id = wp_insert_post($new_link);

		$this->updateNavStatus($data);
		$this->updateNestedPagesStatus($data);
		$this->updateLinkTarget($data);
		return $this->new_id;
	}


	/**
	* Get count of hidden pages
	* @since 1.1.4
	*/
	public function getHiddenCount()
	{
		$hidden = new WP_Query(array(
			'post_type' => array('page', 'np-redirect'),
			'meta_key' => 'nested_pages_status',
			'meta_value' => 'hide',
			'perm' => 'readable'));
		return $hidden->found_posts;
	}


	/**
	* Get Trash Count (pages)
	* @since 1.1.4
	*/
	public function trashedPagesCount()
	{
		$trashed = new WP_Query(array('post_type'=>'page','post_status'=>'trash','posts_per_page'=>-1));
		return $trashed->found_posts;
	}


	/**
	* Get count of published posts
	* @param object $pages
	*/
	public function publishCount($pages)
	{
		$publish_count = 1;
		foreach ( $pages->posts as $p ){
			if ( $p->post_status !== 'trash' ) $publish_count++;
		}
		return $publish_count;
	}

}
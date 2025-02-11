<?php 
namespace NestedPages\Entities\Post;

use NestedPages\Form\Validation\Validation;
use NestedPages\Entities\NavMenu\NavMenuRepository;
use NestedPages\Entities\PostType\PostTypeRepository;
use NestedPages\Entities\User\UserRepository;

/**
* Post Create/Update Methods
*/
class PostUpdateRepository 
{
	/**
	* Validation Class
	* @var object NP_Validation
	*/
	protected $validation;

	/**
	* Nav Menu Repository
	*/
	protected $nav_menu_repo;

	/**
	* Post Type Repository
	*/
	protected $post_type_repo;

	/**
	* User Repository
	*/
	protected $user_repo;

	/**
	* New Post ID
	* @var int
	*/
	protected $new_id;

	public function __construct()
	{
		$this->validation = new Validation;
		$this->nav_menu_repo = new NavMenuRepository;
		$this->post_type_repo = new PostTypeRepository;
		$this->user_repo = new UserRepository;
	}

	/**
	* Update Order
	* @param array posts
	* @param int parent
	* @since 1.0
	*/
	public function updateOrder($posts, $parent = 0, $filtered = false)
	{
		$this->validation->validatePostIDs($posts);
		$post_type = get_post_type($posts[0]['id']);
		$update_post_hook = apply_filters('nestedpages_use_update_post', false);
		if ( !$this->user_repo->canSortPosts($post_type) ) return;
		global $wpdb;
		foreach( $posts as $key => $post )
		{
			$post_id = sanitize_text_field($post['id']);
			$original_modifed_date = get_post_modified_time('Y-m-d H:i:s', false, $post_id);
			$original_modifed_date_gmt = get_post_modified_time('Y-m-d H:i:s', true, $post_id);
			
			if ( !$filtered ) $args['post_parent'] = intval($parent);

			// Update post hook causes server timeout on large sites, but may be required by some users
			if ( $update_post_hook ) wp_update_post(['ID' => $post_id]);

			if ( !$filtered ) :
				$query = $wpdb->prepare(
					"UPDATE $wpdb->posts 
					SET menu_order = '%d', post_parent = '%d', post_modified = '%s', post_modified_gmt = '%s' 
					WHERE ID = '%d'", 
					intval($key), 
					intval($parent),
					$original_modifed_date, 
					$original_modifed_date_gmt, 
					intval($post_id)
				);
			else : // The posts are filtered, don't update the parent
				$query = $wpdb->prepare(
					"UPDATE $wpdb->posts 
					SET menu_order = '%d', post_modified = '%s', post_modified_gmt = '%s' 
					WHERE ID = '%d'", 
					intval($key), 
					$original_modifed_date, 
					$original_modifed_date_gmt, 
					intval($post_id)
				); 
			endif;

			$wpdb->query( $query );
			do_action('nestedpages_post_order_updated', $post_id, $parent, $key, $filtered);

			wp_cache_delete( $post_id, 'posts' );
			
			if ( isset($post['children']) ) $this->updateOrder($post['children'], $post_id);
		}
		do_action('nestedpages_posts_order_updated', $posts, $parent);
		return true;
	}

	/**
	* Update Post
	* @param array data
	* @param boolean append taxonomies - whether to append or overwrite taxonomies
	* @since 1.0
	*/
	public function updatePost($data, $append_taxonomies = false)
	{
		if ( !current_user_can( 'edit_post', $data['post_id'] ) ) return false;
		$updated_post = [
			'ID' => sanitize_text_field($data['post_id'])
		];

		$this->validation->validateCustomFields($data);

		if ( isset($data['post_title']) && $data['post_title'] == "" ){ 
			$this->validation->checkEmpty($data['post_title'], __('Title', 'wp-nested-pages'));
		} elseif ( isset($data['post_title']) ){
			$updated_post['post_title'] = esc_attr($data['post_title']);
		}

		if ( isset($data['post_name']) ) 
			$updated_post['post_name'] = sanitize_text_field($data['post_name']);

		if ( isset($data['post_author']) ) 
			$updated_post['post_author'] = sanitize_text_field($data['post_author']);

		if ( isset($data['post_password']) ) 
			$updated_post['post_password'] = sanitize_text_field($data['post_password']);

		if ( !$this->post_type_repo->standardFieldDisabled('allow_comments', sanitize_text_field($data['post_type'])) ){
			$updated_post['comment_status'] = ( isset($data['comment_status']) ) ? 'open' : 'closed';
		}

		if ( isset($data['post_parent']) && $data['post_parent'] != '-1' ){
			$updated_post['post_parent'] = intval(sanitize_text_field($data['post_parent']));
		}

		if ( isset($data['np_date']) ) {
			$date = $this->validation->validateDate($data);
			$updated_post['post_date'] = $date;
		}

		if ( isset($data['mm']) && isset($data['jj']) && isset($data['aa'])  ){
			$date = $this->validation->validateDate($data);
			$updated_post['post_date'] = $date;
		}

		if ( isset($_POST['keep_private']) && $_POST['keep_private'] == 'private' ){
			$updated_post['post_status'] = 'private';
		} else {
			if ( isset($data['_status']) ) $updated_post['post_status'] = sanitize_text_field($data['_status']);
		}

		wp_update_post($updated_post);

		$this->updateSticky($data);
		$this->updateTemplate($data);
		$this->updateNestedPagesStatus($data);
		$this->updateCustomFields($data);

		// Taxonomies
		$this->updateCategories($data, $append_taxonomies);
		$this->updateTaxonomies($data, $append_taxonomies);

		// Menu Options
		$this->updateNavStatus($data);
		$this->updateNavTitle($data);
		$this->updateLinkTarget($data);
		$this->updateTitleAttribute($data);
		$this->updateNavCSS($data);
		$this->updateNavCustomUrl($data);

		return true;
	}

	/**
	* Update Page Template
	* @param array data
	* @since 1.0
	*/
	public function updateTemplate($data)
	{
		if ( isset($data['page_template']) && current_user_can('edit_post', $data['post_id']) ){
			$template = sanitize_text_field($data['page_template']);
			update_post_meta( 
				$data['post_id'], 
				'_wp_page_template', 
				$template
			);
		}
	}

	/**
	* Update Nav Status (show/hide in nav menu)
	* @since 1.0
	* @param array data
	*/
	public function updateNavStatus($data)
	{
		if ( !current_user_can('edit_post', $data['post_id']) ) return;
		$status = ( isset($data['nav_status']) && $data['nav_status'] == 'hide' ) ? 'hide' : 'show';
		$id = ( isset($data['post_id']) ) ? $data['post_id'] : $this->new_id;
		update_post_meta( 
			$id, 
			'_np_nav_status', 
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
		if ( !current_user_can('edit_post', $data['post_id']) ) return;
		if ( $this->post_type_repo->standardFieldDisabled('hide_in_np', sanitize_text_field($data['post_type'])) ) return;
		
		$status = ( isset($data['nested_pages_status']) && $data['nested_pages_status'] == 'hide' ) ? 'hide' : 'show';
		$id = ( isset($data['post_id']) ) ? $data['post_id'] : $this->new_id;
		update_post_meta(
			$id,
			'_nested_pages_status',
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
		if ( !current_user_can('edit_post', $data['post_id']) ) return;
		if ( isset($data['np_nav_title']) ){
			$title = sanitize_text_field($data['np_nav_title']);
			update_post_meta( 
				$data['post_id'], 
				'_np_nav_title', 
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
		if ( !current_user_can('edit_post', $data['post_id']) ) return;
		if ( isset($data['np_nav_css_classes']) ){
			$css_classes = sanitize_text_field($data['np_nav_css_classes']);
			update_post_meta( 
				$data['post_id'], 
				'_np_nav_css_classes', 
				$css_classes
			);
		}
	}

	/**
	* Update Nested Pages Menu Custom URL
	* @since 3.2.0
	* @param array data
	*/
	private function updateNavCustomUrl($data)
	{
		if ( !current_user_can('edit_post', $data['post_id']) ) return;
		if ( isset($data['np_nav_custom_url']) ){
			$url_input = $data['np_nav_custom_url'];
			$url = ( $url_input == '#' ) ? '#' : esc_url($data['np_nav_custom_url']);
			update_post_meta( 
				$data['post_id'], 
				'_np_nav_custom_url', 
				$url
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
		if ( !current_user_can('edit_post', $data['post_id']) ) return;
		if ( isset($data['np_title_attribute']) ){
			$title_attr = sanitize_text_field($data['np_title_attribute']);
			update_post_meta( 
				$data['post_id'], 
				'_np_title_attribute', 
				$title_attr
			);
		}
	}

	/**
	* Update Custom Fields, Available through filters
	* @param array data
	*/
	private function updateCustomFields($data)
	{
		foreach ( $data as $key => $value ){
			if ( strpos($key, 'np_custom_') !== false) {
				$field_key = str_replace('np_custom_', '', $key);
				$matches = preg_match('/nptype_(.*)_nptype/', $field_key, $output_array);
				$field_type = ( $output_array && isset($output_array[1]) ) ? $output_array[1] : null;
				$value = sanitize_text_field($data[$key]);
				$field_key = preg_replace('/nptype_(.*)_nptype_/', '', $field_key);
				if ( $field_type == 'url' ) $value = esc_url($value);
				if ( !current_user_can('edit_post', $data['post_id']) ) continue;
				update_post_meta( 
					$data['post_id'], 
					$field_key, 
					$value
				);
			}
		}
	}

	/**
	* Update Categories
	* @since 1.0
	* @param array data
	*/
	private function updateCategories($data, $append_taxonomies = false)
	{
		if ( !current_user_can('edit_post', $data['post_id']) ) return;
		if ( isset($data['post_category']) )
		{
			$this->validation->validateIntegerArray($data['post_category']);
			$cats = [];
			foreach($data['post_category'] as $cat) {
				if ( $cat !== 0 ) $cats[] = (int) $cat;
			}
			wp_set_post_terms($data['post_id'], $cats, 'category', $append_taxonomies);
		}
	}

	/**
	* Update Hierarchical Taxonomy Terms
	* @since 1.0
	* @param array data
	*/
	private function updateTaxonomies($data, $append_taxonomies)
	{
		if ( !current_user_can('edit_post', $data['post_id']) ) return;
		if ( isset($data['tax_input']) ) {
			foreach ( $data['tax_input'] as $taxonomy => $term_ids ){
				$tax = get_taxonomy($taxonomy);
				if ( $tax->hierarchical ){
					$this->validation->validateIntegerArray($term_ids);
					$this->updateHierarchicalTaxonomies($data, $taxonomy, $term_ids, $append_taxonomies);
				} else {
					$this->updateFlatTaxonomy($data, $taxonomy, $term_ids, $append_taxonomies);
				}
			}
		}
	}

	/**
	* Update Hierarchical Taxonomy Terms
	* @since 1.1.4
	* @param array data
	*/
	private function updateHierarchicalTaxonomies($data, $taxonomy, $term_ids, $append_taxonomies)
	{
		if ( !current_user_can('edit_post', $data['post_id']) ) return;
		$terms = [];
		foreach ( $term_ids as $term ){
			if ( $term !== 0 ) $terms[] = (int) $term;
		}
		wp_set_post_terms($data['post_id'], $terms, $taxonomy, $append_taxonomies);
	}

	/**
	* Update Flat Taxonomy Terms
	* @since 1.1.4
	* @param array data
	*/
	private function updateFlatTaxonomy($data, $taxonomy, $terms, $append_taxonomies)
	{
		if ( !current_user_can('edit_post', $data['post_id']) ) return;
		$terms = explode(',', sanitize_text_field($terms));
		$new_terms = array();
		foreach($terms as $term)
		{
			if ( $term !== "" )	array_push($new_terms, $term);
		}
		wp_set_post_terms($data['post_id'], $new_terms, $taxonomy, $append_taxonomies);
	}

	/**
	* Update Link Target for Redirects
	* @since 1.1
	* @param array data
	*/
	private function updateLinkTarget($data)
	{
		if ( !current_user_can('edit_post', $data['post_id']) ) return;
		$link_target = ( isset($data['link_target']) && $data['link_target'] == "_blank" ) ? "_blank" : "";
		$id = ( isset($data['post_id']) ) ? $data['post_id'] : $this->new_id;
		update_post_meta( 
			$id, 
			'_np_link_target', 
			$link_target
		);
	}

	/*
	* Update Sticky Posts
	* @since 2.0.1
	* @param array data
	*/
	private function updateSticky($data)
	{
		if ( !current_user_can('manage_options') ) return;
		if ( $this->post_type_repo->standardFieldDisabled('sticky', sanitize_text_field($data['post_type'])) ) return;
		$sticky_posts = get_option('sticky_posts');
		if ( isset($data['sticky']) && $data['sticky'] ){
			$sticky_posts[] = $data['post_id'];
			update_option('sticky_posts', $sticky_posts);
			return;
		}
		foreach($sticky_posts as $key => $post){
			if ( $post == intval($data['post_id']) ) unset($sticky_posts[$key]);
		}
		update_option('sticky_posts', $sticky_posts);
	}

	/**
	* Update Menu Related Meta
	* @since 1.4.1
	* @param array $data
	*/
	private function updateMenuMeta($data)
	{
		if ( !current_user_can( 'edit_post', $data['post_id'] ) ) return false;
		$id = ( isset($data['post_id']) ) ? $data['post_id'] : $this->new_id;
		$link_target = ( isset($data['linkTarget']) ) ? "_blank" : "";
		update_post_meta($id, '_np_link_target', $link_target);
		update_post_meta($id, '_np_nav_menu_item_type', sanitize_text_field($data['menuType']));
		update_post_meta($id, '_np_nav_menu_item_object', sanitize_text_field($data['objectType']));
		update_post_meta($id, '_np_nav_menu_item_object_id', sanitize_text_field($data['objectId']));
		if ( isset($data['cssClasses']) ){
			update_post_meta($id, '_np_nav_css_classes', sanitize_text_field($data['cssClasses']));
		}
		if ( isset($data['titleAttribute']) ){
			$title_attr = sanitize_text_field($data['titleAttribute']);
			update_post_meta($id, '_np_title_attribute', $title_attr);
		}
		if ( isset($data['navigationLabel']) ){
			$title = sanitize_text_field($data['navigationLabel']);
			update_post_meta($id, '_np_nav_title', $title);
		}
		$this->updateNavStatus($data);
	}

	/**
	* Update a Redirect
	* @since 1.1
	* @param array data
	*/
	public function updateRedirect($data)
	{
		if ( !current_user_can( 'edit_post', $data['post_id'] ) ) return false;
		$menu_order = isset($data['menu_order']) ? $data['menu_order'] : 0;
		$updated_post = [
			'ID' => sanitize_text_field($data['post_id']),
			'post_title' => sanitize_text_field($data['post_title']),
			'post_status' => sanitize_text_field($data['_status']),
			'post_parent' => sanitize_text_field($data['parent_id']),
			'menu_order' => $menu_order
		];

		if ( isset($data['post_content']) && $data['post_content'] !== "" ){
			$updated_post['post_content'] = esc_url($data['post_content']);
		}

		$this->new_id = wp_update_post($updated_post);
		$this->updateMenuMeta($data);
		return $this->new_id;
	}

	/**
	* Save a new Redirect
	* @since 1.1
	* @param array data
	*/
	public function saveRedirect($data)
	{
		if ( !$this->user_repo->canSortPosts('page') ) return;
		$new_link = [
			'post_title' => sanitize_text_field($data['menuTitle']),
			'post_status' => sanitize_text_field('publish'),
			'post_parent' => sanitize_text_field($data['parent_id']),
			'post_type' => 'np-redirect',
			'post_excerpt' => ''
		];
		if ( isset($data['url']) && $data['url'] !== "" ){
			$new_link['post_content'] = esc_url($data['url']);
		}
		$this->new_id = wp_insert_post($new_link);
		$data['post_id'] = $this->new_id;
		$parent_post_type = ( isset($data['parent_post_type']) ) ? sanitize_text_field($data['parent_post_type']) : 'page';
		add_post_meta($this->new_id, '_np_parent_post_type', $parent_post_type);
		$this->updateMenuMeta($data);
		return $this->new_id;
	}

	/**
	* Update a Post to Match Nav menu
	* @since 1.3.4
	* @param array of post data
	*/
	public function updateFromMenuItem($data)
	{
		if ( !current_user_can( 'edit_post', $data['post_id'] ) ) return false;
		$updated_post = [
			'ID' => sanitize_text_field($data['post_id']),
			'menu_order' => sanitize_text_field($data['menu_order']),
			'post_parent' => sanitize_text_field($data['post_parent'])
		];
		if ( isset($data['content']) ){
			$updated_post['post_content'] = $data['content'];
			$updated_post['post_title'] = $data['np_nav_title'];
		}
		wp_update_post($updated_post);

		// Menu Options
		$this->updateLinkTarget($data);
		$this->updateNavTitle($data);
		$this->updateTitleAttribute($data);

		if ( $data['np_nav_css_classes'][0] !== "" ){
			$data['np_nav_css_classes'] = implode(' ', $data['np_nav_css_classes']);
			$this->updateNavCSS($data);
		}
	}
}

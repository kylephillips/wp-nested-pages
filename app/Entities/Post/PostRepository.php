<?php 
namespace NestedPages\Entities\Post;

class PostRepository 
{
	/**
	* Get count of hidden posts
	* @since 1.1.4
	*/
	public function getHiddenCount($type)
	{
		if ( in_array('page', $type) ) array_push($type, 'np-redirect');
		$hidden = new \WP_Query(array(
			'post_type' => $type,
			'meta_key' => '_nested_pages_status',
			'meta_value' => 'hide',
			'perm' => 'readable'));
		return $hidden->found_posts;
	}

	/**
	* Get Trash Count (pages)
	* @since 1.1.4
	*/
	public function trashedCount($post_type)
	{
		$trashed = new \WP_Query(array('post_type'=>$post_type,'post_status'=>'trash','posts_per_page'=>-1));
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

	/**
	* Return css class string of taxonomies
	* @param object post object with taxonomies added
	* @return string
	*/
	public function getTaxonomyCSS($post, $h_taxonomies = array(), $f_taxonomies = array())
	{
		$out = ' ';
		
		// Build Hierarchical string
		if ( count($h_taxonomies) > 0 ) {
			foreach ( $h_taxonomies as $taxonomy ){
				$taxname = $taxonomy->name;
				if ( !isset($post->$taxname) ) continue;
				$terms = $post->$taxname;
				foreach ( $terms as $term ){
					$out .= 'in-' . $taxonomy->name . '-' . $term . ' ';
				}
			}
		}

		// Build Non-Hierarchical string
		if ( count($f_taxonomies) > 0 ) {
			foreach ( $f_taxonomies as $taxonomy ){
				$taxname = $taxonomy->name;
				if ( !isset($post->$taxname) ) continue;
				$terms = $post->$taxname;
				foreach ( $terms as $term ){
					$out .= 'inf-' . $taxonomy->name . '-nps-' . $term . ' ';
				}
			}
		}
		return $out;
	}

	/**
	* Get an array of pages given an array of IDs
	* @since 1.1.8 (used in creation of new pages)
	* @param ids array
	* @return array
	*/
	public function postArray($ids, $post_type)
	{
		$pages = array();
		$page_query = new \WP_Query(array(
			'post_type' => $post_type,
			'posts_per_page' => -1,
			'post__in' => $ids,
			'post_status' => array('publish', 'draft')
		));
		if ( $page_query->have_posts() ) : $c = 0; while ( $page_query->have_posts() ) : $page_query->the_post();
			global $post;
			
			$pages[$c]['id'] = get_the_id();
			$pages[$c]['title'] = get_the_title();
			$pages[$c]['slug'] = $post->post_name;
			$pages[$c]['author'] = get_the_author_meta('ID');
			$pages[$c]['author_formatted'] = get_the_author();
			$pages[$c]['status'] = ucfirst(get_post_status());
			$pages[$c]['page_template'] = get_page_template_slug($post->ID);
			$pages[$c]['post_parent'] = $post->post_parent;
			$pages[$c]['edit_link'] = get_edit_post_link($post->ID);
			$pages[$c]['view_link'] = get_the_permalink();
			$pages[$c]['delete_link'] = get_delete_post_link($post->ID);
			$pages[$c]['comment_status'] = $post->comment_status;
			$pages[$c]['comment_count'] =  wp_count_comments($post->ID);

			// Date Vars
			$pages[$c]['day'] = get_the_time('d');
			$pages[$c]['month'] = get_the_time('m');
			$pages[$c]['year'] = get_the_time('Y');
			$pages[$c]['hour'] = get_the_time('H');
			$pages[$c]['minute'] = get_the_time('i');
			$pages[$c]['datepicker'] = date_i18n('n/j/Y', get_the_time('U'));
			$pages[$c]['time'] = date_i18n('H:i', get_the_time('U'));
			$pages[$c]['formattedtime'] = date_i18n('g:i', get_the_time('U'));
			$pages[$c]['ampm'] = get_the_time('a');
			$pages[$c]['date_formatted'] = get_the_date();

			// NP Variables
			$all_meta = get_post_meta(get_the_id());
			$pages[$c]['post_meta'] = $all_meta;
			$pages[$c]['np_nav_status'] = ( isset($all_meta['np_nav_status']) && $all_meta['np_nav_status'][0] == 'hide' ) ? 'hide' : 'show';

		$c++; endwhile; endif; wp_reset_postdata();
		return $pages;
	}

	/**
	* Empty the Trash for a given post type
	* @since 1.3.1
	*/
	public function emptyTrash($post_type)
	{
		$posts_q = new \WP_Query(array('post_type'=>$post_type, 'post_status'=>'trash', 'posts_per_page'=>-1));
		if ( $posts_q->have_posts() ) : while ( $posts_q->have_posts() ) : $posts_q->the_post();
			$capability = ( $post_type == 'page' ) ? 'delete_page' : 'delete_posts';
			if( current_user_can( $capability, get_the_id() ) )
				wp_delete_post(get_the_id(), true);
		endwhile; endif; wp_reset_postdata();
		return true;
	}

	/**
	* Does a post exist?
	* @since 1.7.0
	* @param int post_id
	* @return boolean
	*/
	public function postExists($post_id, $post_type = 'post')
	{
		$post_q = new \WP_Query(array('post_type' => $post_type, 'p' => $post_id));
		if ( $post_q->have_posts() ){
			wp_reset_postdata();
			return true;
		}
		return false;
	}

	/**
	* Get all terms for a post
	*/
	public function getAllTerms($post_id)
	{
		global $wpdb;
		$query = $wpdb->prepare("SELECT p.post_title, tr.term_taxonomy_id AS tax_id, t.slug AS term_name, tt.taxonomy AS tax_name, tt.term_id AS term_id FROM {$wpdb->prefix}posts AS p LEFT JOIN {$wpdb->prefix}term_relationships AS tr ON tr.object_id = p.ID LEFT JOIN {$wpdb->prefix}terms AS t ON t.term_id = tr.term_taxonomy_id LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE p.ID = %s", $post_id);
		return $wpdb->get_results($query);
	}
}
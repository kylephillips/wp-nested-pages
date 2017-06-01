<?php 
namespace NestedPages\Entities\PluginIntegration;

use NestedPages\Entities\Post\PostRepository;

/**
* WPML Integration
* @link https://wpml.org/
*/
class WPML 
{
	/**
	* Installed
	* @var boolean
	*/
	public $installed = false;

	/**
	* Global Sitepress object
	*/
	private $sitepress;

	/**
	* WPML Settings
	*/
	private $settings;

	/**
	* Post Repository
	*/
	private $post_repo;

	public function __construct()
	{
		if ( defined('ICL_SITEPRESS_VERSION') ){
			$this->installed = true;
			global $sitepress;
			$this->sitepress = $sitepress;
			$this->settings = get_option('icl_sitepress_settings');
			$this->post_repo = new PostRepository;
			return;
		} 
	}

	/**
	* Get the current language
	* @return string
	*/
	public function getCurrentLanguage($return_type = 'code')
	{
		if ( $return_type == 'code' ) return ICL_LANGUAGE_CODE;
		if ( $return_type == 'name' ) return ICL_LANGUAGE_NAME;
		if ( $return_type == 'name_en' ) return ICL_LANGUAGE_NAME_EN;
	}

	/**
	* Get all available languages
	* @return array
	*/
	public function getLanguages()
	{
		return icl_get_languages();
	}

	/**
	* Get the default language
	* @return array
	*/
	public function getDefaultLanguage()
	{
		return $this->sitepress->get_default_language();
	}

	/**
	* Get a single language
	* @return array
	*/
	public function getSingleLanguage($language = 'en')
	{
		$languages = $this->getLanguages();
		return ( array_key_exists($language, $languages) ) ? $languages[$language] : array();
	}

	/**
	* Is the default language currently being shown
	* @return boolean
	*/
	public function isDefaultLanguage()
	{
		return ( $this->getCurrentLanguage() == $this->getDefaultLanguage() ) ? true : false;
	}

	/**
	* Get all translations for a post
	* @return array or int
	*/
	public function getAllTranslations($post_id, $return = 'array')
	{
		if ( !$post_id && $return == 'array' ) return array();
		if ( !$post_id && $return == 'count' ) return 0;
		$true_id = $this->sitepress->get_element_trid($post_id);
		if ( $return == 'array' ) return $this->sitepress->get_element_translations($true_id);
		if ( $return == 'count' ) return count($this->sitepress->get_element_translations($true_id));
	}

	/**
	* Get the primary language post ID for a provided child id
	* @param int 
	* @return int - post id
	*/
	public function getPrimaryLanguagePost($post_id)
	{
		$all_translations = $this->getAllTranslations($post_id);
		foreach ( $all_translations as $translation ){
			if ( $translation->language_code == $this->getDefaultLanguage() ) return $translation->element_id;
		}
	}

	/**
	* Sync Post Order among translations
	* @param array of posts with children
	*/
	public function syncPostOrder($posts)
	{
		if ( $this->settings['sync_page_ordering'] !== 1 ) return;
		global $wpdb;
		if ( !is_array($posts) ) return;
		foreach ( $posts as $order => $post ) :
			$translations = $this->getAllTranslations($post['id']);
			foreach ( $translations as $lang_code => $post_info ) :
				$post_id = $post_info->element_id;
				$query = "UPDATE $wpdb->posts SET menu_order = '$order' WHERE ID = '$post_id'";
				$wpdb->query( $query );
			endforeach;
			if ( isset($post['children']) ) $this->syncPostOrder($post['children']);
		endforeach;
	}

	/**
	* Output the sync menus button
	* @return html
	*/
	public function syncMenusButton()
	{
		$url = esc_url(admin_url('admin.php?page=sitepress-multilingual-cms/menu/menu-sync/menus-sync.php'));
		return '<a href="' . $url . '" class="np-btn">' . __('Sync WPML Menus', 'wp-nested-pages') . '</a>';
	}

	/**
	* Get the Translated IDs of an array of post IDs
	* @param array $post_ids
	* @return array of post ids
	*/
	public function getAllTranslatedIds($post_ids)
	{
		if ( !is_array($post_ids) ) return array();
		foreach ( $post_ids as $id ){
			$translations = $this->getAllTranslations($id);
			if ( empty($translations) ) continue;
			foreach ( $translations as $lang => $post ){
				if ( $post->element_id == $id ) continue;
				array_push($post_ids, $post->element_id);
			}
		}
		return $post_ids;
	}

	/**
	* Get the number of translated posts for a post type
	*/
	public function translatedPostCount($language_code = '', $post_type = '')
	{
		global $wpdb;

		$default_language_code = $this->getDefaultLanguage();
		$post_type = 'post_' . $post_type;

		$query = $wpdb->prepare("SELECT COUNT( {$wpdb->prefix}posts.ID ) FROM {$wpdb->prefix}posts LEFT JOIN {$wpdb->prefix}icl_translations ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}icl_translations.element_id WHERE {$wpdb->prefix}icl_translations.language_code = '%s' AND {$wpdb->prefix}icl_translations.source_language_code = '%s' AND {$wpdb->prefix}icl_translations.element_type = '%s'", $language_code, $default_language_code, $post_type);

		return $wpdb->get_var( $query );
	}

	/**
	* Get the number of default language posts for a post type
	*/
	public function defaultPostCount($post_type = '')
	{
		global $wpdb;
		$default_language_code = $this->getDefaultLanguage();
		$query = $wpdb->prepare("SELECT COUNT(p.ID) FROM {$wpdb->prefix}posts AS p LEFT JOIN {$wpdb->prefix}icl_translations AS t ON t.element_id = p.ID WHERE p.post_type = '%s' AND p.post_status = 'publish' AND t.language_code = '%s'", $post_type, $default_language_code);
		return $wpdb->get_var( $query );
	}

	/**
	* Output a list of language links in the tools section of the listing head
	* @param string $post_type
	* @return string html
	* @example English (2) | German (1) | Spanish (0)
	*/
	public function languageToolLinks($post_type)
	{
		$html = '<ul class="subsubsub" style="clear:both;">';
		$c = 1;
		$languages = $this->getLanguages();
		foreach ( $languages as $lang_code => $lang ){
			$translated_language = $this->getTranslatedLanguageName($this->getDefaultLanguage(), $lang_code);
			if ( !$translated_language ) continue;
			$post_count = ( $lang_code == $this->getDefaultLanguage() )
				? $this->defaultPostCount($post_type)
				: $this->translatedPostCount($lang_code, $post_type);
			$html .= '<li>';
			if ( $c > 1 ) $html .= '|&nbsp;';
			if ( $lang['active'] ) $html .= '<strong>';
			if ( $post_count > 0 ) $html .= '<a href="' . $this->getLanguageFilteredLink($lang_code, $post_type) . '">';
			$html .= $translated_language . ' ';
			if ( $lang_code !== $this->getDefaultLanguage() ) $html .= '(' . $post_count . ')';
			if ( $lang_code == $this->getDefaultLanguage() ) $html .= '(' . $post_count . ')';
			if ( $lang['active'] ) $html .= '</strong>';
			if ( $post_count > 0 ) $html .= '</a>';
			$html .= '&nbsp;</li>';
			$c++;
		}
		$html .= '<li>|&nbsp;<a href="' . $this->getLanguageFilteredLink('all', $post_type) . '">' . __('All Languages', 'wp-nested-pages') . '</a></li>';
		$html .= '</ul>';
		return $html;
	}

	/**
	* Get the Links to a filtered language
	*/
	public function getLanguageFilteredLink($language, $post_type)
	{
		$url = 'admin.php?page=nestedpages';
		if ( $post_type !== 'page' ) $url .= '-' . $post_type;
		$url .= '&lang=' . $language;
		return esc_url(admin_url($url));
	}

	/**
	* Get a translated language name for a given language
	* @param string $translated_language - the language to return as
	* @param string $target_language - the language to translate
	* @return string - translated name of language (ex: if default is english, and target is spanish, will return "Spanish" rather than "Espanol")
	*/
	public function getTranslatedLanguageName($translated_language, $target_language)
	{
		global $wpdb;
		$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}icl_languages_translations WHERE language_code = '%s' AND display_language_code = '%s'",  $target_language, $translated_language);
		$results = $wpdb->get_results( $query );
		return ( $results[0]->name ) ? $results[0]->name : null;
	}

	/**
	* Sync fields across all langauges
	*/
	public function syncPosts($post_id)
	{
		$all_translations = $this->getAllTranslations($post_id);
		$source_template = get_post_meta($post_id, '_wp_page_template', true);
		$source_post = get_post($post_id);
		$sticky_flag = ( $this->settings['sync_sticky_flag'] ) ? get_option('sticky_posts') : array();
		foreach ( $all_translations as $translation ) :
			if ( $translation->element_id == $post_id ) continue;
			if ( $this->settings['sync_page_template'] && $source_template ){
				update_post_meta( 
					$translation->element_id, 
					'_wp_page_template', 
					$source_template
				);
			}
			$update_args = array();
			if ( $this->settings['sync_comment_status'] ) $update_args['comment_status'] = $source_post->comment_status;
			if ( $this->settings['sync_post_date'] ) $update_args['post_date'] = $source_post->post_date;
			if ( $this->settings['sync_post_date'] ) $update_args['post_date_gmt'] = $source_post->post_date_gmt;
			if ( $this->settings['sync_ping_status'] ) $update_args['post_status'] = $source_post->post_status;
			if ( $this->settings['sync_password'] ) $update_args['post_password'] = $source_post->post_password;
			if ( $this->settings['sync_private_flag'] ) $update_args['post_status'] = $source_post->post_status;
			if ( $this->settings['sync_post_taxonomies'] ) $this->syncTaxonomies($post_id);
		endforeach;
	}

	/**
	* Sync Taxonomies across all languages
	*/
	public function syncTaxonomies($source_post_id)
	{
		global $wpdb;
		$all_translations = $this->getAllTranslations($source_post_id);
		$terms = $this->post_repo->getAllTerms($source_post_id);
		$translated_terms = array();

		// Get all translations for the terms
		foreach ( $terms as $term ){
			if ( $term->tax_name == 'language' ) continue;
			$query = "SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id = $term->term_id AND element_type LIKE 'tax_%'";
			$trid = $wpdb->get_var($query);
			if ( !$trid ) continue;
			$translated_terms[] = $this->getTermTranslations($trid);
			if ( !$translated_terms ) continue;
		}

		foreach ( $all_translations as $translation ) :
			if ( $translation->element_id == $source_post_id ) continue;
			if ( !$terms ) continue;

			// Build the array of terms to sync
			foreach ( $translated_terms as $term_translations ) :
				foreach ( $term_translations as $trans ) :
					$taxonomy = get_taxonomy($trans->taxonomy);
					$tax_hierarchical = $taxonomy->hierarchical;
					if ( $trans->language_code == $translation->language_code )  {
						$translation->terms[$trans->taxonomy][] = ($tax_hierarchical) ? $trans->term_id : $trans->name;
					}
				endforeach;
			endforeach;

			if ( !is_array($translation->terms) ) continue;
			foreach ( $translation->terms as $taxonomy => $terms ) :
				wp_set_post_terms($translation->element_id, $terms, $taxonomy, false);
			endforeach;
		endforeach;
	}

	/**
	* Get all the translations for a term
	*/
	public function getTermTranslations($trid)
	{
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT iclt.language_code, t.term_id, t.name, t.slug, tt.taxonomy FROM {$wpdb->prefix}icl_translations AS iclt LEFT JOIN {$wpdb->prefix}terms AS t ON t.term_id = iclt.element_id LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_id = t.term_id WHERE trid = %s", $trid));
	}

	/**
	* Get the count of published posts for a post type in a specific language
	* @param $post_type string – post type name
	* @param $language string - language code
	*/ 
	public function getPostTypeCountByLanguage($post_type, $language = null)
	{
		if ( !$language ) $language = $this->getCurrentLanguage();
		global $wpdb;
		$query = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}posts AS p LEFT JOIN {$wpdb->prefix}icl_translations AS trans ON p.ID = trans.element_id WHERE post_type = %s AND p.post_status = 'publish' AND trans.language_code = %s", $post_type, $language);
		return $wpdb->get_var($query);
	}
}
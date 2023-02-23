<?php 
namespace NestedPages;

/**
* Helper Functions
*/
class Helpers 
{
	/**
	* Plugin Root Directory
	*/
	public static function plugin_url()
	{
		$url = plugins_url('/', NESTEDPAGES_URI);
		return rtrim($url, '/');
	}

	/**
	* View
	*/
	public static function view($file)
	{
		return dirname(__FILE__) . '/Views/' . $file . '.php';
	}

	/**
	* Asset
	*/
	public static function asset($file)
	{
		return dirname(dirname(__FILE__)) . '/assets/' . $file;
	}

	/**
	* Link to the default WP Pages listing
	* @since 1.2
	* @return string
	*/
	public static function defaultPagesLink($type = 'page')
	{
		$link = esc_url( admin_url('edit.php?post_type=' . $type ) );
		return $link;
	}

   /**
	 * Return posts of a page group (recursion function)
	 */
	private static function getChildPosts_recurse(int $toplevel_id, array &$pages, string &$sqlWhere) {
		global $wpdb;
		$sqlQuery = "select id from {$wpdb->posts} where post_parent = {$toplevel_id} $sqlWhere";
		$rows_sub_ids = $wpdb->get_results($sqlQuery, ARRAY_A);
		foreach ( $rows_sub_ids as &$row_sub_id ) {
			$sub_id = $row_sub_id['id'];
			$pages[] = $sub_id;
			self::getChildPosts_recurse($sub_id, $pages, $sqlWhere);
		}
		unset($row_sub_id);
	}

	/**
	 * Return a part of an SQL where clause.
	 * Since this function is used internally and possibly by theme and plugin developers only,
	 * it is expected that the field name is not vulnerable to SQL injection.
	 */
	private static function getSQLWhere(bool $include, string $field, array $values) {
		$sqlWhere = ' and ' . $field;
		if ( !$include ) $sqlWhere .= ' not';
		$sqlWhere .= ' in (';
		$sep = '';
		foreach ($values as $value) {
			$sqlWhere .= $sep . "'" . esc_sql($value) . "'";
			$sep = ', ';
		}
		$sqlWhere .= ')';
	}

	/**
	 * Get child posts of a post
	 */
	public static function getChildPosts(int $toplevel_id, array $args = []) {
		$sqlWhere = '';
		foreach ( ['post_type', 'post_status'] as $field ) {
			if ( array_key_exists($field, $args) ) {
				foreach ( ['include', 'exclude'] as $rule ) {
					if ( array_key_exists( $rule, $args[$field] ) ) {
						$sqlWhere .= self::getSQLWhere($rule == 'include', $field, (array)$args[$field][$rule]);
					}
				}
			}
		}
		$page_ids = [ $toplevel_id ];
		self::getChildPosts_recurse($toplevel_id, $page_ids, $sqlWhere);
		return $page_ids;
	}

	/**
	 * Get posts of a page group.
	 * By default, it returns all related posts.
	 */
	public static function getPostsOfPageGroup(int $post_id, ?array $postTypes = null) {
		$page_ids = self::getChildPosts( self::getPageGroup($post_id), $postTypes );
		return $page_ids;
	}

	/**
	 * Return page group of the given post
	 */
	public static function getPageGroup(int $post_id) {
		global $wpdb;
		$pagegroup = false;
		$has_error = false;
		$p_id = $post_id;
		do {
			$rows = $wpdb->get_results("select id, post_parent from {$wpdb->posts} where post_type = 'page' and id = " . $p_id, ARRAY_A);
			if ( count( $rows ) == 1 ) {
				if ( $rows[0]['post_parent'] ) {
					$p_id = $rows[0]['post_parent'];
				} else {
					$pagegroup = $rows[0]['id'];
				}
			} else
				$has_error = true;
		} while ( ! $has_error && $pagegroup === false );
		return $has_error ? false : $pagegroup;
	}


}

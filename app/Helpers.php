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
	 * Return a part of an SQL where clause.
	 * Since this function is used internally and possibly by theme and plugin developers only,
	 * it is expected that the field name is not vulnerable to SQL injection.
	 */
	public static function getSQLWhere(bool $include, string $field, array $values) {
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

}

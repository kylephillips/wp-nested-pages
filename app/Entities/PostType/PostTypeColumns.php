<?php 
namespace NestedPages\Entities\PostType;
use NestedPages\Helpers;
use \DOMDocument;
use \DOMXPath;

include(ABSPATH . '/wp-admin/includes/class-wp-posts-list-table.php');

/**
* Get the columns to display for a specific post type
*/
class PostTypeColumns
{
	/**
	* Post Type Name
	* @var str
	*/
	private $post_type;

	/**
	* The List Table object
	* @var obj WP_Posts_List_Table
	*/
	private $post_list_table;

	public function __construct($post_type)
	{
		$this->post_type = $post_type;
		$this->setTable();
	}

	/**
	* Set the Posts_List_table
	*/
	public function setTable()
	{
		global $post_type_object;
		global $mode;
		$mode = 'list';
		$post_type_object = get_post_type_object($this->post_type);
		$screen = new \stdClass;
		$screen = convert_to_screen('edit-' . $this->post_type);
		$this->post_list_table = _get_list_table('WP_Posts_List_Table', ['screen' => $screen]);
	}

	/**
	* Get the column headers output
	* @return str - html
	*/
	public function columnHeaders()
	{
		ob_start();
		$this->post_list_table->print_column_headers();
		$headers = ob_get_clean();

		$doc = new DOMDocument();
		@$doc->loadHTML($headers);

		// Remove the checkbox
		$xpath = new DOMXPath($doc);
		foreach ($xpath->query('//td[@id="cb"]') as $key => $td) {
			$td->parentNode->removeChild($td);
		}

		$output = $doc->saveHTML();
		return $output;
	}

	/**
	* Get a single row (a single post)
	* @param obj - WP_Post
	* @param bool - Whether ot include the sort handle
	*/
	public function get_single_row($post, $sort_handle = false, $level = 0) 
	{
		ob_start();
		$this->post_list_table->single_row($post);
		$row = ob_get_clean();
		
		$doc = new DOMDocument();
		$doc->loadHTML($row);

		$xpath = new DOMXPath($doc);

		// Add the level designator
		foreach ($xpath->query('//a[@class="row-title"]') as $td) {
			$text = $td->nodeValue;
			$td->nodeValue = str_repeat( '&#8212; ', $level ) . $text;
		}

		// Remove the trash link
		foreach ($xpath->query('//span[@class="trash"]') as $span) {
			$span->parentNode->removeChild($span);
		}

		// Remove the quick edit link
		foreach ($xpath->query('//span[@class="inline hide-if-no-js"]') as $span){
			$span->parentNode->removeChild($span);
		}
		$output = $doc->saveHTML();
		return $output;
	}
}
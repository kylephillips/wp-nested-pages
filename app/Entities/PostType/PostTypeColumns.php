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

		// Add the handle spacer
		$tr = $doc->getElementsByTagName('th'); // First element is the <th> checkbox
		$new_th = $doc->createElement('th', '');
		$new_th->setAttribute('class', 'handle-cell-spacer');
		$tr->item(0)->parentNode->insertBefore($new_th, $tr->item(0));

		$output = $doc->saveHTML();
		return $output;
	}

	/**
	* Get a single row (a single post)
	* @param obj - WP_Post
	* @param bool - Whether ot include the sort handle
	*/
	public function get_single_row($post, $sort_handle = false) 
	{
		ob_start();
		$this->post_list_table->single_row($post);
		$row = ob_get_clean();
		
		$doc = new DOMDocument();
		$doc->loadHTML($row);

		// Add the sort handle if necessary
		if ( $sort_handle ) :
			$th = $doc->getElementsByTagName('th'); // First element is the <th> checkbox
			foreach ( $th as $key => $td ){
				if ( $key > 0 ) continue;
				$src = \NestedPages\Helpers::plugin_url() . '/assets/images/handle.svg';
				$image = $doc->createElement('img');
				$image->setAttribute('src', $src);
				$image->setAttribute('class', 'handle');
				$image->setAttribute('alt', __('Sorting Handle', 'wp-nested-pages'));
				$new_td = $doc->createElement('td', '');
				$new_td->setAttribute('class', 'handle-cell');
				$new_td->appendChild($image);
				$td->insertBefore($new_td);
				$doc->saveHTML();
			}
		endif;

		$xpath = new DOMXPath($doc);

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
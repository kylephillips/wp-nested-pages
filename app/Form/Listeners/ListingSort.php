<?php 
namespace NestedPages\Form\Listeners;

use NestedPages\Entities\PostType\PostTypeRepository;

/**
* Redirect to Listing with Specified Sorting Options Applied
*/
class ListingSort 
{
	/**
	* URL to redirect to
	* @var string
	*/
	private $url;

	/**
	* The Post Type
	* @var string
	*/
	private $post_type;

	/**
	* Post Type Repository
	* @var object
	*/
	private $post_type_repo;

	public function __construct()
	{
		$this->post_type_repo = new PostTypeRepository;
		$this->setURL();
		$this->redirect();
	}

	/**
	* Build the URL to Redirect to
	*/
	private function setURL()
	{
		$this->url = sanitize_text_field($_POST['page']);
		$this->post_type = sanitize_text_field($_POST['post_type']);
		$this->setOrderBy();
		$this->setOrder();
		$this->setAuthor();
		$this->setTaxonomies();
	}

	/**
	* Set Order by parameters
	*/
	private function setOrderBy()
	{
		$allowed = ['menu_order', 'date', 'title']; // prevent tomfoolery
		if ( isset($_POST['np_orderby']) && $_POST['np_orderby'] !== "" && in_array($_POST['np_orderby'], $allowed) ) $this->url .= '&orderby=' . sanitize_text_field($_POST['np_orderby']);
	}

	/**
	* Set Order parameters
	*/
	private function setOrder()
	{
		$allowed = ['ASC', 'DESC']; // prevent tomfoolery
		if ( isset($_POST['np_order']) && in_array($_POST['np_order'], $allowed) ) $this->url .= '&order=' . sanitize_text_field($_POST['np_order']);
	}

	/**
	* Set Author parameters
	*/
	private function setAuthor()
	{
		if ( (isset($_POST['np_author'])) && ($_POST['np_author'] !== "") )	$this->url .= '&author=' . sanitize_text_field($_POST['np_author']);
	}

	/**
	* Set Taxonomy Parameters
	*/
	private function setTaxonomies()
	{
		$h_taxonomies = $this->post_type_repo->getTaxonomies($this->post_type, true);
		$f_taxonomies = $this->post_type_repo->getTaxonomies($this->post_type, false);
		$taxonomies = array_merge($h_taxonomies, $f_taxonomies);
		foreach ( $taxonomies as $tax ) :
			if ( $this->post_type_repo->sortOptionEnabled($this->post_type, $tax->name, true) ) :
				if ( isset($_POST[$tax->name]) && $_POST[$tax->name] !== 'all' ) $this->url .= '&' . $tax->name . '=' . sanitize_text_field($_POST[$tax->name]);
			endif;
		endforeach;
	}

	/**
	* Redirect to new URL
	*/
	private function redirect()
	{
		header('Location:' . $this->url);
	}
}
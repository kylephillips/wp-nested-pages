<?php 

namespace NestedPages\Form\Listeners;

/**
* Filter Pages by Category
*/
class CategoryFilter 
{

	/**
	* URL to redirect to
	* @var string
	*/
	private $url;
	private $taxonomy = 'category';

	public function __construct()
	{
		$this->setURL();
		$this->redirect();
	}

	/**
	* Build the URL to Redirect to
	*/
	private function setURL()
	{
		
		// New check if this is a standard category or custom PT
		// Split the url
		$split_url = explode('nestedpages-', sanitize_text_field($_POST['page']));

		if ( isset($split_url[1]) )
		{
			$this->setTaxonomy($split_url[1]);
		}

		$this->url = sanitize_text_field($_POST['page']);
		$this->setCategories();
	}


	/**
	* Get related Taxonomy
	*/
	private function setTaxonomy($post_type)
	{
		$taxonomy_name = get_object_taxonomies( $post_type );
		$this->taxonomy = $taxonomy_name[0];
	}

	/**
	* Set Order parameters
	*/
	private function setCategories()
	{		
		if ( $this->taxonomy != 'category' )
		{
			$this->url .= '&'.$_POST['associated_tax'].'=' . sanitize_text_field($_POST['np_category']);
		}
		else
		{
			$this->url .= '&category=' . sanitize_text_field($_POST['np_category']);			
		}
	}

	/**
	* Redirect to new URL
	*/
	private function redirect()
	{
		echo $this->url;
		header('Location:' . $this->url);
	}
}
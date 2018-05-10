<?php 
namespace NestedPages\Form\Listeners;

/**
* Gets term names
* @return json response
*/
class GetTaxonomies extends BaseHandler 
{
	/**
	* Terms to return
	* @var object
	*/
	private $terms;

	public function __construct()
	{
		parent::__construct();
		$this->loopTaxonomies();
		$this->setResponse();
		$this->sendResponse();
	}

	/**
	* Loop through the taxonomies
	*/
	private function loopTaxonomies()
	{
		if ( !isset($this->data['terms']) ) return;
		$terms = $this->data['terms'];
		foreach ($terms as $taxonomy => $tax_terms){
			$this->setTermNames($taxonomy, $tax_terms);
		}
	}

	/**
	* Get the Term names for each taxonomy 
	*/
	private function setTermNames($taxonomy, $terms)
	{		
		foreach ( $terms as $key => $term )
		{
			$single_term = get_term_by('id', $term, $taxonomy);
			$term_name = $single_term->name;
			$this->terms[$taxonomy][$key] = $term_name;
		}
	}

	/**
	* Prepare Response
	*/
	private function setResponse()
	{
		$this->response = ['status'=>'success', 'terms'=>$this->terms];
	}
}
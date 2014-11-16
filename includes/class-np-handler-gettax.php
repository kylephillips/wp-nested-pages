<?php
function nestedpages_get_tax()
{
	new NP_GetTax;
}

require_once('class-np-handler-base.php');
require_once('class-np-helpers.php');

/**
* Gets term names
* @return json response
*/
class NP_GetTax extends NP_BaseHandler {

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
		$this->response = array('status'=>'success', 'terms'=>$this->terms);
	}
}

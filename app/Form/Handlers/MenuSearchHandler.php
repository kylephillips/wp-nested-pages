<?php 

namespace NestedPages\Form\Handlers;

class MenuSearchHandler extends BaseHandler 
{
	/**
	* Form Data
	*/
	protected $data;

	/**
	* Search Results
	*/
	private $results;

	public function __construct()
	{
		parent::__construct();
		$this->setFormData();
		$this->search();
		return wp_send_json(array('status' => 'success', 'results' => $this->results));
	}

	/**
	* Set the search-specific form data
	*/
	private function setFormData()
	{
		$this->data['term'] = sanitize_text_field($_POST['term']);
		$this->data['searchType'] = sanitize_text_field($_POST['searchType']);
		$this->data['searchObject'] = sanitize_text_field($_POST['searchObject']);
	}

	/**
	* Perform the search
	*/
	private function search()
	{
		if ( $this->data['searchType'] == 'post_type' ) $this->searchPosts();
		if ( $this->data['searchType'] == 'taxonomy' ) $this->searchTaxonomies();
	}

	/**
	* Perform a search on posts
	*/
	private function searchPosts()
	{
		$sq = new \WP_Query(array(
			'post_type' => $this->data['searchObject'],
			's' => $this->data['term']
		));
		if ( $sq->have_posts() ) :
			$this->results = $sq->posts;
		else :
			$this->results = null;
		endif; wp_reset_postdata();
	}

	/**
	* Perform a taxonomy search
	*/
	private function searchTaxonomies()
	{
		$terms = get_terms($this->data['searchObject'], array(
			'name__like' => $this->data['term']
		));
		if ( $terms ){
			$this->results = $terms;
			return;
		}
		$this->results = null;
	}


}
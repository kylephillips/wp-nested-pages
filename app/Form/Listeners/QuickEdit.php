<?php

namespace NestedPages\Form\Listeners;

/**
* Handles processing the quick edit form
* @return json response
*/
class QuickEdit extends BaseHandler
{

	public function __construct()
	{
		parent::__construct();
		$this->updatePost();
		$this->syncMenu();
		$this->sendResponse();
	}

	/**
	* Update the Post
	* @todo update taxonomies
	*/
	private function updatePost()
	{
		$updated = $this->post_update_repo->updatePost($this->data);
		if ( !$updated ) $this->sendErrorResponse();
		if ( isset($this->data['tax_input']) ) $this->addFlatTaxonomies();
		$this->addData();
		$this->response = array(
			'status' => 'success',
			'message' => __('Post successfully updated.'),
			'post_data' => $this->data
		);
	}

	/**
	* Add Flat Taxonomy IDs
	*/
	private function addFlatTaxonomies()
	{
		$taxonomies = $this->data['tax_input'];
		foreach($taxonomies as $key => $tax_terms){
			$tax = get_taxonomy($key);
			if ( (!is_taxonomy_hierarchical($tax->name)) && !empty($tax_terms) ){
				unset($this->data['tax_input'][$key]); // remove taxonomy from returned tax input
				$terms = explode(',', $tax_terms);
				foreach ( $terms as $i => $term ){
					if ( $term !== "" ){
						$term_obj = get_term_by('name', $term, $tax->name);
						$this->data['flat_tax'][$key][$i] = $term_obj->term_id;	// add the new flat_tax returned object
					}
				}
			}
		}
	}

	/**
	* Add additional data to the response object
	*/
	private function addData()
	{
		if ( isset($this->data['post_author']) ) $this->data['author_name'] = get_the_author_meta('display_name', $this->data['post_author']);
		$this->data['nav_status'] = ( isset($this->data['nav_status']) ) ? 'hide' : 'show';
		$this->data['np_status'] = ( isset($this->data['nested_pages_status']) ) ? 'hide' : 'show';
		$this->data['link_target'] = ( isset($this->data['link_target']) ) ? '_blank' : 'none';
		$this->data['keep_private'] = ( isset($this->data['keep_private']) ) ? 'private' : 'public';
		$this->data['_status'] = ( isset($this->data['_status']) ) ? $this->data['_status'] : 'publish';
		$this->data['permalink'] = get_the_permalink($this->data['post_id']);
		if ( !isset($_POST['comment_status']) ) $this->data['comment_status'] = 'closed';
	}

}

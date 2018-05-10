<?php 
namespace NestedPages\Form\Listeners;

class PostSearch extends BaseHandler 
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
		$this->searchPosts();
		return wp_send_json(['status' => 'success', 'results' => $this->results]);
	}

	/**
	* Set the search-specific form data
	*/
	private function setFormData()
	{
		$this->data['term'] = sanitize_text_field($_POST['term']);
		$this->data['postType'] = sanitize_text_field($_POST['postType']);
	}

	/**
	* Perform a search on posts
	*/
	private function searchPosts()
	{
		$sq = new \WP_Query([
			'post_type' => $this->data['postType'],
			's' => $this->data['term'],
			'posts_per_page' => -1
		]);
		if ( $sq->have_posts() ) :
			$this->results = $sq->posts;
		else :
			$this->results = null;
		endif; wp_reset_postdata();
	}
}
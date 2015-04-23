<?php namespace NestedPages\Form\Handlers;

/**
* Handles processing sortable pages
* updates menu order & page parents
* @return json response
*/
class SortHandler extends BaseHandler {


	public function __construct()
	{
		parent::__construct();
		$this->updateOrder();
		$this->syncMenu();
		$this->sendResponse();
	}


	/**
	* Update Post Order
	*/
	private function updateOrder()
	{
		$posts = $this->data['list'];
		$order = $this->post_update_repo->updateOrder($posts);
		if ( $order ){
			$this->response = array('status' => 'success', 'message' => __('Page order successfully updated.','nestedpages') );
		} else {
			$this->response = array('status'=>'error', 'message'=> __('There was an error updating the page order.','nestedpages') );
		}
	}


}

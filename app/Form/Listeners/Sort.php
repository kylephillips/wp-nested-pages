<?php 
namespace NestedPages\Form\Listeners;

use NestedPages\Entities\PluginIntegration\IntegrationFactory;

/**
* Handles processing sortable pages
* updates menu order & page parents
* @return json response
*/
class Sort extends BaseHandler 
{
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
		$filtered = ( isset($this->data['filtered']) && $this->data['filtered'] == 'true' ) ? true : false;
		$order = $this->post_update_repo->updateOrder($posts, 0, $filtered);
		if ( $order ){
			if ( $this->integrations->plugins->wpml->installed ) $this->integrations->plugins->wpml->syncPostOrder($posts);
			$this->response = ['status' => 'success', 'message' => __('Page order successfully updated.','wp-nested-pages') ];
		} else {
			$this->response = ['status'=>'error', 'message'=> __('There was an error updating the page order.','wp-nested-pages') ];
		}
	}
}
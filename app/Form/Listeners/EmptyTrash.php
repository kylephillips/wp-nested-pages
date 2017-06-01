<?php 
namespace NestedPages\Form\Listeners;

class EmptyTrash extends BaseHandler 
{
	public function __construct()
	{
		parent::__construct();
		$this->emptyTrash();
	}

	private function emptyTrash()
	{
		if ( $this->post_repo->emptyTrash($_POST['posttype']) ){
			return wp_send_json(array(
				'status'=>'success', 
				'message'=> __('Trash successfully emptied.', 'wp-nested-pages')
			));
		}
		$this->sendErrorResponse();
	}
}
<?php namespace NestedPages\Form\Handlers;

class EmptyTrashHandler extends BaseHandler {

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
				'message'=> __('Trash successfully emptied.')
			));
		}
		$this->sendErrorResponse();
	}

}
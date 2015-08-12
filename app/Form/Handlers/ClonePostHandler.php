<?php namespace NestedPages\Form\Handlers;


/**
* Clone an existing post
*/
class ClonePostHandler extends BaseHandler {

	public function __construct()
	{
		parent::__construct();
		return wp_send_json(array('status' => 'testing'));
	}
}
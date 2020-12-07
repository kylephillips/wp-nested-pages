<?php 
namespace NestedPages\Form\Listeners;

class ResetUserPreferences extends BaseHandler 
{
	public function __construct()
	{
		parent::__construct();
		$this->reset();
	}

	private function reset()
	{
		if ( !current_user_can('manage_options') ) return;
		global $wpdb;
		$wpdb->delete($wpdb->usermeta, ['meta_key' => 'np_visible_posts']);
		return wp_send_json(['status' => 'success']);
	}
}
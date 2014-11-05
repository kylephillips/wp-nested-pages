<?php
/**
* Confirmation Messages
*/
class NP_Confirmation {

	/**
	* Message Output
	* @var string
	*/
	private $message;

	/**
	* Type of Message
	* @var string
	*/
	private $type;


	public function __construct()
	{
		$this->setType();
		$this->setMessage();
	}


	/**
	* Set the Type of confirmation
	*/
	private function setType()
	{
		if ( (isset($_GET['trashed'])) && (intval($_GET['trashed']) > 0) ) $this->type = 'trashConfirm';
		if ( (isset($_GET['untrashed'])) && (intval($_GET['untrashed']) > 0) ) $this->type = 'trashRestored';
	}


	/**
	* Set the confirmation message
	*/
	private function setMessage()
	{
		if ( $this->type ){
			$type = $this->type;
			$this->$type();
		}
	}


	/**
	* Trash Confirmation
	*/
	private function trashConfirm()
	{
		$out = '<div id="message" class="updated below-h2"><p>';
		$trashed = ( explode(',', $_GET['ids']) );
		if ( count($trashed) > 1 ){
			$out .= count($trashed) . ' ' . __('pages moved to the Trash', 'nestedpages');
		} else {
			$out .= '<strong>' . get_the_title($trashed[0]) . ' </strong>' . __('page moved to the Trash', 'nestedpages');

			// Undo Link
			if ( current_user_can('delete_pages') ) {
				$page_obj = get_post_type_object('page');
				$out .= ' <a href="' . wp_nonce_url( admin_url( sprintf( $page_obj->_edit_link . '&amp;action=untrash', $trashed[0] ) ), 'untrash-post_' . $trashed[0] ) . '">' . __( 'Undo' ) . "</a>";
			}
		}

		$out .= '</p></div>';
		$this->message = $out;
	}


	/**
	* Trash Restored (Posts moved out of trash)
	*/
	private function trashRestored()
	{
		$out = "";
		$untrashed = sanitize_text_field($_GET['untrashed']);
		$page = ( intval($untrashed) > 1 ) ? __('pages', 'nestedpages') : __('page', 'nestedpages');
		$this->message = '<div id="message" class="updated below-h2"><p>' . $untrashed . ' ' . $page . ' ' . __('restored from trash', 'nestedpages') . '.</p></div>';
	}


	/**
	* Get the Message
	*/
	public function getMessage()
	{
		return $this->message;
	}

}
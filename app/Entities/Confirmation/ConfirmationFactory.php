<?php
namespace NestedPages\Entities\Confirmation;

/**
* Confirmation Message
* @since 1.2
*/
class ConfirmationFactory 
{
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
		$this->build();
	}

	/**
	* Set the Type of confirmation
	*/
	private function build()
	{
		if ( (isset($_GET['trashed'])) && (intval($_GET['trashed']) > 0) ) $this->type = 'TrashConfirmation';
		if ( (isset($_GET['untrashed'])) && (intval($_GET['untrashed']) > 0) ) $this->type = 'TrashRestoredConfirmation';
		if ( (isset($_GET['linkdeleted'])) && (intval($_GET['linkdeleted']) > 0 ) ) $this->type = 'LinkDeletedConfirmation';
		if ( $this->type ) $this->createClass();
	}

	/**
	* Set the confirmation message
	*/
	private function createClass()
	{
		$class = 'NestedPages\Entities\Confirmation\\' . $this->type;
		$confirm = new $class;
		$this->message = $confirm->setMessage();
	}

	/**
	* Get the Message
	*/
	public function getMessage()
	{
		return $this->message;
	}
}
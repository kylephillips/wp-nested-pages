<?php namespace NestedPages\Form;

/**
* Registers the WP Actions/Handlers
*/
class FormActionFactory {

	/**
	* Actions
	* @var array
	*/
	private $actions;

	/**
	* Handler Class
	* @var stdClass obj
	*/
	private $handlers;


	public function __construct()
	{
		$this->setActions();
	}

	/**
	* Set the Form Actions
	*/
	public function setActions()
	{
		$this->actions = array(
			'wp_ajax_npsort',
			'wp_ajax_npquickEdit',
			'wp_ajax_npsyncMenu',
			'wp_ajax_npnestToggle',
			'wp_ajax_npquickEditLink',
			'wp_ajax_npnewLink',
			'wp_ajax_npgetTaxonomies',
			'wp_ajax_npnewChild',
			'admin_post_npListingSort',
			'wp_ajax_npEmptyTrash',
			'admin_post_npSearch'
		);
		$this->setHandlers();
	}

	/**
	* Set the Handlers Object
	*/
	public function setHandlers()
	{
		foreach($this->actions as $key => $action){
			$class = str_replace('admin_post_np', '', $action); // Non-AJAX forms
			$class = ucfirst(str_replace('wp_ajax_np', '', $class)) . 'Handler'; // AJAX forms
			$this->handlers[$key] = new \stdClass();
			$this->handlers[$key]->action = $action;
			$this->handlers[$key]->class = 'NestedPages\Form\Handlers\\' . $class;
		}
		$this->build();
	}

	/**
	* Register the WP Actions
	*/
	public function build()
	{
		foreach($this->handlers as $handler){
			add_action($handler->action, function() use ($handler) {
				new $handler->class;
			});
		}
	}

}
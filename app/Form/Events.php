<?php 
namespace NestedPages\Form;

/**
* Registers the WP Actions/Handlers
*/
class Events 
{
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
		$this->registerEvents();
	}

	/**
	* Set the Form Actions
	*/
	public function registerEvents()
	{
		$this->actions = [
			'wp_ajax_npsort',
			'wp_ajax_npquickEdit',
			'wp_ajax_npsyncMenu',
			'wp_ajax_npnestToggle',
			'wp_ajax_npquickEditLink',
			'wp_ajax_npgetTaxonomies',
			'wp_ajax_npnewChild',
			'wp_ajax_npnewBeforeAfter',
			'admin_post_npListingSort',
			'wp_ajax_npEmptyTrash',
			'admin_post_npSearch',
			'wp_ajax_npclonePost',
			'wp_ajax_npmenuSearch',
			'wp_ajax_npnewMenuItem',
			'admin_post_npCategoryFilter',
			'admin_post_npBulkActions',
			'wp_ajax_npmanualMenuSync',
			'admin_post_npBulkEdit',
			'wp_ajax_nptrashWithChildren',
			'wp_ajax_nppostSearch',
			'wp_ajax_npWpmlTranslations',
			'wp_ajax_npresetSettings',
			'wp_ajax_npresetUserPreferences',
			'wp_ajax_npresetAdminMenuSettings'
		];
		$this->setHandlers();
	}

	/**
	* Set the Handlers Object
	*/
	public function setHandlers()
	{
		foreach($this->actions as $key => $action){
			$class = str_replace('admin_post_np', '', $action); // Non-AJAX forms
			$class = ucfirst(str_replace('wp_ajax_np', '', $class)); // AJAX forms
			$this->handlers[$key] = new \stdClass();
			$this->handlers[$key]->action = $action;
			$this->handlers[$key]->class = 'NestedPages\Form\Listeners\\' . $class;
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
<?php namespace NestedPages\Entities\NavMenu;

/**
* Factory class for instantiating a menu synchronnization
* @since 1.3.4
*/
class NavMenuSyncFactory {

	/**
	* Type of Sync
	* @var string
	*/
	private $type;

	public function __construct($type)
	{
		$this->type = $type;
	}

	/**
	* Run the sync method on the class
	*/
	public function sync()
	{
		$classname = 'NestedPages\Entities\NavMenu\NavMenuSync' . ucfirst($this->type);
		$class = new $classname;
		$class->sync();
	}

}
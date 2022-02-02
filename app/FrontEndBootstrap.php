<?php
namespace NestedPages;

class FrontEndBootstrap
{
	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		new Entities\NavMenu\NavMenuFrontEnd;
	}
}
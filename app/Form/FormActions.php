<?php namespace NestedPages\Form;

/**
* Registers the WP Actions/Handlers
*/
class FormActions {

	public function __construct()
	{
		if ( is_admin() ) {
			add_action( 'wp_ajax_npsort', array($this, 'sort') );
			add_action( 'wp_ajax_npquickEdit', array($this, 'quickEdit') );
			add_action( 'wp_ajax_npsyncMenu', array($this, 'syncMenu') );
			add_action( 'wp_ajax_npnestToggle', array($this, 'nestToggle') );
			add_action( 'wp_ajax_npquickEditRedirect', array($this, 'quickEditRedirect') );
			add_action( 'wp_ajax_npnewLink', array($this, 'newLink') );
			add_action( 'wp_ajax_npgetTaxonomies', array($this, 'getTaxonomies') );
			add_action( 'wp_ajax_npnewChild', array($this, 'newChild') );
		}
	}

	public function sort()
	{
		new Handlers\SortHandler;
	}

	public function quickEdit()
	{
		new Handlers\QuickEditHandler;
	}

	public function syncMenu()
	{
		new Handlers\SyncMenuHandler;
	}

	public function nestToggle()
	{
		new Handlers\NestToggleHandler;
	}

	public function quickEditRedirect()
	{
		new Handlers\QuickEditRedirectHandler;
	}

	public function newLink()
	{
		new Handlers\NewLinkHandler;
	}

	public function getTaxonomies()
	{
		new Handlers\GetTaxonomiesHandler;
	}

	public function newChild()
	{
		new Handlers\NewChildHandler;
	}

}
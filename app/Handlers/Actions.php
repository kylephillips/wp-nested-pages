<?php namespace NestedPages\Handlers;

/**
* Registers the WP Actions/Handlers
*/
class Actions {

	public function __construct()
	{
		if ( is_admin() ) {
			add_action( 'wp_ajax_npsort', array($this, 'sort') );
			add_action( 'wp_ajax_npquickedit', array($this, 'quickEdit') );
			add_action( 'wp_ajax_npsyncmenu', array($this, 'syncMenu') );
			add_action( 'wp_ajax_npnesttoggle', array($this, 'nestToggle') );
			add_action( 'wp_ajax_npquickeditredirect', array($this, 'quickEditRedirect') );
			add_action( 'wp_ajax_npnewredirect', array($this, 'newLink') );
			add_action( 'wp_ajax_gettax', array($this, 'getTaxonomies') );
			add_action( 'wp_ajax_npnewchild', array($this, 'newChild') );
		}
	}

	public function sort()
	{
		new SortHandler;
	}

	public function quickEdit()
	{
		new QuickEditHandler;
	}

	public function syncMenu()
	{
		new SyncMenuHandler;
	}

	public function nestToggle()
	{
		new NestToggleHandler;
	}

	public function quickEditRedirect()
	{
		new QuickEditRedirectHandler;
	}

	public function newLink()
	{
		new NewLinkHandler;
	}

	public function getTaxonomies()
	{
		new GetTaxonomiesHandler;
	}

	public function newChild()
	{
		new NewChildHandler;
	}

}
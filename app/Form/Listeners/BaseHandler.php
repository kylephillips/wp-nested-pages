<?php 
namespace NestedPages\Form\Listeners;

use NestedPages\Entities\NavMenu\NavMenuSyncListing;
use NestedPages\Entities\Post\PostRepository;
use NestedPages\Entities\Post\PostUpdateRepository;
use NestedPages\Entities\User\UserRepository;
use NestedPages\Entities\PluginIntegration\IntegrationFactory;
use NestedPages\Config\SettingsRepository;

/**
* Base Form Handler Class
*/
abstract class BaseHandler 
{
	/**
	* Nonce
	* @var string
	*/
	protected $nonce;

	/**
	* Form Data
	* @var array
	*/
	protected $data;

	/**
	* Post Repo
	* @var object
	*/
	protected $post_repo;

	/**
	* User Repo
	* @var object
	*/
	protected $user;

	/**
	* Post Update Repo
	*/
	protected $post_update_repo;

	/**
	* Response
	* @var array;
	*/
	protected $response;

	/**
	* Plugin Integrations
	* @var object;
	*/
	protected $integrations;

	/**
	* Settings Repo
	* @var object;
	*/
	protected $settings;


	public function __construct()
	{
		$this->post_repo = new PostRepository;
		$this->post_update_repo = new PostUpdateRepository;
		$this->user = new UserRepository;
		$this->integrations = new IntegrationFactory;
		$this->settings = new SettingsRepository;
		$this->setData();
		$this->validateNonce();
	}

	/**
	* Set the Form Data
	*/
	protected function setData()
	{
		$this->nonce = sanitize_text_field($_POST['nonce']);
		$data = array();		
		foreach( $_POST as $key => $value ){
			$data[$key] = $value;
		}
		$this->data = $data;
	}

	/**
	* Validate the Nonce
	*/
	protected function validateNonce()
	{
		if ( ! wp_verify_nonce( $this->nonce, 'nestedpages-nonce' ) ){
			$this->response = array( 'status' => 'error', 'message' => __('Incorrect Form Field', 'wp-nested-pages') );
			$this->sendResponse();
			die();
		}
	}

	/**
	* Sync the Nav Menu
	*/
	protected function syncMenu()
	{
		if ( get_option('nestedpages_menusync') !== 'sync' ) return;
		if ( get_option('nestedpages_disable_menu') == 'true' ) return;
		
		if ( $_POST['post_type'] == 'page' ) {
			if ( $_POST['syncmenu'] !== 'sync' ){
				return update_option('nestedpages_menusync', 'nosync');
			}
			update_option('nestedpages_menusync', 'sync');
			try {
				$menu = new NavMenuSyncListing;
				$menu->sync();
			} catch ( \Exception $e ){
				return $this->exception($e->getMessage());
			}
			return;
		}
	}

	/**
	* Send a Generic Success Message
	*/
	protected function sendErrorResponse()
	{
		$this->response = array(
			'status' => 'error', 
			'message' => __('There was an error updating the page.', 'wp-nested-pages') 
		);
		$this->sendResponse();
	}

	/**
	* Send Error from Exception
	*/
	protected function exception($message)
	{
		return wp_send_json(array(
			'status' => 'error',
			'message' => $message
		));
	}

	/**
	* Return Response
	*/
	protected function sendResponse()
	{
		return wp_send_json($this->response);
	}
}
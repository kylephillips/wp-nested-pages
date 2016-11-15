<?php 

namespace NestedPages\Form\Listeners;

class UpdateFavorites extends BaseHandler
{

	public function __construct()
	{
		parent::__construct();
		$this->updatefavorites();
	}

	private function updatefavorites()
	{
		$postID = intval($_POST['id']);
		$newStatus = $_POST['newStatus'] === 'true'? true: false;
		$favorites = $this->user->getFavoritePages();

		if($newStatus){
			//Get all ancestor IDs. Otherwise, only pages whose ancestors up to the root level are marked as favorites will show up.

			$ancestors = get_ancestors($postID, get_post_type($postID));
			if($ancestors==null)
				$ancestors = array();
			array_push($ancestors, $postID);
			$favorites = array_merge($favorites, $ancestors);

			//Get children as well
			$children = get_pages( array(
				'child_of' => $postID,
				'post_type' => get_post_type($postID)
			));
			if($children==null)
				$children = array();
			$childrenIDs = array();
			foreach ($children as $ch)
				array_push($childrenIDs, $ch->ID);

			$favorites = array_merge($favorites, $childrenIDs);
		}
		else if(in_array($postID, $favorites)){
			//Remove both postID as well as any children, for the same reasons as above.

			$children = get_pages( array(
				'child_of' => $postID,
				'post_type' => get_post_type($postID)
			));
			if($children==null)
				$children = array();
			$childrenIDs = array();
			foreach ($children as $ch)
				array_push($childrenIDs, $ch->ID);
			array_push($childrenIDs, $postID);
			$favorites = array_diff($favorites, $childrenIDs);
		}

		$this->user->updateFavoritePages($favorites);

		return wp_send_json(array(
			'status'=>'success',
			'message'=> __(count($this->user->getFavoritePages()))
		));
	}

}
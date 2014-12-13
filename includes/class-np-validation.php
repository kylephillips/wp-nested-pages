<?php

/**
* Nested Pages Form Validation
*/
class NP_Validation {


	/**
	* Validate Post IDs in an array of posts
	*/
	public function validatePostIDs($posts)
	{
		foreach ($posts as $post)
		{
			if ( !is_numeric($post['id']) ){
				return wp_send_json(array('status'=>'error', 'message'=>'Incorrect Form Field'));
			}
		}
	}

	/**
	* Validate IDs in an array (tax ids)
	*/
	public function validateIntegerArray($items)
	{
		foreach ( $items as $item )
		{
			if ( !is_numeric($item) ){
				return wp_send_json(array('status'=>'error', 'message'=>'Incorrect Form Field'));
			}
		}
	}


	/**
	* Validate Date Input
	*/
	public function validateDate($data)
	{
		// First validate that it is an actual date
		if ( !wp_checkdate( 
				intval($data['mm']), 
				intval($data['jj']), 
				intval($data['aa']),
				$data['aa'] . '-' . $data['mm'] . '-' . $data['jj']
				)
			){
			return wp_send_json(array('status' => 'error', 'message' => __('Please provide a valid date.', 'nestedpages') ));
			die();
		}

		// Validate all the fields are there
		if ( ($data['aa'] !== "") 
			&& ( $data['mm'] !== "" )
			&& ( $data['jj'] !== "" )
			&& ( $data['hh'] !== "" )
			&& ( $data['mm'] !== "" )
			&& ( $data['ss'] !== "" ) )
		{
			$date = strtotime($data['aa'] . '-' . $data['mm'] . '-' . $data['jj'] . ' ' . $data['hh'] . ':' . $data['mm'] . ':' . $data['ss']);
			return date('Y-m-d H:i:s', $date);
		} else {
			return wp_send_json(array('status' => 'error', 'message' => __('Please provide a valid date.', 'nestedpages') ));
			die();
		}
	}


	/**
	* Validate new redirect/link fields
	*/
	public function validateRedirect($data)
	{
		if ( (!isset($data['np_link_title'])) || ($data['np_link_title'] == "") ){
			return wp_send_json(array('status' => 'error', 'message' => __('Please provide a menu title.', 'nestedpages') ));
		}
		if ( (!isset($data['np_link_content'])) || ($data['np_link_content'] == "") ){
			return wp_send_json(array('status' => 'error', 'message' => __('Please provide a valid URL.', 'nestedpages') ));
		}
	}


	/**
	* Validate a string isn't empty
	*/
	public function checkEmpty($var, $title)
	{
		if ( $var == "" ){
			$message = __('Please provide a ', 'nestedpages') . $title;
			return wp_send_json(array('status' => 'error', 'message' => $message));
			die();
		}
	}


	/**
	* Validate New Pages
	*/
	public function validateNewPages($data)
	{
		// Check for Parent ID
		if ( (!isset($data['parent_id'])) || (!is_numeric($data['parent_id'])) ){
			$message = __('A valid parent page was not provided.', 'nestedpages');
			return wp_send_json(array('status' => 'error', 'message' => $message));
			die();
		}

		// Make sure there's at least one page
		if ( !isset($data['post_title']) ){
			$message = __('Please provide at least one page title.', 'nestedpages');
			return wp_send_json(array('status' => 'error', 'message' => $message));
			die();
		}

		// Page fields cannot be blank
		foreach ( $data['post_title'] as $title ){
			if ( $title == "" ){
				$message = __('Page titles cannot be blank.', 'nestedpages');
				return wp_send_json(array('status' => 'error', 'message' => $message));
				die();
			}
		}

		return true;
	}


}
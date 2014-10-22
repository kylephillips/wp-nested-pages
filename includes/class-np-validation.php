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


}
<?php

class NP_PostRepository {

	/**
	* Update Order
	*/
	public function updateOrder($posts, $parent = 0)
	{
		$this->validateIDs($posts);
		foreach( $posts as $key => $post )
		{
			wp_update_post(array(
				'ID' => $post['id'],
				'menu_order' => $key,
				'post_parent' => $parent
			));

			if ( isset($post['children']) ){
				$this->updateOrder($post['children'], $post['id']);
			}
		}
		return true;
	}


	/**
	* Validate Post IDs before saving
	*/
	private function validateIDs($posts)
	{
		foreach ($posts as $post)
		{
			if ( !is_numeric($post['id']) ){
				return wp_send_json(array('status'=>'error', 'message'=>'Incorrect Form Field'));
			}
		}
	}


	/**
	* Update Post
	*/
	public function updatePost($data)
	{
		$date = $this->validateDate($data);
		
		$updated_post = array(
			'ID' => sanitize_text_field($data['post_id']),
			'post_title' => sanitize_text_field($data['post_title']),
			'post_author' => sanitize_text_field($data['post_author']),
			'post_name' => sanitize_text_field($data['post_name']),
			'post_date' => $date,
			'comment_status' => sanitize_text_field($data['comment_status']),
			'post_status' => sanitize_text_field($data['_status'])
		);

		$updated = wp_update_post($updated_post);

		$this->updateTemplate($data);
		$this->updateNavStatus($data);
		$this->updateNestedPagesStatus($data);

		return $updated_post;
	}


	/**
	* Update Page Template
	*/
	public function updateTemplate($data)
	{
		update_post_meta( 
			$data['post_id'], 
			'_wp_page_template', 
			sanitize_text_field($data['page_template'])
		);
	}


	/**
	* Update Nav Status (show/hide in nav menu)
	*/
	private function updateNavStatus($data)
	{
		$status = ( isset($data['nav_status']) ) ? 'hide' : 'show';
		update_post_meta( 
			$data['post_id'], 
			'np_nav_status', 
			$status
		);
	}


	/**
	* Update Nested Pages Visibility (how/hide in Nested Pages interface)
	*/
	private function updateNestedPagesStatus($data)
	{
		$status = ( isset($data['nested_pages_status']) ) ? 'hide' : 'show';
		update_post_meta( 
			$data['post_id'], 
			'nested_pages_status', 
			$status
		);
	}


	/**
	* Validate Date Input
	*/
	private function validateDate($data)
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
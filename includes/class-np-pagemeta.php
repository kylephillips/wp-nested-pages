<?php

class NP_PageMeta {

	function __construct()
	{
		add_action( 'add_meta_boxes', array( $this, 'metaBox' ));
		add_action( 'save_post', array($this, 'saveMeta' ));
	}


	/**
	* Register the Meta Box
	*/
	public function metaBox() 
	{
    	add_meta_box( 
    		'nestedpages', 
    		'Nested Pages', 
    		array($this, 'metaFields'), 
    		'page', 
    		'side', 
    		'low' 
    	);
	}


	/**
	* Meta Boxes for Output
	*/
	public function metaFields($post)
	{
		// Set field values
	    wp_nonce_field( 'nestedpages_meta_box_nonce', 'np_meta_box_nonce' ); 
	    $np_nav_status = get_post_meta($post->ID, 'np_nav_status', true);
		$np_nav_title = get_post_meta($post->ID, 'np_nav_title', true);
		$nested_pages_status = get_post_meta($post->ID, 'nested_pages_status', true);
	    ?>
	    <div class="netsedpages-meta">
			<p class="full">
				<label for="np_nav_status">
					<input type="checkbox" name="np_nav_status" id="np_nav_status" <?php if ( $np_nav_status == 'hide' ) echo 'checked'; ?>>
					<?php _e('Hide in Nav Menu', 'nestedpages'); ?>
				</label>
			</p>
			<p>
				<label for="np_status">
					<input type="checkbox" name="nested_pages_status" id="np_status" <?php if ( $nested_pages_status == 'hide' ) echo 'checked'; ?>>
					<?php _e('Hide in Nested Pages Tree', 'nestedpages'); ?>
				</label>
			</p>
			<p>
				<label for="np_nav_title"><?php _e('Nav Menu Title', 'nestedpages'); ?></label>
				<input type="text" name="np_nav_title" id="np_nav_title" value="<?php echo $np_nav_title; ?>" />
			</p>
		</div><!-- .nestedpages-meta -->
	    <?php
	}


	/**
	* Save the custom post meta
	*/
	public function saveMeta( $post_id ) 
	{
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		// Verify the nonce & permissions.
		if( !isset( $_POST['np_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['np_meta_box_nonce'], 'nestedpages_meta_box_nonce' ) ) return;
		//if( !current_user_can( 'edit_post' ) ) return;

	    // Save the Nav Status
		if( isset( $_POST['np_nav_status'] ) ) {
			update_post_meta( $post_id, 'np_nav_status', 'hide' );
		} else {
			update_post_meta( $post_id, 'np_nav_status', 'show' );
		}

	    // Save the Nav Title
		if( isset( $_POST['np_nav_title'] ) )
			update_post_meta( $post_id, 'np_nav_title', esc_attr( $_POST['np_nav_title'] ) );

		// Save the NP Status
		if ( isset( $_POST['nested_pages_status'] ) ){
			update_post_meta( $post_id, 'nested_pages_status', 'hide' );
		} else {
			update_post_meta( $post_id, 'nested_pages_status', 'show' );
		}

	} 

}
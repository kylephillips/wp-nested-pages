<?php
/**
* Custom Columns
*/
if ( $columns ) :
	$i = 1;
	echo '</div><!-- .cell.title -->';
	
	foreach ( $columns as $column => $label ) :
		echo '<div class="cell custom-column" style="width:' . $column_width . '%;';
		if ( $i == count($columns) && $this->integrations->plugins->yoast->installed ) echo 'padding-right:24px;';
		echo '">';

		$is_tax = ( substr($column, 0, 11) == 'np_taxonomy' ) ? true : false;

		if ( $is_tax ) echo $this->post_type_repo->getTaxonomiesList($this->post->id, substr($column, 12));

		if ( !$is_tax ) :

			if ( $this->post_type->name == 'page' ) :
				do_action( 'manage_pages_custom_column', $column, $this->post->id, $this->post );
			elseif ( $this->post_type->name == 'post' ) :
				do_action( 'manage_posts_custom_column', $column, $this->post->id, $this->post );
			else :
				do_action( "manage_{$this->post_type->name}_posts_custom_column", $column, $this->post->id, $this->post );
			endif;

		endif;
		
		echo '</div><!-- .cell -->';
		$i++;
	endforeach;

endif;
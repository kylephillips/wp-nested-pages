<?php
$columns = $this->columns;
$width = ( 0.7 / count($columns) ) * 100;
$thumbnail_size = $this->post_type_repo->thumbnails($this->post_type->name, 'display_size', 'pixels');
?>
<div class="nestedpages-columns-header">
	<?php
		if ( $this->post_type->hierarchical ) echo '<div class="cell toggle-spacer"><span></span></div>';
	?>
	<div class="cell header title"><?php echo $this->post_type->labels->singular_name; ?></div>
	<?php
		foreach ( $columns as $key => $label ){
			echo '<div class="cell" style="width:' . $width . '%;">' . $label . '</div>';
		}
		// Spacer for thumbnails
		if ( $thumbnail_size ) :
			echo '<div class="cell thumbnail-spacer" style="width:' . $thumbnail_size . ';"><span style="width:' . $thumbnail_size . ';"></span></div>';
		endif;
	?>
	<div class="cell bulk-spacer" aria-hidden="true"><span></span></div>
</div><!-- .nestedpages-columns-header -->
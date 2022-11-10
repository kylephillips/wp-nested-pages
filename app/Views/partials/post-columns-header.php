<?php
/**
* Display the header cells for the WP_Posts_List_Table
* @see NestedPages\Entities\PostType\PostTypeColumns
*/
if ( !$this->post_list_table ) return;
?>
<div class="np-post-columns-header-wp-spacer"></div>
<table class="np-post-columns-header-wp fixed">
	<thead>
		<tr>
			<?php echo $this->post_list_table->columnHeaders(); ?>
		</tr>
	</thead>
</table>